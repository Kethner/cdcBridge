<?php
use Kethner\cdcBridge\implementations\amoCRM\amoLeadMap;
use Kethner\cdcBridge\implementations\amoCRM\amoHelper;


class amoLeadMapExt extends amoLeadMap {

    public static function mapResponse($response) {
        $data = parent::mapResponse($response);

        $custom_fields = $response['custom_fields'];
        $roistat_key = array_search('657527', array_column($custom_fields, 'id'));
        $data['roistat_id'] = ($roistat_key !== false) ? $custom_fields[$roistat_key]['values'][0]['value'] : null;

        return $data;
    }

    public static function mapRequest($data) {
        $request = parent::mapRequest($data);
        $request['custom_fields'][] = amoHelper::addCustomField(657527, $data['roistat_id']);
        return $request;
    }

}