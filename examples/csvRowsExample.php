<?php

require_once('../vendor/autoload.php');

use Kethner\cdcBridge\classes\DataObject;
use Kethner\cdcBridge\implementations\csv\csvConnection;
use Kethner\cdcBridge\implementations\csv\csvRows;
use Kethner\cdcBridge\implementations\csv\csvRowsMap;

$csvFile = new csvConnection(__DIR__ . '/test.csv', false, 1);
// $csvFile = new csvConnection(__DIR__ . '/test.csv', ['id', 'name']);
$csvFile->connect();
$csvRows = new csvRows($csvFile, csvRowsMap::class);

$dataObject = new DataObject([
    ['id' => 1, 'name' => 'test1'],
    ['id' => 2, 'name' => 'test22'],
    ['id' => 3, 'name' => 'test33'],
    ['id' => 4, 'name' => 'test4'],
]);
$csvRows->set($dataObject);

$dataObject = new DataObject([]);
$csvRows->get($dataObject);
print_r($dataObject);
