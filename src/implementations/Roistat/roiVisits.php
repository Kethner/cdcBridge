<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;


class roiVisits implements Connector {

    public $connection;
    public $map;

    function __construct(amoConnection $connection, $map) {
        $this->connection = $connection;
        $this->map = $map;
    }


    public function get($data_object) {
        $data = &$data_object->data;
        $response = $this->connection->request(null, 'api/v2/leads/?limit_rows=' . $data['limit'] . '&limit_offset=' . $data['offset']);
        if (empty($response)) return false;

        $response = $response['_embedded']['items'];
        foreach ($response as $item) {
            $data[] = $this->map::mapResponse($item);
        }

        return true;
    }

    public function set($data_object) {
        $data = $data_object->data;
        
        foreach (array_chunk($data, 250) as $chunk) {
            $payload = [];
            foreach ($chunk as &$item) {
                $payload[] = $this->map::mapRequest($item);
            }
            $request['update'] = $payload;

            if (count($payload) > 0) {
                $this->connection->request($request, 'api/v2/leads/');
            }
        };
    }

}