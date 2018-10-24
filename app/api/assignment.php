<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('../config/database.php');
require_once('../objects/assignment.php');
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
$assignment = new Assignment($db);

switch($method){
    case 'GET':

        if(!empty($_GET['id'])){
            $assignment->id = $_GET['id'];
        }

        $result = $assignment->read();

        echo json_encode($result);

        break;
    case 'POST':

        $assignment->name = $data->name;

        if($assignment->create()){
            echo '{ "message": "Assignment was added." }';
        }
        else{
            echo '{ "message": "Unable to add assignment." }';
        }
        break;
    case 'PUT':

        $assignment->name = $data->name;
        $assignment->id = $data->id;

        if($assignment->update()){
            echo '{ "message": "Assignment was updated." }';
        }
        else{
            echo '{ "message": "Unable to update assignment." }';
        }
        break;
    case 'DELETE':

        $assignment->id = $data->id;

        if($assignment->delete()){
            echo '{ "message": "Assignment was deleted." }';
        }
        else{
            echo '{ "message": "Unable to delete assignment." }';
        }
        break;
    default:
        echo 'Default';
}