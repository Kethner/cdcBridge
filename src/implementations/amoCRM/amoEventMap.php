<?php

namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Map;

class amoEventMap implements Map
{
    public static function mapResponse($response)
    {
        $result['id'] = $response['id'];
        $result['type'] = $response['type'];
        $result['entity_id'] = $response['entity_id'];
        $result['entity_type'] = $response['entity_type'];
        $result['created_by'] = $response['created_by'];
        $result['created_at'] = $response['created_at'];

        $result['value_after'] = $response['value_after']
            ? $response['value_after'][0]['custom_field_value']['text']
            : null;
        $result['value_before'] = $response['value_before']
            ? $response['value_before'][0]['custom_field_value']['text']
            : null;

        return $result;
    }

    public static function mapRequest($data)
    {
    }
}
