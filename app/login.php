<?php 

session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('config/database.php');
require_once('objects/employee.php');


if($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_SESSION['employeeSession'])){

    if(!isset($_SESSION['employeeSession'])){
        $database = new Database();
        $db = $database->getConnection();
        $employee = new Employee($db);
    }else{
        session_destroy();
    }

    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->user) && isset($data->pass)){

        $employee->username = $data->user;
        $employee->password = $data->pass;

        if($employee->login()){
            
            $res = array(
                "message" => true,
            );

            echo json_encode($res);

        }else{

            $res = array(
                "message" => false
            );

            echo json_encode($res);

        }
    }
}