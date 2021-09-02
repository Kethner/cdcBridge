<?php
namespace Kethner\cdcBridge\classes;

class DataObject
{
    public $data = [];

    function __construct(array $data)
    {
        $this->data = $data;
    }
}
