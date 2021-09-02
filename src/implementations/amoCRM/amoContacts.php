<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;

class amoContacts implements Connector
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
            'api/v2/contacts/?limit_rows=' . $data['limit'] . '&limit_offset=' . $data['offset'],
        );
        if (empty($response)) {
            return false;
        }

        $response = $response['_embedded']['items'];
        foreach ($response as $item) {
            $data[] = $this->map::mapResponse($item);
        }

        return true;
    }

    public function set($data_objects)
    {
    }
}
