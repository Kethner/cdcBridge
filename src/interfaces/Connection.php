<?php
namespace Kethner\cdcBridge\interfaces;


interface Connection {

    /**
     * Authorization / connection
     */
    public function connect();

    /**
     * Sending request
     */
    public function request($query, $data);

}