<?php 

require_once('method.php');

class Task extends Method{
    
    private $conn;
    private $tableName = "task";

    // Properties of class
    public $id;
    public $ids = array();
    public $name;
    public $assignment_Id;
    public $employee_Id;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    /**
     * Fetches task from database, single and multiple record
     * 
     * @return array $dataArr Fetched rows from database as associative array.
     */
    function read(){
        
        // If id property is present fetch single record
        if($this->id){

            // SQL Query to fetch a single task 
            $query = "SELECT t.Id, t.Name FROM {$this->tableName} t WHERE t.id = :id";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);
            
            // Santize and bind property
            $this->id = parent::sanitize($this->id);
            $stmt->bindParam(":id", $this->id);
        }else{
            // SQL query to fetch all tasks from specifik assignment
            $query = "SELECT t.Id, t.Name FROM {$this->tableName} t WHERE Assignment_Id = :assignment_Id";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);

            // Sanitize property
            $this->task_id = parent::sanitize($this->assignment_Id);
            
            // Bind property to statement
            $stmt->bindParam(":assignment_Id", $this->assignment_Id);
        }

        // Execute query
        $stmt->execute();

         // Creates associative array with keys to contain values from fetched from database
        $taskProp = array_fill_keys(array("name", "id"),"");
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $taskProp);
    
        return $dataArr;
    }

    /**
     * Updates relationship between Employee and Assignments
     * 
     * @return boolean Update status
     */ 
    function updateEmployeeTasks(){

        $sqlValues = [];

        // Dynamically add values to add into VALUES clause
        for($i = 0; $i< count($this->ids); $i++){
            array_push($sqlValues, "(:employeeId, :taskId".$i.")");
        }

        $query = "START TRANSACTION;
                  DELETE FROM employee_task WHERE Employee_Id = :employeeId;
                  INSERT INTO employee_task (Employee_Id, Task_Id)
                  VALUES"; 
        // Output all values that should be added
        $query .= implode(',', $sqlValues).";";
        $query .= "COMMIT;";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize and bind all assignment values
        for($i = 0; $i< count($this->ids); $i++){
            $this->ids[$i] =  parent::sanitize($this->ids[$i]);
            $stmt->bindParam(":taskId".$i, $this->ids[$i]);
        }

        // Santize and bind property
        $this->employee_Id =  parent::sanitize($this->employee_Id);
        $stmt->bindParam(":employeeId", $this->employee_Id);

        // Execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /**
     * Creates a record of task in database
     * 
     * @return boolean Creation status
     */
    function create(){

        // SQL query to create an task
        $query = "INSERT INTO {$this->tableName} (Name, Assignment_Id) VALUES (:name, :assignment_Id)";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Santize and bind properties
        $this->assignment_Id = parent::sanitize($this->assignment_Id);
        $this->name = parent::sanitize($this->name);
        $stmt->bindParam(":assignment_Id", $this->assignment_Id);
        $stmt->bindParam(":name", $this->name);
                
         // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /**
     * Updates task record in database
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

    /**
     * Fetches all tasks and subtasks for given employee
     * 
     * @return array $dataArr Fetched rows from database as associative array.
     */ 
    function getEmployeeTasks(){
        
        // SQL Query to select all tasks and subtasks 
        $query = "SELECT task.Name as 'Task', subtask.Name as 'Subtask', task.Id as 'TaskId', subtask.Id as 'SubtaskId' 
                  FROM employee
                  INNER JOIN employee_task ON employee.Id = employee_task.Employee_Id
                  INNER JOIN task ON task.Id = employee_task.Task_Id
                  INNER JOIN subtask ON subtask.Task_Id = task.Id
                  WHERE employee.Id = :employee_Id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Sanitizes property
        $this->employee_Id = parent::sanitize($this->employee_Id);

        // Bind property to statement
        $stmt->bindParam(":employee_Id", $this->employee_Id);
    
        // Execute query
        $stmt->execute();
        
        // Creates associative array with keys to contain values from fetched from database
        $shiftProp = array_fill_keys(array("task", "taskId", "subtask", "subtaskId" ),"");
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $shiftProp);

        return $dataArr;
    }
}