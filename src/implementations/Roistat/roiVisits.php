<?php
namespace Kethner\cdcBridge\implementations\Roistat;

use Kethner\cdcBridge\interfaces\Connector;


class roiVisits implements Connector {

    public $connection;
    public $map;

    function __construct(roiConnection $connection, $map) {
        $this->connection = $connection;
        $this->map = $map;
    }


    public function get($data_object) {
        $data = &$data_object->data;

        $request = [
            "limit" => $data['limit'],
            "offset" => $data['offset']
        ];
        $response = $this->connection->request($request, 'project/site/visit/list');
        if (empty($response)) return false;

        $response = $response['data'];
        foreach ($response as $item) {
            $data[] = $this->map::mapResponse($item);
        }

        return true;
    }

    public function set($data_object) {
    }

}