<?php

namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;

class amoCompanies implements Connector
{
    public $connection;
    public $map;
    public $params;

    function __construct(
        amoConnection $connection,
        $map,
        $params = [
            'page' => 0,
            'limit' => 10,
            'with' => 'leads,contacts',
        ]
    ) {
        $this->connection = $connection;
        $this->map = $map;
        $this->params = $params;
    }

    public function get($data_object)
    {
        $data = &$data_object->data;
        $response = $this->connection->request($this->params, 'api/v4/companies', 'GET');
        if (empty($response)) {
            return false;
        }

        $response = $response['_embedded']['companies'];
        foreach ($response as $item) {
            $data[] = $this->map::mapResponse($item);
        }

        $this->params['page']++;

        return true;
    }

    public function set($data_object)
    {
        $data = $data_object->data;

        foreach (array_chunk($data, 50) as $chunk) {
            $payload = [];
            $links = [];

            foreach ($chunk as &$item) {
                if ($mapped_item = $this->map::mapRequest($item)) {
                    $item_links = $mapped_item['links'] ?? [];
                    foreach ($item_links as $to_entity_type => $to_entity_ids) {
                        $is_main = true;
                        foreach ($to_entity_ids as $to_entity_id) {
                            $link = [
                                'entity_id' => $mapped_item['id'],
                                'to_entity_id' => (int) $to_entity_id,
                                'to_entity_type' => $to_entity_type,
                            ];
                            if ($is_main && $to_entity_type === 'contacts') {
                                $link['metadata']['is_main'] = true;
                            }
                            $is_main = false;

                            $links[] = $link;
                        }
                    }
                    unset($mapped_item['links']);

                    $payload[] = $mapped_item;
                }
            }

            if (count($payload) > 0) {
                echo $payload[0]['id'] . "\n";
                $this->connection->request($payload, 'api/v4/companies');
                echo "Chunk sent \n";
                sleep(1);
            }

            if (count($links) > 0) {
                $this->connection->request($links, 'api/v4/companies/link', 'POST');
                echo "Links updated \n";
                sleep(1);
            }
        }
    }
}
