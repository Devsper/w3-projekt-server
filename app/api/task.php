<?php

// required headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT');
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/task.php';


$method = $_SERVER['REQUEST_METHOD'];

$database = new Database();
$db = $database->getConnection();

$task = new Task($db);

switch($method){
    case 'GET':

        if(!empty($_GET['id'])){
            $task->id = $_GET['id'];
        }

        $task->assignment_Id = $_GET['assignment_Id'];

        $result = $task->read();

        echo json_encode($result);
        break;
    case 'POST':

        $data = json_decode(file_get_contents("php://input"));

        $task->name = $data->name;
        $task->assignment_Id = $data->assignment_Id;

        if($task->create()){
            echo '{ "message": "Task was added." }';
        }
        else{
            echo '{ "message": "Unable to add task." }';
        }
        break;
    case 'PUT':

        $data = json_decode(file_get_contents("php://input"));

        $task->name = $data->name;
        $task->id = $data->id;

        if($task->update()){
            echo '{ "message": "Task was updated." }';
        }
        else{
            echo '{ "message": "Unable to update task." }';
        }
        break;
    case 'DELETE':

        $data = json_decode(file_get_contents("php://input"));

        $task->id = $data->id;

        if($task->delete()){
            echo '{ "message": "Task was deleted." }';
        }
        else{
            echo '{ "message": "Unable to delete task." }';
        }
        break;
    default:
        echo 'Default';
}