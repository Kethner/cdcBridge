<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connection;
use Exception;

class roiConnection implements Connection {

    private $api_url;
    private $project;
    private $key;

    function __construct($api_url, $project, $key) {
        $this->api_url = $api_url;
        $this->project = $project;
        $this->key = $key;
    }


    /**
     * Авторизация в Роистат не требуется
     * Api-ключ и id проекта передаются в каждом запросе
     */
    public function connect() {
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
    public function request($data, $link = 'private/api/auth.php?type=json') {
        $api_url = $this->api_url;

        /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP. */
        $curl = curl_init(); // Сохраняем дескриптор сеанса cURL
        // Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $api_url . $link);
        if ($data !== null) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }
        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_path . '/amo_cookie');
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_path . '/amo_cookie');
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = curl_exec($curl); // Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Получим HTTP-код ответа сервера
        curl_close($curl); // Завершаем сеанс cURL
        /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int) $code;
        $errors = array(
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable'
        );

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