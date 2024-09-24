<?php
namespace Kethner\cdcBridge\implementations\amoCRM;

class amoHelper
{
    static function addCustomField($id, $data, $name = null, $enum = null, $val_key = 'value')
    {
        if (!is_array($data)) {
            $data = [$data];
        }
        foreach ($data as $e) {
            $value[$val_key] = $e;
            if (!empty($enum)) {
                $value['enum_code'] = $enum;
            }
            $values[] = $value;
        }
        $field = [
            'field_id' => $id,
            'values' => $values,
        ];
        if (!empty($name)) {
            $field['name'] = $name;
        }
        return $field;
    }
}
