<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/assignment.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        $database = new Database();
        $db = $database->getConnection();

        $assignment = new Assignment($db);
        $assignment->addRelationship('employee');
        $assignment->relationships[0]->id = 1;

        // Uses parameter to fetch single object
        $stmt = $assignment->read();

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