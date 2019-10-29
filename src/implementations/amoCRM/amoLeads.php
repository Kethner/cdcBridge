<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;
use Kethner\cdcBridge\implementations\amoCRM\amoLead;


class amoLeads implements Connector {

    public $connection;

    function __construct(amoConnection $connection) {
        $this->connection = $connection;
    }


    public function get($data_object) {
        $data = &$data_object->data;
        $response = $this->connection->request(null, 'api/v2/leads/?limit_rows=' . $data['limit'] . '&limit_offset=' . $data['offset']);
        if (empty($response)) return false;

        $response = $response['_embedded']['items'];
        foreach ($response as $item) {
            $data[] = amoLead::map_response($item);
        }

        return true;
    }

    public function set($data_object) {
        $data = $data_object->data;
        
        foreach (array_chunk($data, 250) as $chunk) {
            $payload = [];
            foreach ($chunk as &$item) {
                if (empty($item['roistat_id_init']) && !empty($item['roistat_id'])) {
                    $payload[] = amoLead::map_request($item);
                }
            }
            $request['update'] = $payload;

            if (count($payload) > 0) {
                $response = $this->connection->request($request, 'api/v2/leads/');
            }
        };
    }

}