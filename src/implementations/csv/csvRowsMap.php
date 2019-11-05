<?php
namespace Kethner\cdcBridge\implementations\csv;

use Kethner\cdcBridge\interfaces\Map;


class csvRowsMap implements Map {

    public static function mapResponse($response) {
        return $response;
    }

    public static function mapRequest($data) {
        return $data;
    }

}