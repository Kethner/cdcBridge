<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;

// TODO add abstract class for AMO object
// TODO better way to extend implementations for specific project (map, custom_fields ids etc.)

class amoContact implements Connector
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
        $response = $this->connection->request(null, 'api/v2/contacts/?id=' . $data_object->data['id']);
        $response = $response['_embedded']['items'][0];
        $data_object->data = $this->map::mapResponse($response);
    }

    public function set($data_object)
    {
        $data = &$data_object->data;

        $payload[] = $this->map::mapRequest($data);
        if (!empty($data['id'])) {
            $request['update'] = $payload;
        } else {
            $request['add'] = $payload;
        }
        $response = $this->connection->request($request, 'api/v2/contacts/');
        $response = $response['_embedded']['items'][0];

        $data['id'] = $response['id'];
    }

    public function find($data_object)
    {
        $data = $data_object->data;
        foreach ($data as $search_value) {
            $response = $this->connection->request(null, 'api/v2/contacts/?query=' . urlencode($search_value));
            $response = $response['_embedded']['items'][0];

            if (is_array($response)) {
                $data_object->data = $this->map::mapResponse($response);
                return true;
            }
        }
        return false;
    }
}
