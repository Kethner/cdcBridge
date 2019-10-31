<?php
use Kethner\cdcBridge\implementations\amoCRM\amoLeads;


class amoLeadsExt extends amoLeads {

    public function set($data_object) {
        $data = $data_object->data;
        
        foreach (array_chunk($data, 250) as $chunk) {
            $payload = [];
            foreach ($chunk as &$item) {
                if (empty($item['roistat_id_init']) && !empty($item['roistat_id'])) {
                    $payload[] = $this->map::mapRequest($item);
                }
            }
            $request['update'] = $payload;

            if (count($payload) > 0) {
                $this->connection->request($request, 'api/v2/leads/');
            }
        };
    }

}