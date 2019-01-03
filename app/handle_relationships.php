<?php 

// Required headers
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
$data = json_decode(file_get_contents("php://input"));
$token = $auth->authenticate($data->token);

// Cancel request if authentication failed
if(!$token){ return ;} 

// Create database connection
$database = new Database();
$db = $database->getConnection();

if($method == "POST"){
    
    // Determine which relationship to work with
    $relationship_to_handle = $data->relationship;

    if($relationship_to_handle == "assignments"){

        // Instantiate assignment object
        $assignment = new Assignment($db);
        $assignment->employee_Id = $data->employee_Id;
        $assignment->ids = $data->assignmentIds;

        if($assignment->updateEmployeeAssignments()){

            $res = array("status" => "success", "message" => "Assignments for employee was updated.");
            echo json_encode($res);
        }else{

            $res = array("status" => "failure", "message" => "Unable to update assignments for employee.");
            echo json_encode($res);
        }

    }

    if($relationship_to_handle == "tasks"){
        
        // Instantiate task object
        $task = new Task($db);
        $task->employee_Id = $data->employee_Id;
        $task->ids = $data->taskIds;

        if($task->updateEmployeeTasks()){

            $res = array("status" => "success", "message" => "Tasks for employee was updated.");
            echo json_encode($res);
        }else{

            $res = array("status" => "failure", "message" => "Unable to update tasks for employee.");
            echo json_encode($res);
        }
    }
}
