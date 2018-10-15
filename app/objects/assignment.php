<?php 

require_once('method.php');
require_once('relationship.php');

class Assignment extends Method{
    
    private $conn;
    private $tableName = "assignment";

    // Properties of class
    public $id;
    public $name;
    public $createdDate;
    public $relationships = array();

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){

        $query = "SELECT Name FROM {$this->tableName};";

    }

    public function addRelationshipTables($relationship){

        if($relationship == "employee"){
            array_push($this->relationship, new Relationship("Employee_Id", "employee_assignment", "employee"));
        }
    }
}