<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('../config/database.php');
require_once('../objects/employee.php');
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

// Instantiate employee object
$employee = new Employee($db);

// Determine HTTP method
switch($method){
    case 'GET':
				
		// Check if employee id is present
        if(!empty($_GET['id'])){
						
			// Assign value to object property
            $employee->id = $_GET['id'];
            
            // Fetch employee from database
            $result = $employee->read();
            // Determine startpage for employee
            $startpage = $employee->determineStartPage();
            $result['startpage'] = $startpage;
						
			// Return result as JSON
            echo json_encode($result);
        }

        break;
    // HTTP methods to be used in the future
    case 'POST':
        break;
    case 'PUT':
        break;
    case 'DELETE':
        break;
    default:
        echo 'Default';
}
