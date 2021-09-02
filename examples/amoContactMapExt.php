<?php
use Kethner\cdcBridge\implementations\amoCRM\amoContactMap;

class amoContactMapExt extends amoContactMap
{
    public static function mapResponse($response)
    {
        $data = parent::mapResponse($response);

        $custom_fields = $response['custom_fields'];
        $custom_fields_ids = array_column($custom_fields, 'id');
        $phone_key = array_search('59483', $custom_fields_ids);
        $email_key = array_search('59485', $custom_fields_ids);

        $data['phone'] = $phone_key !== false ? $custom_fields[$phone_key]['values'][0]['value'] : null;
        $data['email'] = $email_key !== false ? $custom_fields[$email_key]['values'][0]['value'] : null;

        return $data;
    }
}
