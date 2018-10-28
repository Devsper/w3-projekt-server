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

// Determine HTTP Method
if($_SERVER['REQUEST_METHOD'] == "POST"){
	
	// Get JSON data sent from client
    $data = json_decode(file_get_contents("php://input"));

	// Create database connection
    $database = new Database();
    $db = $database->getConnection();
    // instantiate object
    $employee = new Employee($db);

	// Check if username and password are present
    if(isset($data->user) && isset($data->pass)){
			
	    // Bind to properties
        $employee->username = $data->user;
        $employee->password = $data->pass;
				
		// If login is successful
        if($employee->login()){
            
            // Determine startpage for employee
            $startpage = $employee->checkStartPage();
						
			// Create authenication token 
            $auth = new Authentication();
            $authToken = $auth->createToken($employee->password);
						
			// Associative array with data to return to client	
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
			// Return as JSON
            echo json_encode($res);

        }else{

            $res = array(
                "message" => false
            );

            echo json_encode($res);

        }
    }
}
