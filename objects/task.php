<?php 

class Task{
    
    private $conn;
    private $tableName = "task";

    // Properties of class
    public $id;
    public $name;
    public $createdDate;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read products
    function get(){
        
        // select all query
        $query = "SELECT * FROM ".$this->tableName;
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
}