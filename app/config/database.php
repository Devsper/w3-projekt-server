<?php
class Database{
 
    // Database settings
    private $host = "localhost";
    private $dbName = "w3_projekt";
    private $username = "jesper";
    private $password = "jesper";
    public $conn;
 
    public function getConnection(){
        
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
