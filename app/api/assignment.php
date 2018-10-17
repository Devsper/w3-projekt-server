<?php

session_start();
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
        $assignment->addRelationshipTables('employee');
        $assignment->relationships[0]->id = $_SESSION['employeeId'];

        $result = $assignment->getEmployeeAssignments();
        
        if(count($result[0]) == 1){
            $hasBeenIncluded = true;
            include_once 'task.php';
        }else{
            echo json_encode($result);
        } 

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