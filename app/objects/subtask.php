<?php 

class SubTask{
    
    private $conn;
    private $tableName = "subtask";

    // Properties of class
    public $id;
    public $name;
    public $createdDate;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function get($param = null){
        
        // Fetches all employees if no parameter is passed else fetch specific employee
        if(empty($param)){
            // select all query
            $query = "SELECT * FROM {$this->tableName}";
        }else{
            $query = "SELECT * FROM {$this->tableName} WHERE {$this->tableName}.Id = $param";
        }
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
}