<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Map;


class amoLead implements Map {

    public static function mapRequest($data) {
        return $data;
    }

}