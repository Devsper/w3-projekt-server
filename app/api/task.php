<?php

// required headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT');
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/task.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':

        $database = new Database();
        $db = $database->getConnection();

        $task = new Task($db);

        if($hasBeenIncluded){

            $result = $task->getEmployeeTasks();
            echo json_encode($result);
        }else{

        }

        // $task->addRelationshipTables('employee');
        // $task->relationships[0]->id = $_SESSION['employeeId'];

        $result = $task->read();

        echo json_encode($result);
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