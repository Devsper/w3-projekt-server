<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('../config/database.php');
require_once('../objects/employee.php');
require_once('../helpers/jwt_helper.php');
require_once('../objects/authentication.php');

$method = $_SERVER['REQUEST_METHOD'];

$database = new Database();
$db = $database->getConnection();

$auth = new Authentication();
$token = $auth->authenticate($_GET['token']);

if(!$token){ return; }

$employee = new Employee($db);

switch($method){
    case 'GET':

        if(!empty($_GET['id'])){

            $employee->id = $_GET['id'];
            
            $result = $employee->read();
            echo json_encode($result);
        }

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