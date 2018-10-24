<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('../config/database.php');
require_once('../objects/shift.php');
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
$shift = new Shift($db);

switch($method){
    case 'GET':

        $shift->addRelationshipTables("all");

        if(!empty($_GET['employeeId'])){

            $shift->employee_Id = $_GET['employeeId'];

            if(!empty($_GET['shiftId'])){

                $shift->id = $_GET['shiftId'];

            }elseif(!empty($_GET['date'])){
    
                $shift->date = $_GET['date'];
            }

            $shift->read();
        }

        break;
    case 'POST':
        
        // get posted data
        $data = json_decode(file_get_contents("php://input"));

        $shift->startTime = $data->startTime;
        $shift->endTime = $data->endTime;
        $shift->employee_Id = $data->employee_Id;
        $shift->shiftType = $data->shiftType;
        
        $shift->addRelationshipTables($shift->shiftType);

        // Adds relationship ID to relationship object
        $shift->relationship[0]->id = $data->relationship_Id;

        if($shift->create()){
            echo '{ "message": "Shift was added." }';
        }
        else{
            echo '{ "message": "Unable to add shift." }';
        }

        break;
    case 'PUT':
        
        $data = json_decode(file_get_contents("php://input"));

        $shift->id = $data->id;
        $shift->startTime = $data->startTime;
        $shift->endTime = $data->endTime;
        $shift->shiftType = $data->shiftType;
        $shift->shiftTypeChanged = $data->shiftTypeChanged;

        $shift->addRelationshipTables("all");
        $shift->relationship[0]->id = $data->relationship->id;
        $shift->relationship[1]->id = $data->relationship->id;

        if($shift->update()){
            echo '{ "message": "Shift was updated." }';
        }else{
            echo '{ "message": "Unable to update shift." }';
        }

        break;
    case 'DELETE':
        
        $data = json_decode(file_get_contents("php://input"));
        $shift->id = $data->id;

        if($shift->delete()){
            echo '{ "message": "Shift was deleted." }';
        }else{
            echo '{ "message": "Unable to delete shift." }';
        }

        break;
    default:
        echo 'Default';
}