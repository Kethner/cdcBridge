<?php
namespace Kethner\cdcBridge\implementations\amoCRM;


class amoHelper {

    static function addCustomField($id, $value, $name=null, $enum=null) {
        $values['value'] = $value;
        if (!empty($enum)) {
            $values['enum'] = $enum;
        }
        $field = array(
                    'id' => $id,
                    'values' => array($values)
                );
        if (!empty($name)) {
            $field['name'] = $name;
        }
        return $field;
    }

}