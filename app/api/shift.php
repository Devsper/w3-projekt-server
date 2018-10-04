<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../functions.php';
include_once '../objects/shift.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        $database = new Database();
        $db = $database->getConnection();

        $shift = new Shift($db);

        // If parameter is available fetch id from database
        if(!empty($_GET['userId']) && empty($_GET['shiftId'])){

            $stmt = $shift->getUserShift($_GET['userId']);
            $shiftProp = array_fill_keys(array("name", "startTime", "endTime", "subName"),"");
            echo "what";
        }elseif(!empty($_GET['userId']) && !empty($_GET['shiftId'])){

            $stmt = $shift->get($_GET['shiftId']);
            $shiftProp = array_fill_keys(array("id", "startTime", "endTime"),"");
            echo "the";
        }else{
            // Fetches all objects
            $stmt = $shift->get();
            $shiftProp = array_fill_keys(array("id", "startTime", "endTime"),"");
        }

        $dataArr = fetchRows($stmt, $shiftProp);

        echo json_encode($dataArr);

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
        $shift->relationshipTable = "shift_assignment";

        if($shift->post()){

            echo '{ "message": "Shift was added." }';
        }
        // if unable to create the product, tell the user
        else{
            echo '{ "message": "Unable to add shift." }';
        }

        break;
    case 'PUT':
        break;
    case 'DELETE':
        break;
    default:
        echo 'Default';
}