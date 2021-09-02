<?php
namespace Kethner\cdcBridge\interfaces;

interface Connector
{
    public function get($dataObject);

    public function set($dataObject);
}
