<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../functions.php';
include_once '../objects/employee.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        $database = new Database();
        $db = $database->getConnection();

        $employee = new Employee($db);

        // If parameter is available fetch id from database
        if(!empty($_GET['id'])){

            // Uses parameter to fetch single object
            $stmt = $employee->get($_GET['id']);
        }else{
            // Fetches all objects
            $stmt = $employee->get();
        }

        // Creates arrays to send as JSON
        $dataArr = array();
        $dataArr["data"] = array();

        // Fetches all rows
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);
 
            $item =array(
                "id" => $Id,
                "employeeNr" => $EmployeeNr,
                "name" => $Name,
                "isActive" => $IsActive,
                "isAdmin" => $IsAdmin,
                "username" => $Username,
                "createdDate" => $CreatedDate
            );

            // Add row to array
            array_push($dataArr["data"], $item);
        }
        
        echo json_encode($dataArr);

        break;
    case 'POST':
        break;
    case 'PUT':
        break;
    case 'DELETE':
        break;
    default:
        echo 'Default';
}