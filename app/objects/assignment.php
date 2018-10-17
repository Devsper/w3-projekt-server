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

            $query = "SELECT a.Id, a.Name FROM {$this->tableName} a WHERE t.id = :id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            $this->id = htmlspecialchars(strip_tags($this->id));
            // bind values
            $stmt->bindParam(":id", $this->id);

        }else{
            $query = "SELECT a.Id, a.Name FROM {$this->tableName} a";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
        }

        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    function create(){

        $query = "INSERT INTO {$this->tableName} (Name) VALUES (':name')";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->name = htmlspecialchars(strip_tags($this->name));

        // bind values
        $stmt->bindParam(":name", $this->name);
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    function update(){
        
        $query = "UPDATE {$this->tableName} SET name =:name WHERE id=:id";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));

        // bind values
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    function delete(){

        $query = "DELETE FROM {$this->tableName} WHERE id=:id";
       
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":id", $this->id);
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    public function addRelationshipTables($relationship){

        if($relationship == "employee"){
            array_push($this->relationships, new Relationship("Employee_Id", "employee_assignment", "employee"));
        }
    }
}