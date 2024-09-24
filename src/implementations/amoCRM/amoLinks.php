<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

use Kethner\cdcBridge\interfaces\Connector;

// TODO можно удалить - привязка делается через класс соотв. сущности в АМО параллельно с обновлением самих объектов

class amoLinks implements Connector
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
            'with' => 'leads',
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
        $data = $data_object->data;

        foreach (array_chunk($data, 50) as $chunk) {
            $payload = [];
            foreach ($chunk as &$item) {
                $payload[] = $this->map::mapRequest($item);
            }

            if (count($payload) > 0) {
                echo $payload[0]['id'] . "\n";
                $this->connection->request($payload, 'api/v4/contacts', 'PATCH');
                echo "Chunk sent \n";
                sleep(1);
            }
        }
    }
}
