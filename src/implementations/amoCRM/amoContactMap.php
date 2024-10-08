<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Map;

class amoContactMap implements Map
{
    public static function mapResponse($response)
    {
        $data['id'] = $response['id'];
        $data['name'] = $response['name'];
        $data['created_at'] = $response['created_at'];
        $data['leads'] = $response['_embedded']['leads']
            ? implode(' ', array_column($response['_embedded']['leads'], 'id'))
            : null;
        $data['companies'] = $response['_embedded']['companies']
            ? implode(' ', array_column($response['_embedded']['companies'], 'id'))
            : null;
        return $data;
    }

    public static function mapRequest($data)
    {
        if (!empty($data['id'])) {
            $request['id'] = $data['id'];
        }
        if (!empty($data['name'])) {
            $request['name'] = $data['name'];
        }
        $request['updated_at'] = time();
        return $request;
    }
}
