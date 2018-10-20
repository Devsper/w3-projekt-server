<?php 

session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('config/database.php');
require_once('objects/assignment.php');
require_once('objects/task.php');

if($_SERVER['REQUEST_METHOD'] == "GET"){

    if (isset($_SESSION['employeeSession'])) {

        $database = new Database();
        $db = $database->getConnection();
        
        $dataToGet = $_GET['getData'];

        switch ($dataToGet) {
            case 'employeeAssignments':
                
                $assignment = new Assignment($db);
                $result = $assignment->getEmployeeAssignments();

                echo json_encode($result);
                break;
            case 'employeeTasks':

                $task = new Task($db);
                $result = $task->getEmployeeTasks();

                echo json_encode($result);
                break;
            default:
                # code...
                break;
        }



    }
}