<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Map;


class amoContactMap implements Map {

    public static function mapResponse($response) {
        $data['id'] = $response['id'];
        $data['name'] = $response['name'];
        $data['leads'] = ($response['leads']) ? implode(' ', $response['leads']['id']) : null;
        return $data;
    }

}