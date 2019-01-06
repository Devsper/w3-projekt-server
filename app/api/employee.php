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

/** Removed authentication to easier use Employees as a CRUD web service */

// Instantiate authentication object
//$auth = new Authentication();

// Authenticate token from both GET and other HTTP methods
if($method == 'GET'){
    //$token = $auth->authenticate($_GET['token']);
}else{
    $data = json_decode(file_get_contents("php://input"));
    //$token = $auth->authenticate($data->token);

    //check if it was invalid json string
    if( $data == null ){
        $res = return_error("json_error");
        echo json_encode($res);
        return;
    } 

    $validData = true;

    // Check all data for post and update methods
    if($method == 'POST' || $method == 'UPDATE'){

        // Validates input
        is_string($data->name)? true : $validData = false;
        is_string($data->username)? true : $validData = false;
        is_string($data->password)? true : $validData = false;
        // Checks if employee number is both numeric and string
        is_string($data->employeeNr)? true : $validData = false;
        is_numeric($data->employeeNr)? true : $validData = false;
    }

    // Extra check when IDs are present
    if($method == 'PUT' || $method == 'DELETE'){
        is_numeric($data->id)? true : $validData = false;
    }

    if(!$validData){
        $res = return_error("data_error");
        echo json_encode($res);
        return;
    }
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
            
            // Validate that id is a number
            if(!is_numeric($_GET['id'])){
                $res = return_error("data_error");
                echo json_encode($res);
                return;
            }

			// Assign value to object property
            $employee->id = $_GET['id'];
            
            // Fetch employee from database
            $result = $employee->read();

            // Try to read employee
            if($result["success"]){
                echo json_encode($result["data"]);
                return;
            }else{
                $res = return_error("server_error");
                echo json_encode($res);
                return;
            }

           
        }

        $result = $employee->read();

        // Try to read employee
        if($result["success"]){
            echo json_encode($result["data"]);
            return;
        }else{
            $res = return_error("server_error");
            echo json_encode($res);
            return;
        }

        break;
    // HTTP methods to be used in the future
    case 'POST':

        $employee->name = $data->name;
        $employee->username = $data->username;
        $employee->password = $data->password;
        $employee->employeeNr = $data->employeeNr;

        // Try to create shift in database and return message
        if($employee->create()){
            http_response_code(201);
            $res = array("status" => "success", "message" => "Employee was created.");
            echo json_encode($res);
        }else{
            $res = return_error("server_error");
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
            $res = return_error("server_error");
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
            $res = return_error("server_error");
            echo json_encode($res);
        }    
        
        break;
    default:
        echo 'Default';
}

function return_error($error_type){

    if($error_type == "server_error"){
        http_response_code(500);
    }else{
        http_response_code(400);
    }

    switch($error_type){
        case "json_error":
            $message = "Unable to handle employee. Please check that your JSON string has been formatted correctly";
            break;
        case "data_error":
            $message = "Unable to handle employee. Please check that your data and parameters has the correct data types";
            break;
        case "server_error":
            $message = "Unable to handle employee. The server could not handle your request. Please try again later";
            break;
        default:
            $message = "Unable to handle employee. Reason Unknown";
    }
    
    return array("status" => "failure", "message" => $message);
}