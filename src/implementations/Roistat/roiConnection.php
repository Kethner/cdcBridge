<?php
namespace Kethner\cdcBridge\implementations\Roistat;

use Kethner\cdcBridge\interfaces\Connection;
use Exception;

class roiConnection implements Connection
{
    const API_URL = 'https://cloud.roistat.com/api/v1/';
    private $project;
    private $key;
    private $auth_query;

    function __construct($project, $key)
    {
        $this->project = $project;
        $this->key = $key;
        $this->auth_query = '?' . http_build_query(['key' => $this->key, 'project' => $this->project]);
    }

    /**
     * Авторизация в Роистат не требуется
     * Api-ключ и id проекта передаются в каждом запросе
     */
    public function connect()
    {
    }

    /**
     * Стандартная форма отправки запроса в Roistat
     *
     *
     * @param type $data
     * @param type $link
     * @return type
     * @throws Exception
     */
    public function request($data, $link = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::API_URL . $link . $this->auth_query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        if ($data !== null) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        }
        $out = curl_exec($curl); // Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Получим HTTP-код ответа сервера
        curl_close($curl); // Завершаем сеанс cURL
        $code = (int) $code;
        $errors = [
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];

        try {
            // Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
            if ($code != 200 && $code != 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
            }
        } catch (Exception $E) {
            die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
        }
        return json_decode($out, true);
    }
}
