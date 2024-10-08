<?php
namespace Kethner\cdcBridge\implementations\MySQL;

use Kethner\cdcBridge\interfaces\Connector;

class sqlTableRows implements Connector
{
    public $connection;
    public $table_name;
    public $get_field;

    // TODO add map? limit/offset with get?
    // TODO display errors
    function __construct(sqlConnection $connection, $table_name, $get_field = 'id')
    {
        $this->connection = $connection;
        $this->table_name = $table_name;
        $this->get_field = $get_field;
    }

    public function get($data_object)
    {
        $data = &$data_object->data;

        if (empty($data)) {
            $query = "SELECT * FROM {$this->table_name}";
            $result = $this->connection->request($query);
            while ($item = $result->fetch()) {
                $data[] = $item;
            }
            return;
        }

        foreach ($data as &$item) {
            $query = "SELECT * FROM {$this->table_name} WHERE {$this->get_field} = {$item[$this->get_field]}";
            $result = $this->connection->request($query);
            $item = $result->fetch();
        }
    }

    public function set($data_object)
    {
        $queries = [];
        $data = &$data_object->data;
        foreach ($data as &$item) {
            if (is_array($item)) {
                $array_keys = array_keys($item);
                $columns = '(' . implode(', ', $array_keys) . ')';
                $values = '(:' . implode(', :', $array_keys) . ')';
                $update = implode(
                    ', ',
                    array_map(function ($e) {
                        return $e . ' = :' . $e;
                    }, $array_keys),
                );
                $query = "INSERT INTO {$this->table_name} {$columns} VALUES {$values} ON DUPLICATE KEY UPDATE {$update}";

                $key = implode(' ', $array_keys);
                $queries[$key][] = array_values($item);

                // TODO ignore on duplicate key?
                $item['id'] = $this->connection->pdo->lastInsertId(); //wtf is this
            }

            $this->connection->request($query, $item);
        }

        // TODO оптимизация set() для sql коннекторов, данные в таблице медленно обновляются
        // foreach ($queries as $keys => $values_arr) {
        //     $keys = explode(' ', $keys);

        //     $columns = '(' . implode(', ', $keys) . ')';
        //     foreach ($values_arr as $v) {
        //         $values[] = '(' . implode(',', $v) . ')';
        //     }
        //     $values = implode(', ', $values);
        //     $update = implode(
        //         ', ',
        //         array_map(function ($e) {
        //             return $e . ' = new.' . $e;
        //         }, $keys),
        //     );
        //     $query = "INSERT INTO {$this->table_name} {$columns} VALUES {$values} as new ON DUPLICATE KEY UPDATE {$update}";
        //     $this->connection->request($query);
        // }
    }
}
