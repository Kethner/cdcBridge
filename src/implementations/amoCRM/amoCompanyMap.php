<?php

namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Map;

class amoCompanyMap implements Map
{
    public static function mapResponse($response)
    {
        $data['id'] = $response['id'];
        $data['name'] = $response['name'];
        $data['leads'] = $response['_embedded']['leads']
            ? implode(' ', array_column($response['_embedded']['leads'], 'id'))
            : null;
        $data['contacts'] = $response['_embedded']['contacts']
            ? implode(' ', array_column($response['_embedded']['contacts'], 'id'))
            : null;
        return $data;
    }

    public static function mapRequest($data)
    {
    }
}
