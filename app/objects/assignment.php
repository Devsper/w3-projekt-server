<?php 

require_once('method.php');
require_once('relationship.php');

class Assignment extends Method{
    
    private $conn;
    private $tableName = "assignment";

    // Properties of class
    public $id;
    public $name;
    public $relationships = array();

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function getEmployeeAssignments(){

        $query = "SELECT a.Name, a.Id
                  FROM {$this->tableName} a 
                  INNER JOIN employee_assignment ON a.id = employee_assignment.Assignment_Id 
                  INNER JOIN employee ON employee.Id = employee_assignment.Employee_Id 
                  WHERE employee.Id = {$this->relationships[0]->id}";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();

        $employeeProp = array_fill_keys(array("name", "id"),"");
        $dataArr = parent::fetchRows($stmt, $employeeProp);

        return $dataArr;
    }

    function read(){

        if($this->id){

            $query = "SELECT a.Id, a.Name FROM {$this->tableName} a WHERE a.id = :id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            $this->id = parent::sanitize($this->id);
            // bind values
            $stmt->bindParam(":id", $this->id);

        }else{
            $query = "SELECT a.Id, a.Name FROM {$this->tableName} a";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
        }

        // execute query
        $stmt->execute();
    
        $assignmentProp = array_fill_keys(array("name", "id"),"");
        $dataArr = parent::fetchRows($stmt, $assignmentProp);
    
        return $dataArr;
    }
    function create(){

        $query = "INSERT INTO {$this->tableName} (Name) VALUES (:name)";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->name = parent::sanitize($this->name);

        // bind values
        $stmt->bindParam(":name", $this->name);
         
        // execute query
         if($stmt->execute()){
            return true;
        }
        return false;
    }
    function update(){
        
        $query = "UPDATE {$this->tableName} SET Name =:name WHERE id=:id";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->id = parent::sanitize($this->id);
        $this->name = parent::sanitize($this->name);

        // bind values
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    function delete(){

        $query = "DELETE FROM {$this->tableName} WHERE id=:id";
       
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->id = parent::sanitize($this->id);

        // bind values
        $stmt->bindParam(":id", $this->id);
        
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function addRelationshipTables($relationship){

        if($relationship == "employee"){
            array_push($this->relationships, new Relationship("Employee_Id", "employee_assignment", "employee"));
        }
    }
}