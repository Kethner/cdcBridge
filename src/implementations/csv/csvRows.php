<?php
namespace Kethner\cdcBridge\implementations\csv;

use Kethner\cdcBridge\interfaces\Connector;
use LimitIterator;

class csvRows implements Connector
{
    public $connection;
    public $map;
    public $get_field;

    function __construct(csvConnection $connection, $map, $get_field = 'id')
    {
        $this->connection = $connection;
        $this->map = $map;
        $this->get_field = $get_field;
    }

    public function get($data_object)
    {
        $data = &$data_object->data;
        $file = $this->connection;

        $fileStream = $file->getFileStream();
        if (!empty($data['limit'])) {
            $fileIterator = new LimitIterator($fileStream, $data['offset'], $data['limit']);
        } else {
            $fileIterator = new LimitIterator($fileStream, $file->skip_rows);
        }

        foreach ($fileIterator as $line) {
            if (empty($line)) {
                continue;
            }
            $data[] = array_combine($file->headers, str_getcsv($line, $file->delimiter));
        }
    }

    // TODO for now just append, need to add search and replace functionality
    public function set($data_object)
    {
        $data = &$data_object->data;
        $file = $this->connection;

        $fileStream = $file->getFileStream();
        $fileStream->seek(PHP_INT_MAX);

        foreach ($data as $item) {
            if (is_array($item)) {
                $fileStream->fputcsv(array_values($item), $file->delimiter);
            }
        }
    }
}
