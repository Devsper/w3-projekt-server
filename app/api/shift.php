<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/shift.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':

        $database = new Database();
        $db = $database->getConnection();
        $shift = new Shift($db);
        $shift->addRelationshipTables("all");

        if(!empty($_GET['userId']) && !empty($_GET['shiftId'])){

            $shift->employee_Id = $_GET['userId'];
            $shift->id = $_GET['shiftId'];

            $shift->readSingle();

        }elseif(!empty($_GET['userId']) && empty($_GET['date'])){

            $shift->employee_Id = $_GET['userId'];

            $shift->readAll();

        }elseif(!empty($_GET['userId']) && !empty($_GET['date'])){

            $shift->employee_Id = $_GET['userId'];
            $shift->date = $_GET['date'];
            $shift->readAllDate();
        }
        else{
            echo '{ "message": "Could not retrieve shifts." }';
            return;
        }

        break;
    case 'POST':
        
        $database = new Database();
        $db = $database->getConnection();

        $shift = new Shift($db);

        // get posted data
        $data = json_decode(file_get_contents("php://input"));

        $shift->startTime = $data->startTime;
        $shift->endTime = $data->endTime;
        $shift->employee_Id = $data->employee_Id;
        $shift->shiftType = $data->shiftType;
        
        $shift->addRelationshipTables($shift->shiftType);

        // Adds relationship ID to relationship object
        $shift->relationship[0]->id = $data->relationship->id;

        if($shift->create()){

            echo '{ "message": "Shift was added." }';
        }
        // if unable to create the product, tell the user
        else{
            echo '{ "message": "Unable to add shift." }';
        }

        break;
    case 'PUT':
        $database = new Database();
        $db = $database->getConnection();

        $shift = new Shift($db);
        
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

        $database = new Database();
        $db = $database->getConnection();

        $shift = new Shift($db);
        
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