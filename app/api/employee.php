<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/employee.php';


$method = $_SERVER['REQUEST_METHOD'];

$database = new Database();
$db = $database->getConnection();

$employee = new Employee($db);

switch($method){
    case 'GET':
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