<?php
namespace Kethner\cdcBridge\classes;

class DataObject {

    public $data = [];

    function __construct(Array $data) {
        $this->data = $data;
    }

}