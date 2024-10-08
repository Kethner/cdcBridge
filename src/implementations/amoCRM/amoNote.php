<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;

class amoNote implements Connector
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
            'entity_type' => null,
        ]
    ) {
        $this->connection = $connection;
        $this->map = $map;
        $this->params = $params;
    }

    public function get($data_object)
    {
    }

    public function set($data_object)
    {
        $data = &$data_object->data;
        foreach (array_chunk($data, 50) as $chunk) {
            $payload = [];

            foreach ($chunk as &$item) {
                if ($mapped_item = $this->map::mapRequest($item)) {
                    $payload[] = $mapped_item;
                }

                if (count($payload) > 0) {
                    echo $payload[0]['id'] . "\n";
                    $this->connection->request($payload, `api/v4/{$this->params['entity_type']}/notes/`);
                    echo "Chunk sent \n";
                    sleep(1);
                }
            }
        }
    }
}
