<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Map;

class amoNoteMap implements Map
{
    public static function mapResponse($response)
    {
    }

    public static function mapRequest($data)
    {
        return $data;
    }
}
