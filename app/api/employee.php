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

/** Removed authentication to easie use Employees as a CRUD web service */

// Instantiate authentication object
//$auth = new Authentication();

// Authenticate token from both GET and other HTTP methods
if($method == 'GET'){
    //$token = $auth->authenticate($_GET['token']);
}else{
    $data = json_decode(file_get_contents("php://input"));
    //$token = $auth->authenticate($data->token);
}

// Cancel request if authentication failed
//if(!$token){ return ;} 

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
						
			// Return result as JSON
            echo json_encode($result);
            return;
        }

        $result = $employee->read();
        echo json_encode($result);

        break;
    // HTTP methods to be used in the future
    case 'POST':
        
        $employee->name = $data->name;
        $employee->username = $data->username;
        $employee->password = $data->password;
        $employee->employeeNr = $data->employeeNr;

        // Try to create shift in database and return message
        if($employee->create()){
            $res = array("status" => "success", "message" => "Employee was created.");
            echo json_encode($res);
        }else{
            $res = array("status" => "failure", "message" => "Unable to create employee.");
            echo json_encode($res);
        }    

        break;
    case 'PUT':

        // Assign values to object properties to update
        $employee->name = $data->name;
        $employee->id = $data->id;
        $employee->username = $data->username;
        $employee->password = $data->password;
        $employee->employeeNr = $data->employeeNr;

        // Try to update employee in database and return message
        if($employee->update()){
            $res = array("status" => "success", "message" => "Employee was updated.");
            echo json_encode($res);
        }else{
            $res = array("status" => "failure", "message" => "Unable to update employee.");
            echo json_encode($res);
        }    

        break;
    case 'DELETE':

        $employee->id = $data->id;
        
        // Try to delete employee in database and return message
        if($employee->delete()){
            $res = array("status" => "success", "message" => "Employee was deleted.");
            echo json_encode($res);
        }else{
            $res = array("status" => "failure", "message" => "Unable to delete employee.");
            echo json_encode($res);
        }    
        
        break;
    default:
        echo 'Default';
}
