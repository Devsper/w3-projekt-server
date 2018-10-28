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

// Instantiate authenication object
$auth = new Authentication();

// Authenticate sent token both from GET and other HTTP methods
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
// Instantiate assignment object
$assignment = new Assignment($db);

// Determine HTTP method
switch($method){
    case 'GET':
				
		// If id is present add it to object, method will then fetch specific row id
        if(!empty($_GET['id'])){
            $assignment->id = $_GET['id'];
        }
				
		// Fetch from database
        $result = $assignment->read();
				
		// Send back result to clien as JSON
        echo json_encode($result);

        break;
    case 'POST':
				
		// Assign value to object property
        $assignment->name = $data->name;
				
		// Try to add assignment to database
        if($assignment->create()){
					
			// Send back success message
            $res = array("status" => "success", "message" => "Assignment was added.");
            echo json_encode($res);
        }
        else{
         	// Send back failed message	 
            $res = array("status" => "failure", "message" => "Assignment not was added.");
            echo json_encode($res);
        }
        break;
    case 'PUT':
				
		// Assign values to object properties
        $assignment->name = $data->name;
        $assignment->id = $data->id;
				
		// Try to update assingment
        if($assignment->update()){
					
            $res = array("status" => "success", "message" => "Assignment was updated.");
            echo json_encode($res);
        }
        else{

            $res = array("status" => "failure", "message" => "Unable to update assignment.");
            echo json_encode($res);
        }
        break;
    case 'DELETE':
			
		// Assign values to object properties
        $assignment->id = $data->id;
				
		// Try to delete assignment
        if($assignment->delete()){
        	  
            $res = array("status" => "success", "message" => "Assignment was deleted.");
            echo json_encode($res);
        }
        else{
            $res = array("status" => "failure", "message" => "Unable to delete assignment.");
            echo json_encode($res);
        }
        break;
    default:
        echo 'Default';
}
