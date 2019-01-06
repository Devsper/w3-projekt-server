<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('../config/database.php');
require_once('../objects/task.php');
require_once('../helpers/jwt_helper.php');
require_once('../objects/authentication.php');

$method = $_SERVER['REQUEST_METHOD'];

// Instantiate authentication object
$auth = new Authentication();

// Authenticate token from GET and other HTTP methods
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

// Instantiate task object
$task = new Task($db);

// Determine HTTP method
switch($method){
    case 'GET':
				
		// Check if id is present to fetch a single row
        if(!empty($_GET['id'])){
            $task->id = $_GET['id'];
        }
        
		// Fetch from database
        $result = $task->read();
				
        // Return JSON result to client
        echo json_encode($result);
        break;
    case 'POST':
				
		// Assign values to object properties
        $task->name = $data->name;
        $task->assignment_Id = $data->assignment_Id;
				
		// Try to add task to database, return message
        if($task->create()){
            $res = array("status" => "success", "message" => "Task was added.");
            echo json_encode($res);
        }
        else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to add task.");
            echo json_encode($res);
        }
        break;
    case 'PUT':
			 
		// Assign values to object properties
        $task->name = $data->name;
        $task->id = $data->id;
			
		// Try to update task in database, return message
        if($task->update()){
            $res = array("status" => "success", "message" => "Task was updated.");
            echo json_encode($res);
        }
        else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to update task.");
            echo json_encode($res);
        }
        break;
    case 'DELETE':
			 
		// Assign values to object properties
        $task->id = $data->id;
				
		// Try to delete task in database, return message
        if($task->delete()){
            $res = array("status" => "success", "message" => "Task was deleted.");
            echo json_encode($res);
        }
        else{
            http_response_code(500);
            $res = array("status" => "failure", "message" => "Unable to delete task.");
            echo json_encode($res);
        }
        break;
    default:
        echo 'Default';
}
