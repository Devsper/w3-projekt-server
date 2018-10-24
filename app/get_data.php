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

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $data = json_decode(file_get_contents("php://input"));
    
    $auth = new Authentication();
    $token = $auth->authenticate($data->token);

    if(!$token){ return ;} 

    $database = new Database();
    $db = $database->getConnection();
    
    $dataToGet = $data->getData;

    switch ($dataToGet) {
        case 'employeeAssignments':
            
            $assignment = new Assignment($db);
            $assignment->employee_Id = $data->employee_Id;

            $result = $assignment->getEmployeeAssignments();

            echo json_encode($result);
            break;
        case 'employeeTasks':

            $task = new Task($db);
            $task->employee_Id = $data->employee_Id;
            $result = $task->getEmployeeTasks();

            echo json_encode($result);
            break;
        default:
            # code...
            break;
    }
}
