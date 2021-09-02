<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Map;

class amoLeadMap implements Map
{
    public static function mapResponse($response)
    {
        $data['id'] = $response['id'];
        $data['name'] = $response['name'];
        $data['created_at'] = $response['created_at'];
        $data['contact_id'] = $response['main_contact'] ? $response['main_contact']['id'] : null;
        $data['contacts'] = $response['contacts'] ? implode(' ', $response['contacts']['id']) : null;
        return $data;
    }

    public static function mapRequest($data)
    {
        $request['id'] = $data['id'];
        $request['updated_at'] = time();
        return $request;
    }
}
