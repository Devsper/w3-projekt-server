<?php

// required headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT');
header("Content-Type: application/json; charset=UTF-8");



include_once '../config/database.php';
include_once '../functions.php';
include_once '../objects/task.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        $database = new Database();
        $db = $database->getConnection();

        $task = new Task($db);

        // If parameter is available fetch id from database
        if(!empty($_GET['id'])){

            // Uses parameter to fetch single object
            $stmt = $task->get($_GET['id']);
        }else{
            // Fetches all objects
            $stmt = $task->get();
        }

        // Creates arrays to send as JSON
        $dataArr = array();
        $dataArr["data"] = array();

        // Fetches all rows
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);
    
            $item =array(
                "id" => $Id,
                "name" => $Name,
                "createdDate" => $CreatedDate,
                "assignment_Id" => $Assignment_Id
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