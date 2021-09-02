<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;

class amoLead implements Connector
{
    public $connection;
    public $get_field;
    public $map;

    function __construct(amoConnection $connection, $map, $get_field = 'id')
    {
        $this->connection = $connection;
        $this->map = $map;
        $this->get_field = $get_field;
    }

    public function get($data_object)
    {
        $response = $this->connection->request(null, 'api/v2/leads/?id=' . $data_object->data['id']);
        $response = $response['_embedded']['items'][0];
        $data_object->data = $this->map::mapResponse($response);
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
        $response = $this->connection->request($request, 'api/v2/leads/');
        $response = $response['_embedded']['items'][0];

        $data['id'] = $response['id'];
    }
}
