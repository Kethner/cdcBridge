<?php

namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;

// TODO parent class for AMO collections
// major (only) difference between implementations is API URI
// TODO also filter and extra get parameters - need better way to customize

class amoEvents implements Connector
{
    public $connection;
    public $map;

    function __construct(amoConnection $connection, $map)
    {
        $this->connection = $connection;
        $this->map = $map;
    }

    public function get($data_object)
    {
        $data = &$data_object->data;
        $response = $this->connection->request(
            null,
            'api/v4/events?limit=' .
                $data['limit'] .
                '&page=' .
                $data['page'] .
                '&filter[type]=custom_field_937811_value_changed',
        );
        if (empty($response)) {
            return false;
        }

        $response = $response['_embedded']['events'];
        foreach ($response as $item) {
            $data[] = $this->map::mapResponse($item);
        }

        return true;
    }

    public function set($data_object)
    {
    }
}
