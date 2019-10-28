<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;
use Kethner\cdcBridge\implementations\amoCRM\amoHelper;


class amoLead implements Connector {

    private $connection;
    private $get_field;

    function __construct(amoConnection $connection, $get_field = 'id') {
        $this->connection = $connection;
        $this->get_field = $get_field;
    }


    public function get($data_object) {
        $response = $this->connection->request(null, 'api/v2/leads/?id=' . $data_object->data['id']);
        $response = $response['_embedded']['items'][0];
        $data_object->data = self::map($response);
    }

    public function set($data_object) {
    }

    public static function map($response) {
        $result['id'] = $response['id'];
        $result['name'] = $response['name'];
        $result['created_at'] = $response['created_at'];

        $custom_fields = $response['custom_fields'];
        $roistat_key = array_search('657527', array_column($custom_fields, 'id'));

        $result['roistat_id'] = ($roistat_key !== false) ? $custom_fields[$roistat_key]['values'][0]['value'] : null;
        $result['contact_id'] = ($response['main_contact']) ? $response['main_contact']['id'] : null;
        $result['contacts'] = ($response['contacts']) ? implode(' ', $response['contacts']['id']) : null;

        return $result;
    }

    public static function map_request($item) {
        $payload['id'] = $item['id'];
        $payload['updated_at'] = time();
        $payload['custom_fields'][] = amoHelper::addCustomField(657527, $item['roistat_id']);
        return $payload;
    }

}