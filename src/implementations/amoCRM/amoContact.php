<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;


// TODO add abstract class for AMO object
// TODO better way to extend implementations for specific project (map, custom_fields ids etc.)
class amoContact implements Connector {

    public $connection;
    public $get_field;
    public $map;

    function __construct(amoConnection $connection, $map, $get_field = 'id') {
        $this->connection = $connection;
        $this->map = $map;
        $this->get_field = $get_field;
    }


    public function get($data_object) {
        $response = $this->connection->request(null, 'api/v2/contacts/?id=' . $data_object->data['id']);
        $response = $response['_embedded']['items'][0];
        $data_object->data = $this->map::mapResponse($response);
    }

    public function set($data_object) {
    }

}