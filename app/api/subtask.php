<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('../config/database.php');
require_once('../objects/subtask.php');
require_once('../helpers/jwt_helper.php');
require_once('../objects/authentication.php');


$method = $_SERVER['REQUEST_METHOD'];

$auth = new Authentication();

if($metod == 'GET'){
    $token = $auth->authenticate($_GET['token']);
}else{
    $data = json_decode(file_get_contents("php://input"));
    $token = $auth->authenticate($data->token);
}

if(!$token){ return ;} 

$database = new Database();
$db = $database->getConnection();

$subtask = new Subtask($db);

switch($method){
    case 'GET':

        // If parameter is available fetch id from database
        if(!empty($_GET['id'])){
            $subtask->id = $_GET['id'];
        }

        $subtask->task_Id = $_GET['task_Id'];

        $result = $subtask->read();
        
        echo json_encode($result);

        break;
    case 'POST':

        $data = json_decode(file_get_contents("php://input"));

        $subtask->name = $data->name;
        $subtask->task_Id = $data->task_Id;

        
        if($subtask->create()){
            echo '{ "message": "Subtask was added." }';
        }
        else{
            echo '{ "message": "Unable to add subtask." }';
        }
        break;
    case 'PUT':

        $data = json_decode(file_get_contents("php://input"));

        $subtask->name = $data->name;
        $subtask->id = $data->id;
        
        if($subtask->update()){
            echo '{ "message": "Subtask was updated." }';
        }
        else{
            echo '{ "message": "Unable to update subtask." }';
        }

        break;
    case 'DELETE':

        $data = json_decode(file_get_contents("php://input"));

        $subtask->id = $data->id;
        
        if($subtask->delete()){
            echo '{ "message": "Subtask was deleted." }';
        }
        else{
            echo '{ "message": "Unable to delete subtask." }';
        }
            break;
    default:
        echo 'Default';
}