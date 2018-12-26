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
    public $name;
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
        
        if($this->id){
            // SQL query to fetch employee by id
            $query = "SELECT * FROM employee WHERE Id =:id LIMIT 1";
        }else{
            // SQL query to fetch employee by id
            $query = "SELECT * FROM employee";
        }
        
				
        // Prepares statement
        $stmt = $this->conn->prepare($query);
        // Santize and bind property
        $this->username = parent::sanitize($this->id);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        $stmt->execute();
        
         // Creates associative array with keys to contain values from fetched from database
        $employeeProp = array_fill_keys(array("id", "username", "name", "isAdmin", "password", "employeeNr"),"");
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $employeeProp, false);
				
        // Return associative array
        return $dataArr;
    }

    /**
     * Updates employee record in database
     * 
     * @return boolean Update status
     */
    function update(){  
        // SQL query to update a given record      
        $query = "UPDATE {$this->tableName} SET Name =:name, Username = :username, Password = :password, EmployeeNr = :employeeNr  WHERE id=:id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Santize and bind properties
        $this->id = parent::sanitize($this->id);
        $this->name = parent::sanitize($this->name);
        $this->username = parent::sanitize($this->username);
        $this->password = parent::sanitize($this->password);
        $this->employeeNr = parent::sanitize($this->employeeNr);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":employeeNr", $this->employeeNr);

        // Execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /**
     * Creates employee record in database
     * 
     * @return boolean Create status
     */
    function create(){  

        // SQL query to create employee
        $query = "INSERT INTO {$this->tableName} (EmployeeNr, Username, Name, IsAdmin, IsActive, Password) VALUES (:employeeNr, :username, :name, :isAdmin, :isActive, :password)";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Santize and bind properties
        $this->name = parent::sanitize($this->name);
        $this->username = parent::sanitize($this->username);
        $this->password = parent::sanitize($this->password);
        $this->employeeNr = parent::sanitize($this->employeeNr);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":employeeNr", $this->employeeNr);
        // Currently hardcoded values 
        $isAdmin = 'N';
        $isActive = 'Y';
        $stmt->bindParam(":isAdmin", $isAdmin);
        $stmt->bindParam(":isActive", $isActive);
                
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /**
     * Deletes employee record in database
     * 
     * @return boolean Delete status
     */   
    function delete(){

        // SQL query to delete given record, 
        $query = "DELETE FROM {$this->tableName}
                  WHERE id = :employeeId";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Santize and bind property
        $this->id = parent::sanitize($this->id);
        $stmt->bindParam(":employeeId", $this->id);

        // Execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
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
