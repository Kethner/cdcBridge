<?php
namespace Kethner\cdcBridge\implementations\Roistat;

use Kethner\cdcBridge\interfaces\Map;

class roiVisitMap implements Map
{
    public static function mapResponse($response)
    {
        $data['id'] = $response['id'];
        $data['date'] = $response['date'];
        $data['google_client_id'] = $response['google_client_id'];
        $data['metrika_client_id'] = $response['metrika_client_id'];
        $data['utm_source'] = $response['source']['utm_source'];
        $data['utm_medium'] = $response['source']['utm_medium'];
        return $data;
    }

    public static function mapRequest($data)
    {
    }
}
