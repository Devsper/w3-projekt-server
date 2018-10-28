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

    /**
     * Fetches subtasks from database, single and multiple record
     * 
     * @return array $dataArr Fetched rows from database as associative array.
     */
    function read(){

        // If id property is present fetch single record
        if($this->id){

            // SQL query to select single subtask
            $query = "SELECT st.Id, st.Name FROM {$this->tableName} st WHERE st.Id = :id";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // Sanitize property
            $this->id = parent::sanitize($this->id);
      
            // Bind property to statement
            $stmt->bindParam(":id", $this->id);
        }else{
            // SQL query to select subtask from specific task relationship
            $query = "SELECT st.Id, st.Name FROM {$this->tableName} st WHERE Task_Id = :task_id";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);

            // Santize and bind property
            $this->task_Id = parent::sanitize($this->task_Id);
            $stmt->bindParam(":task_id", $this->task_Id);
        }

        // Execute query
        $stmt->execute();

        // Creates associative array with keys to contain values from fetched from database
        $subtaskProp = array_fill_keys(array("name", "id"),"");
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $subtaskProp);
    
        return $dataArr;
    }
    
    /**
     * Creates a record of subtask in database
     * 
     * @return boolean Creation status
     */
    function create(){

        // SQL query to create an subtask
        $query = "INSERT INTO {$this->tableName} (Name, Task_Id) VALUES (:name, :task_Id)";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Santize and bind properties
        $this->task_Id = parent::sanitize($this->task_Id);
        $this->name = parent::sanitize($this->name);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':task_Id', $this->task_Id);

        // Execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /**
     * Updates subtask record in database
     * 
     * @return boolean Update status
     */
    function update(){

        // SQL query to update a given record      
        $query = "UPDATE {$this->tableName} SET Name =:name WHERE id=:id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Santize and bind properties
        $this->id = parent::sanitize($this->id);
        $this->name = parent::sanitize($this->name);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);

        // Execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /**
     * Deletes assignment record in database
     * 
     * @return boolean Delete status
     */   
    function delete(){
        
        // SQL query to delete given record
        $query = "DELETE FROM {$this->tableName} WHERE id=:id";
       
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Santize and bind property
        $this->id = parent::sanitize($this->id);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    
}