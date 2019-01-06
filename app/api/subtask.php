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

// Instantiate authentication object
$auth = new Authentication();

// Authenticate token from both GET and other HTTP methods
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

// Instantiate subtask object
$subtask = new Subtask($db);

// Determine HTTP method
switch($method){
    case 'GET':

        // Check if id is present to fetch a single row in database
        if(!empty($_GET['id'])){
            $subtask->id = $_GET['id'];
        }
			
		// Assign value to object property
        $subtask->task_Id = $_GET['task_Id'];
				
		// Fetch rows from database
        $result = $subtask->read();
        
        // Return JSON result to client
        echo json_encode($result);

        break;
    case 'POST':
				
		// Get JSON data sent from client
        $data = json_decode(file_get_contents("php://input"));
				
		// Assign values to object properties
        $subtask->name = $data->name;
        $subtask->task_Id = $data->task_Id;
        
        // Try to add subtask to database, return message
        if($subtask->create()){
            $res = array("status" => "success", "message" => "Subtask was added.");
            echo json_encode($res);
        }
        else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to add subtask.");
            echo json_encode($res);
        }
        break;
    case 'PUT':
				
		// Get JSON data sent from client
        $data = json_decode(file_get_contents("php://input"));
				
		// Assign values to object properties
        $subtask->name = $data->name;
        $subtask->id = $data->id;
        
        // Try to update subtask in database, return message
        if($subtask->update()){
            $res = array("status" => "success", "message" => "Subtask was updated.");
            echo json_encode($res);
        }
        else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to update subtask.");
            echo json_encode($res);
        }

        break;
    case 'DELETE':
        // Get JSON data sent from client
        $data = json_decode(file_get_contents("php://input"));
				
		// Assign values to object properties
        $subtask->id = $data->id;
        
        // Try to delete subtask in database, send back message
        if($subtask->delete()){
            $res = array("status" => "success", "message" => "Subtask was deleted.");
            echo json_encode($res);
        }
        else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to delete subtask.");
            echo json_encode($res);
        }
            break;
    default:
        echo 'Default';
}
