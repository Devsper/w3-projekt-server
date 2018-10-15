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

    function get($employeeId = null){
        
        // Fetches all employees if no parameter is passed else fetch specific employee
        if(empty($employeeId)){
            // select all query
            $query = "SELECT * FROM {$this->tableName}";
        }else{
            $query = "SELECT * FROM {$this->tableName} WHERE {$this->tableName}.Id = $employeeId";
        }
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    function login(){

        $query = "SELECT * FROM employee WHERE Username =:username LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        
        $stmt->bindParam(":username", $this->username);
        
        // execute query
        $stmt->execute();

        $employeeProp = array_fill_keys(array("id", "username", "password"),"");
        $dataArr = parent::fetchRows($stmt, $employeeProp, false);

        if(count($dataArr) > 0){

            if($this->password == $dataArr[0]['password']){
 
                $_SESSION['employeeSession'] = true;
                $_SESSION['employeeId'] = $dataArr[0]['id'];
                return true;
            }else{

                return false;
            }
        }
    }
}