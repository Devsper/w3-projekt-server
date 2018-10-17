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

        $data = json_decode(file_get_contents("php://input"));

        $assignment->name = $data->name;

        if($assignment->create()){
            echo '{ "message": "Assignment was added." }';
        }
        else{
            echo '{ "message": "Unable to add assignment." }';
        }
        break;
    case 'PUT':

        $data = json_decode(file_get_contents("php://input"));

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

        $data = json_decode(file_get_contents("php://input"));

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