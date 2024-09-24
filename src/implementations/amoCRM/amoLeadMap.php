<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Map;

class amoLeadMap implements Map
{
    public static function mapResponse($response)
    {
        $data['id'] = $response['id'];
        $data['created_at'] = $response['created_at'];
        $data['name'] = $response['name'];
        $data['pipeline_id'] = $response['pipeline_id'];

        if ($contacts = $response['_embedded']['contacts']) {
            foreach ($contacts as $contact) {
                $data['contacts'][] = $contact['id'];
                if ($contact['is_main']) {
                    $data['contact_id'] = $contact['id'];
                }
            }
            $data['contacts'] = implode(' ', $data['contacts']);
        }

        $data['companies'] = $response['_embedded']['companies']
            ? implode(' ', array_column($response['_embedded']['companies'], 'id'))
            : null;

        return $data;
    }

    public static function mapRequest($data)
    {
        $request['id'] = $data['id'];
        $request['updated_at'] = time();
        return $request;
    }
}
