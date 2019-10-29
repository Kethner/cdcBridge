<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;


// TODO add abstract class for AMO object
class amoContact implements Connector {

    public $connection;
   
    function __construct(amoConnection $connection) {
        $this->connection = $connection;
    }


    public function get($data_object) {
        $response = $this->connection->request(null, 'api/v2/contacts/?id=' . $data_object->data['id']);
        $response = $response['_embedded']['items'][0];
        $data_object->data = self::map($response);
    }

    public function set($data_object) {
    }

    public static function map_response($response) {
        $result['id'] = $response['id'];
        $result['name'] = $response['name'];

        $custom_fields = $response['custom_fields'];
        $custom_fields_ids = array_column($custom_fields, 'id');
        $phone_key = array_search('59483', $custom_fields_ids);
        $email_key = array_search('59485', $custom_fields_ids);

        $result['phone'] = ($phone_key !== false) ? $custom_fields[$phone_key]['values'][0]['value'] : null;
        $result['email'] = ($email_key !== false) ? $custom_fields[$email_key]['values'][0]['value'] : null;
        
        $result['leads'] = ($response['leads']) ? implode(' ', $response['leads']['id']) : null;

        return $result;
    }

}