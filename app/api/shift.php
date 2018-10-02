<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../functions.php';
include_once '../objects/shift.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        $database = new Database();
        $db = $database->getConnection();

        $shift = new Shift($db);
        $shiftProp = array_fill_keys(array("id", "registerDate", "startTime", "endTime", "employee_Id"),"");

        get($shift, $shiftProp);
        break;
    case 'POST':
        break;
    case 'PUT':
        break;
    case 'DELETE':
        break;
    default:
        echo 'Default';
}