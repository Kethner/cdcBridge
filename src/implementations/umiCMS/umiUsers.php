<?php
namespace Kethner\cdcBridge\implementations\umiCMS;

use Kethner\cdcBridge\interfaces\Connector;
use selector;
use umiObjectsCollection;
use umiObjectTypesCollection;

class umiUsers implements Connector {

    public $map;
    public $get_field;

    function __construct($map, $get_field = 'id') {
        $this->map = $map;
        $this->get_field = $get_field;
    }


    // TODO make commong umiObjects class instead and take umi method and name, i.e. (users, user) as arg
    public function get($data_object) {
        $data = &$data_object->data;
        $selector = new selector('objects');
        $selector->types('object-type')->name('users', 'user');
        $ids = array_column($data, $this->get_field);
        if (!empty($ids)) {
            $selector->where($this->get_field)->equals(array_column($data, $this->get_field));
        }
        if (!empty($data['limit'])) {
            $selector->limit($data['offset'], $data['limit']);
        }
        if (empty($selector->length()) === 0) return false;

        foreach ($selector as $item) {
            $data[] = $this->map::mapResponse($item);
        }
        return true;
    }

    public function set($data_object) {
        $data = &$data_object->data;

        $umiObjects = umiObjectsCollection::getInstance();

        foreach ($data as $item) {
            if (is_array($item)) {
                if(!empty($item['id'])) {
                    $object = $umiObjects->getObject($item['id']);
                } else {
                    $type_id = umiObjectTypesCollection::getInstance()->getTypeIdByHierarchyTypeName('users', 'user');
                    $object = $umiObjects->addObject($item['name'], $type_id);
                }

                if (!$object instanceof umiObject) { continue; }

                $request = $this->map::mapRequest($item);
                foreach ($request as $prop_name => $prop_value) {
                    $object->setValue($prop_name, $prop_value);
                }
            }
        }
    }

}