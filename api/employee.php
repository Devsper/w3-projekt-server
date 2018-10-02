<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/employee.php';


$method = $_SERVER['REQUEST_METHOD'];


$database = new Database();
$db = $database->getConnection();

$employee = new Employee($db);
echo $_GET['id'];

$stmt = $employee->get();
$num = $stmt->rowCount();

$employeeArr = array();
$employeeArr["employees"] = array();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    extract($row);

    $employeeItem = array(
        "id" => $Id,
        "employeeNr" => $EmployeeNr,
        "isActive" => $IsActive,
        "isAdmin" => $IsAdmin,
        "username" => $Username,
        "createdDate" => $CreatedDate
    );

    array_push($employeeArr["employees"], $employeeItem);
}

echo json_encode($employeeArr);