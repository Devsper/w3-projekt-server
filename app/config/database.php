<?php

class Database{
 
    // Database settings
    private $host = "localhost";
    private $dbName = "w3_projekt";
    private $username = "jesper";
    private $password = "jesper";
    public $conn;
 	 
 	 /**
     * Creates PDO Connection to database
     * 
     * @throws PDOException - Connection error
     * @return int $this->conn - Connection to database
     */ 
    public function getConnection(){
 			
 		// Resets connection if present       
        $this->conn = null;
 	
        try{
        	// Creates database connection from properties
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
