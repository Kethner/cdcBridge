<?php
namespace Kethner\cdcBridge\implementations\Roistat;

use Kethner\cdcBridge\interfaces\Connector;


class roiVisit implements Connector {

    public $connection;
    public $get_field;
    public $map;

    function __construct(roiConnection $connection, $map, $get_field = 'id') {
        $this->connection = $connection;
        $this->map = $map;
        $this->get_field = $get_field;
    }


    public function get($data_object) {
        $data = &$data_object->data;

        $request = [
            "filters" => [
                [ "id", "=", $data['id'] ]
            ],
            "limit" => 1,
            "offset" => 0
        ];
        $response = $this->connection->request($request, 'project/site/visit/list');
        $response = $response['data'][0];
        $data_object->data = $this->map::mapResponse($response);
    }

    public function set($data_object) {
    }

}