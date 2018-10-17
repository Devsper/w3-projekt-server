<?php 

require_once('method.php');

class Subtask extends Method{
    
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

            $query = "SELECT st.Id, st.StName FROM subtask st WHERE st.Task_Id = :task_id AND st.id = :id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            $this->task_id = htmlspecialchars(strip_tags($this->task_Id));
            $this->id = htmlspecialchars(strip_tags($this->id));
            // bind values
            $stmt->bindParam(":task_id", $this->task_id);
            $stmt->bindParam(":id", $this->id);
        }else{
            $query = "SELECT st.Id, st.StName FROM {$this->tableName} st WHERE Task_Id = :task_id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            $this->task_Id = htmlspecialchars(strip_tags($this->task_Id));
            // bind values
            $stmt->bindParam(":task_id", $this->task_Id);
        }

        // execute query
        $stmt->execute();

        $subtaskProp = array_fill_keys(array("StName", "id"),"");
        $dataArr = parent::fetchRows($stmt, $subtaskProp);
    
        return $dataArr;
    }
    function create(){

        $query = "INSERT INTO {$this->tableName} (Name, Task_Id) VALUES (:name, :task_Id)";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->task_Id = htmlspecialchars(strip_tags($this->task_Id));
        $this->name = htmlspecialchars(strip_tags($this->name));

        // bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':task_Id', $this->task_Id);

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
    
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));

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
    
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":id", $this->id);
        
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
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