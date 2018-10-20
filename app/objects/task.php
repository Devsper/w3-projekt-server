<?php 

require_once('method.php');

class Task extends Method{
    
    private $conn;
    private $tableName = "task";

    // Properties of class
    public $id;
    public $name;
    public $assignment_Id;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){
        
        if($this->id){

            $query = "SELECT t.Id, t.Name FROM {$this->tableName} t WHERE t.Assignment_Id = :assignment_Id AND t.id = :id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            $this->task_id = parent::sanitize($this->assignment_Id);
            $this->id = parent::sanitize($this->id);
            // bind values
            $stmt->bindParam(":assignment_Id", $this->assignment_Id);
            $stmt->bindParam(":id", $this->id);
        }else{
            $query = "SELECT t.Id, t.Name FROM {$this->tableName} t WHERE Assignment_Id = :assignment_Id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            $this->task_id = parent::sanitize($this->assignment_Id);
            // bind values
            $stmt->bindParam(":assignment_Id", $this->assignment_Id);
        }

        // execute query
        $stmt->execute();

        $taskProp = array_fill_keys(array("name", "id"),"");
        $dataArr = parent::fetchRows($stmt, $taskProp);
    
        return $dataArr;
    }
    function create(){

        $query = "INSERT INTO {$this->tableName} (Name, Assignment_Id) VALUES (:name, :assignment_Id)";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->assignment_Id = parent::sanitize($this->assignment_Id);
        $this->name = parent::sanitize($this->name);

        // bind values
        $stmt->bindParam(":assignment_Id", $this->assignment_Id);
        $stmt->bindParam(":name", $this->name);
        // execute query
        $stmt->execute();
        
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
    
        $this->id = parent::sanitize($this->id);
        $this->name = parent::sanitize($this->name);

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
    
        $this->id = parent::sanitize($this->id);

        // bind values
        $stmt->bindParam(":id", $this->id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function getEmployeeTasks(){
        
        $query = "SELECT task.Name as 'Task', subtask.Name as 'Subtask', task.Id as 'TaskId', subtask.Id as 'SubtaskId' 
                  FROM employee
                  INNER JOIN employee_task ON employee.Id = employee_task.Employee_Id
                  INNER JOIN task ON task.Id = employee_task.Task_Id
                  INNER JOIN subtask ON subtask.Task_Id = task.Id
                  WHERE employee.Id = :employee_Id";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        $employeeId = parent::sanitize($_SESSION['employeeId']);

        // bind values
        $stmt->bindParam(":employee_Id", $employeeId);
    
        // execute query
        $stmt->execute();
        
        $shiftProp = array_fill_keys(array("task", "subtask", ),"");
        $dataArr = parent::fetchRows($stmt, $shiftProp);

        return $dataArr;
    }
}