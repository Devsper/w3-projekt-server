<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('config/database.php');
require_once('objects/shift.php');

if($_SERVER['REQUEST_METHOD'] == "GET"){

        $database = new Database();
        $db = $database->getConnection();
        $shift = new Shift($db);

        try{
            $token = JWT::decode($_GET['token'], 'secret_server_key');
        }catch(Exception $e){
    
            $res = array(
                "Exception" => $e->getMessage()
            );
    
            echo json_encode($res);
            return;
        }
        
        if(!empty($_GET['date'])){

            $shift->date = $_GET['date'];

            $result = $shift->getAllEmployeeShifts();
            $result = $shift->calculateTotalHours($result);

            echo json_encode($result);
        }else{
            echo '{"message": "No date present"}';
        }

}