<?php
namespace Kethner\cdcBridge\classes;

use Kethner\cdcBridge\classes\DataObject;
use Kethner\cdcBridge\interfaces\Connector;

class Bridge
{
    public $dataObject;
    public $connectorLeft;
    public $connectorRight;

    function __construct(Connector $connectorLeft = null, Connector $connectorRight = null)
    {
        // TODO убрать из аргумента dataObject, зачем он вообще нужен? всегда пустой или нет?  зачем клон? по идее нужен референс
        // $this->dataObject = clone $dataObject;

        $this->connectorLeft = $connectorLeft;
        $this->connectorRight = $connectorRight;
        $this->flush();
    }

    public function setDataObject(DataObject $dataObject)
    {
        $this->dataObject = $dataObject;
    }

    public function setLeft()
    {
        $this->connectorLeft->set($this->dataObject);
    }

    public function setRight()
    {
        $this->connectorRight->set($this->dataObject);
    }

    public function getLeft()
    {
        return $this->connectorLeft->get($this->dataObject);
    }

    public function getRight()
    {
        return $this->connectorRight->get($this->dataObject);
    }

    public function flush()
    {
        $this->dataObject = new DataObject([]);
    }
}
