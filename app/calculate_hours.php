<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once('config/database.php');
require_once('objects/shift.php');
require_once('helpers/jwt_helper.php');
require_once('objects/authentication.php');

$method = $_SERVER['REQUEST_METHOD'];

// // Instantiate authenication object
// $auth = new Authentication();

// // Authenticate sent token both from GET and other HTTP methods
// if($method == 'GET'){
//     $token = $auth->authenticate($_GET['token']);
// }

// // Cancel request if authentication failed
// if(!$token){ return ;} 

// Determines HTTP Method
if($method == "GET"){
							
		// Creates connection to database
        $database = new Database();
        $db = $database->getConnection();
        // Instantiate Shift object
        $shift = new Shift($db);
    				
    	// Check if a date has been sent    
        if(!empty($_GET['date'])){
											
			// Add date to shift object
            $shift->date = $_GET['date'];
				
			// Fetch shifts for all employees from database
            $shifts = $shift->getAllEmployeeShifts();

            // Sorts every shift to its employee
            $employeeShifts = $shift->groupShiftsByEmployee($shifts);

            // Calculate total job hours for each employee 
            $result = $shift->calculateTotalHours($employeeShifts);

            echo json_encode($result);
        }else{
            echo '{"message": "No date present"}';
        }

}
