<?php 

class SubTask{
    
    private $conn;
    private $tableName = "subtask";

    // Properties of class
    public $id;
    public $name;
    public $createdDate;
    public $task_Id;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){

        if($this->id){

            $query = "SELECT st.Id, st.Name FROM subtask st WHERE t.Task_Id = :task_id AND t.id = :id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            $this->task_id = htmlspecialchars(strip_tags($this->task_id));
            $this->id = htmlspecialchars(strip_tags($this->id));
            // bind values
            $stmt->bindParam(":task_id", $this->task_id);
            $stmt->bindParam(":id", $this->id);
        }else{
            $query = "SELECT st.Id, st.Name FROM {$this->tableName} st WHERE Task_Id = :task_id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            $this->task_id = htmlspecialchars(strip_tags($this->task_id));
            // bind values
            $stmt->bindParam(":task_id", $this->task_id);
        }

        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    function create(){
        
        $query = "INSERT INTO {$this->tableName} (Name, Task_Id) VALUES (':name', ':task_id')";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->task_id = htmlspecialchars(strip_tags($this->task_id));
        $this->name = htmlspecialchars(strip_tags($this->name));

        // bind values
        $stmt->bindParam(":task_id", $this->task_id);
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