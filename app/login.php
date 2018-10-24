<?php 

header("Access-Control-Allow-Origin: http://127.0.0.1:4200");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('config/database.php');
require_once('objects/employee.php');
require_once('helpers/jwt_helper.php');
require_once('objects/authentication.php');

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $data = json_decode(file_get_contents("php://input"));

    $database = new Database();
    $db = $database->getConnection();
    $employee = new Employee($db);

    if(isset($data->user) && isset($data->pass)){

        $employee->username = $data->user;
        $employee->password = $data->pass;

        if($employee->login()){
            
            $startpage = $employee->checkStartPage();

            $auth = new Authentication();
            $authToken = $auth->createToken($employee->id);

            $res = array(
                "message" => true,
                "startpage" => $startpage,
                "employee" => array(
                    "id" => $employee->id,
                    "username" => $employee->username,
                    "name" => $employee->name,
                    "admin" => $employee->isAdmin
                ),
                "token" => $authToken
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