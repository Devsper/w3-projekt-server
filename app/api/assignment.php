<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../functions.php';
include_once '../objects/assignment.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        $database = new Database();
        $db = $database->getConnection();

        $assignment = new Assignment($db);
        $assignmentProp = array_fill_keys(array("id", "name", "createdDate"),"");

        get($assignment, $assignmentProp);
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