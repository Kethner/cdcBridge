<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connection;
use Exception;

class amoConnection implements Connection
{
    private $api_url;
    private $user_login;
    private $user_hash;
    public $cookie_path;

    function __construct($api_url, $user_login, $user_hash, $cookie_path = false)
    {
        $this->api_url = $api_url;
        $this->user_login = $user_login;
        $this->user_hash = $user_hash;
        if ($cookie_path === false) {
            $cookie_path = getcwd();
        }
        $this->cookie_path = $cookie_path;
    }

    /**
     * Авторизация в AMO. Нужна для каждого запроса.
     * Делается непосредственно перед выполнением запроса и сохраняет данные в директорию для хранения временных данных
     *
     * @return boolean
     */
    public function connect()
    {
        $user = [
            'USER_LOGIN' => $this->user_login, // Ваш логин (электронная почта)
            'USER_HASH' => $this->user_hash, // Хэш для доступа к API (смотрите в профиле пользователя)
        ];
        $response = $this->request($user);
        $response = $response['response'];

        if (isset($response['auth'])) {
            //Флаг авторизации доступен в свойстве "auth"
            return true; // 'Авторизация прошла успешно';
        }
        return false; // 'Авторизация не удалась';
    }

    /**
     * Стандартная форма отправки запроса в AMO
     *
     *
     * @param type $data
     * @param type $link
     * @return type
     * @throws Exception
     */
    public function request($data, $link = 'private/api/auth.php?type=json')
    {
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
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
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
