<?php 

session_start();

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

        if(isset($_SESSION['employeeSession'])){

            $result = $shift->getAllEmployeeShifts();
            $result = $shift->calculateTotalHours($result);

            echo json_encode($result);
        }
}