<?php
namespace Kethner\cdcBridge\classes;

use Kethner\cdcBridge\classes\DataObject;
use Kethner\cdcBridge\interfaces\Connector;


class Bridge {

    public $dataObject;
    public $connectorLeft;
    public $connectorRight;

    function __construct(DataObject $dataObject, Connector $connectorLeft = null, Connector $connectorRight = null) {
        $this->dataObject = clone $dataObject;
        $this->connectorLeft = $connectorLeft;
        $this->connectorRight = $connectorRight;
    }


    public function setLeft() {
        $this->connectorLeft->set($this->dataObject);
    }
    
    public function setRight() {
        $this->connectorRight->set($this->dataObject);
    }

    public function getLeft() {
        return $this->connectorLeft->get($this->dataObject);
    }

    public function getRight() {
        return $this->connectorRight->get($this->dataObject);
    }

}