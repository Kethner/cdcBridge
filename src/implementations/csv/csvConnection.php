<?php
namespace Kethner\cdcBridge\implementations\csv;

use Kethner\cdcBridge\interfaces\Connection;
use Exception;
use SplFileObject;


/**
 * @property bool|array $headers
 */
class csvConnection implements Connection {

    public $file_path;
    public $headers;
    public $skip_rows;
    public $delimiter;
    private $file;

    /**
     * CSV file
     *
     * Requires file path
     * If headers arg is 'false' - get header from first line
     * If file does not exist and headers are not provided - raises error
     **/
    function __construct($file_path, $headers = false, $skip_rows = 0, $delimiter = ',') {
        $this->file_path = $file_path;
        $this->headers = $headers;
        $this->skip_rows = $skip_rows;
        $this->delimiter = $delimiter;
    }

    /**
     * Closing file stream on object destruction
     **/
    function __destruct() {
        if ($this->file) {
            $this->file = null;
        }
    }


    /**
     * Opens CSV file stream and sets headers
     **/
    public function connect() {
        $handle = fopen($this->file_path, "r+");
        if ($handle) {
            if ($this->headers === false) {
                $this->headers = fgetcsv($handle, 0, $this->delimiter);
            }
        } else {
            if ($this->headers === false) {
                throw new Exception("Error: Headers are required for new file", 1);
            } else {
                $handle = fopen($this->file_path, "w+");
                fputcsv($handle, $this->headers, $this->delimiter);
            }
        }
        $this->file = new SplFileObject($this->file_path, "r+");
    }

    public function request($query, $data=[]) {
    }

    public function getFileStream() {
        return $this->file;
    }

}
