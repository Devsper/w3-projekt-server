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

    function login(){

        $query = "SELECT * FROM employee WHERE Username =:username LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $this->username = parent::sanitize($this->username);
        
        $stmt->bindParam(":username", $this->username);
        
        // execute query
        $stmt->execute();

        $employeeProp = array_fill_keys(array("id", "username", "name", "password"),"");
        $dataArr = parent::fetchRows($stmt, $employeeProp, false);

        if(count($dataArr) > 0){

            if($this->password == $dataArr[0]['password']){
                
                $_SESSION['employeeSession'] = true;
                $_SESSION['employeeId'] = $dataArr[0]['id'];

                $this->id = $dataArr[0]['id'];
                $this->name = $dataArr[0]['name'];

                return true;
            }else{
                return false;
            }
        }

        return false;
    }

    function checkStartPage(){

        $query = "SELECT COUNT(ea.Employee_Id) as 'Rows'
                  FROM employee_assignment ea
                  WHERE ea.Employee_Id = :employee_id;";

        $stmt = $this->conn->prepare($query);

        $_SESSION['employeeId'] = parent::sanitize($_SESSION['employeeId']);
        
        $stmt->bindParam(":employee_id", $_SESSION['employeeId']);
        
        // execute query
        $stmt->execute();

        $employeeProp = array_fill_keys(array("rows"),"");
        $dataArr = parent::fetchRows($stmt, $employeeProp, false);

        if($dataArr[0]["rows"] > 1){
            
            return 'assignments';
        }else{
            return 'tasks';
        }

    }
}