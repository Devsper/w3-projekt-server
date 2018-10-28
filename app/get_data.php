<?php 

header("Access-Control-Allow-Origin: http://127.0.0.1:4200");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('config/database.php');
require_once('objects/assignment.php');
require_once('objects/task.php');
require_once('helpers/jwt_helper.php');
require_once('objects/authentication.php');

$method = $_SERVER['REQUEST_METHOD'];

// Instantiate authenication object
$auth = new Authentication();

// Authenticate sent token both from GET and other HTTP methods
if($method == 'POST'){
    $data = json_decode(file_get_contents("php://input"));
    $token = $auth->authenticate($data->token);
}

// Cancel request if authentication failed
if(!$token){ return ;} 

//Determine HTTP Method
if($method == "POST"){

	// Read JSON data from client
    $data = json_decode(file_get_contents("php://input"));
    
    // Authenticate JSON Web token
    $auth = new Authentication();
    $token = $auth->authenticate($data->token);

	// Cancel request if token is invalid
    if(!$token){ return ;} 

	// Create database connection
    $database = new Database();
    $db = $database->getConnection();
    
    
    $dataToGet = $data->getData;
	
	// Determine if assignments or tasks should be fetched from database
    switch ($dataToGet) {
        case 'employeeAssignments':
            
            // instantiate object and add data
            $assignment = new Assignment($db);
            $assignment->employee_Id = $data->employee_Id;

			// Fetch assignments from database
            $result = $assignment->getEmployeeAssignments();
				
			// Return data as JSON
            echo json_encode($result);
            break;
        case 'employeeTasks':
				
			// instantiate object and add data 
            $task = new Task($db);
            $task->employee_Id = $data->employee_Id;
            
            // Fetch tasks from database 
            $result = $task->getEmployeeTasks();
				
		    // return data as JSON
            echo json_encode($result);
            break;
        default:
            break;
    }
}
