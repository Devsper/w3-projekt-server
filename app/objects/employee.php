<?php

require_once('method.php');

class Employee extends Method{
    
    private $conn;
    private $tableName = "employee";

    // Properties of class
    public $id;
    public $employeeNr;
    public $isActive;
    public $isAdmin;
    public $username;
    public $password;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }
    
    /**
     * Fetches single employee record from database
     * 
     * @return array $dataArr Fetched record from database
     */
    function read(){
				
        // SQL query to fetch employee by id
        $query = "SELECT * FROM employee WHERE Id =:id LIMIT 1";
				
        // Prepares statement
        $stmt = $this->conn->prepare($query);

        // Santize and bind property
        $this->username = parent::sanitize($this->id);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        $stmt->execute();
        
         // Creates associative array with keys to contain values from fetched from database
        $employeeProp = array_fill_keys(array("id", "username", "name", "isAdmin"),"");
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $employeeProp, false);
				
        // Return associative array
        return $dataArr;
    }
    
    /**
     * Login employee to application
     * 
     * @return boolean Result of login attempt
     */
    function login(){

        // SQL query to fetch user from database
        $query = "SELECT * FROM employee WHERE Username =:username LIMIT 1";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Santize and bind property
        $this->username = parent::sanitize($this->username);
        $stmt->bindParam(":username", $this->username);
        
        // Execute query
        $stmt->execute();

        // Creates associative array with keys to contain values from fetched from database
        $employeeProp = array_fill_keys(array("id", "username", "name", "password", "isAdmin"),"");
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $employeeProp, false);

        // If user was found
        if(count($dataArr) > 0){

            // Compare passwords
            if($this->password == $dataArr[0]['password']){
                
                // Add values to send back to user
                $this->id = $dataArr[0]['id'];
                $this->name = $dataArr[0]['name'];
                $this->isAdmin = $dataArr[0]['isAdmin'];

                return true;
            }else{
                return false;
            }
        }

        return false;
    }
}
