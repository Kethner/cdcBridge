<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;

class amoNote implements Connector
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
    }

    public function set($data_object)
    {
        $data = &$data_object->data;

        $payload[] = $this->map::mapRequest($data);
        if (empty($data['id'])) {
            $request['add'] = $payload;
        } else {
            $request['update'] = $payload;
        }
        $response = $this->connection->request($request, 'api/v2/notes/');
        $response = $response['_embedded']['items'][0];

        $data['id'] = $response['id'];
    }
}
