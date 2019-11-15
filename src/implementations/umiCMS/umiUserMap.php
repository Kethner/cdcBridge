<?php
namespace Kethner\cdcBridge\implementations\umiCMS;

use Kethner\cdcBridge\interfaces\Map;


class umiUserMap implements Map {

    public static function mapResponse($response) {
        $data = [];
        $data['name'] = $response->getName();
        return $data;
    }

    public static function mapRequest($data) {
        return $data;
    }

}