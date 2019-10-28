<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;
use Kethner\cdcBridge\implementations\amoCRM\amoContact;


class amoContacts implements Connector {
    
    private $connection;

    function __construct(amoConnection $connection) {
        $this->connection = $connection;
    }


    public function get($data_object) {
        $data = &$data_object->data;
        $response = $this->connection->request(null, 'api/v2/contacts/?limit_rows=' . $data['limit'] . '&limit_offset=' . $data['offset']);
        if (empty($response)) return false;

        $response = $response['_embedded']['items'];
        foreach ($response as $item) {
            $data[] = amoContact::map($item);
        }

        return true;
    }

    public function set($data_objects) {
    }

}