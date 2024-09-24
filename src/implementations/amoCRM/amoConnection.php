<?php

namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connection;
use Exception;

class amoConnection implements Connection
{
    private $api_url;
    private $token;
    private $refresh_token;
    private $code;
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    function __construct($api_url)
    {
        $this->api_url = $api_url;
    }

    public static function withTokens($api_url, $token, $refresh_token, $client_id, $client_secret, $redirect_uri)
    {
        $instance = new self($api_url);
        $instance->token = $token;
        $instance->refresh_token = $refresh_token;
        $instance->client_id = $client_id;
        $instance->client_secret = $client_secret;
        $instance->redirect_uri = $redirect_uri;
        return $instance;
    }

    public static function withAccessToken($api_url, $token)
    {
        $instance = new self($api_url);
        $instance->token = $token;
        return $instance;
    }

    public static function withRefreshToken($api_url, $refresh_token, $client_id, $client_secret, $redirect_uri)
    {
        $instance = new self($api_url);
        $instance->refresh_token = $refresh_token;
        $instance->client_id = $client_id;
        $instance->client_secret = $client_secret;
        $instance->redirect_uri = $redirect_uri;
        return $instance;
    }

    public static function withCode($api_url, $code, $client_id, $client_secret, $redirect_uri)
    {
        $instance = new self($api_url);
        $instance->code = $code;
        $instance->client_id = $client_id;
        $instance->client_secret = $client_secret;
        $instance->redirect_uri = $redirect_uri;
        return $instance;
    }

    /**
     * Проверка/получение авторизационного токена
     *
     * Проверяет токен, пытается обновить, если срок действия токена истек или получает токен по авторизационному коду.
     *
     * @throws Exception
     */
    public function connect()
    {
        if ($this->token) {
            try {
                $this->request(null, 'api/v4/account', 'GET');
                return false;
            } catch (\Throwable $th) {
                return $this->refreshToken();
            }
        }
        if ($this->refresh_token) {
            return $this->refreshToken();
        }
        if ($this->code) {
            return $this->getTokenByCode();
        } else {
            throw new Exception('Авторизоваться в АМО не удалось. Проверьте правильно ли заданы все параметры.');
        }
    }

    /**
     * Получение авторизационного токена по коду
     *
     * @return array
     */
    public function getTokenByCode()
    {
        $response = $this->request([
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'authorization_code',
            'code' => $this->code,
            'redirect_uri' => $this->redirect_uri,
        ]);
        $this->token = $response['access_token'];
        $this->refresh_token = $response['refresh_token'];
        return $response;
    }
    /**
     * Обновление авторизационного токена
     *
     * @return array
     */
    public function refreshToken()
    {
        if (!$this->refresh_token) {
            throw new Exception('Не задан refresh token');
        }
        $response = $this->request([
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refresh_token,
            'redirect_uri' => $this->redirect_uri,
        ]);
        $this->token = $response['access_token'];
        $this->refresh_token = $response['refresh_token'];
        return $response;
    }

    /**
     * Стандартная форма отправки запроса в AMO
     *
     * @param type $data
     * @param type $link
     * @return type
     * @throws Exception
     */
    public function request($data = null, $link = 'oauth2/access_token', $method = 'POST')
    {
        $curl = curl_init();
        $url = $this->api_url . $link;
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
        ]);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if ($data !== null) {
            switch ($method) {
                case 'GET':
                    $url .= '?' . http_build_query($data);
                    break;
                case 'POST':
                default:
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);

        $out = json_decode(curl_exec($curl), true);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

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

        if ($code != 200 && $code != 204) {
            $error_message = isset($errors[$code]) ? "{$code}: {$errors[$code]}" : "{$code}: Undescribed error";
            $error_message .= PHP_EOL . "{$out['title']}: {$out['detail']}";
            if (isset($out['hint'])) {
                $error_message .= PHP_EOL . $out['hint'];
            }
            throw new Exception($error_message);
        }
        return $out;
    }
}
