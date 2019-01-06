<?php

// required headers
header("Access-Control-Allow-Origin: http://127.0.0.1:4200");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Origin");
header("Content-Type: application/json; charset=UTF-8");

require_once('../config/database.php');
require_once('../objects/shift.php');
require_once('../helpers/jwt_helper.php');
require_once('../objects/authentication.php');


$method = $_SERVER['REQUEST_METHOD'];

// Instantiate authentication object
$auth = new Authentication();

// Authenticate token from both GET and other HTTP methods.
if($method == 'GET'){
    $token = $auth->authenticate($_GET['token']);
}else{
    $data = json_decode(file_get_contents("php://input"));
    $token = $auth->authenticate($data->token);
}

// Cancel request if authentication failed
if(!$token){ return ;} 

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate shift object
$shift = new Shift($db);

// Determine HTTP method 
switch($method){
    case 'GET':
				
		// Add relationships for shift
		// Relationships has same information about tables in database
        $shift->addRelationshipTables("all");

		// Check if employee id is sent from client
        if(!empty($_GET['employee_Id'])){
					
			// Assign value to property
            $shift->employee_Id = $_GET['employee_Id'];
						
			// Check if single shift should be fetched or by date
            if(!empty($_GET['shift_Id'])){
							
				// Assign value to property
                $shift->id = $_GET['shift_Id'];

            }elseif(!empty($_GET['date'])){
				// Assign value to property    
                $shift->date = $_GET['date'];
            }

			// Call read method
            $result = $shift->read();

            // Return JSON result to client
            echo json_encode($result);
        }

        break;
    case 'POST':

		// Assign values to object properties for creation
        $shift->startTime = $data->startTime;
        $shift->endTime = $data->endTime;
        $shift->employee_Id = $data->employee_Id;
        $shift->shiftType = $data->shiftType;

        // Add specific relationship to object to know if its an assignment or task to be added
        $shift->addRelationshipTables($shift->shiftType);

        // Adds relationship ID to relationship object
        $shift->relationship[0]->id = $data->relationship_Id;
        
		// Try to add to database and return message
        if($shift->create()){
            $res = array("status" => "success", "message" => "Shift was added.");
            echo json_encode($res);
        }
        else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to add shift.");
            echo json_encode($res);
        }

        break;
    case 'PUT':
				
		// Assign values to object properties to update
        $shift->id = $data->id;
        $shift->startTime = $data->startTime;
        $shift->endTime = $data->endTime;
        $shift->shiftType = $data->shiftType;
        $shift->shiftTypeChanged = $data->shiftTypeChanged;
			
	    // Add relationships to shift
        $shift->addRelationshipTables("all");
        $shift->relationship[0]->id = $data->relationship->id;
        $shift->relationship[1]->id = $data->relationship->id;
				
		// Try to update shift in database and return message
        if($shift->update()){
            $res = array("status" => "success", "message" => "Shift was updated.");
            echo json_encode($res);
        }else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to update shift.");
            echo json_encode($res);
        }

        break;
    case 'DELETE':
        
        // Assign value to property
        $shift->id = $data->id;
				
		// Try to delete shift in database and return message
        if($shift->delete()){
            $res = array("status" => "success", "message" => "Shift was deleted.");
            echo json_encode($res);
        }else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to delete shift.");
            echo json_encode($res);
        }

        break;
    default:
        echo 'Default';
}
