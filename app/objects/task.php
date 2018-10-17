<?php 

class Task extends Method{
    
    private $conn;
    private $tableName = "task";

    // Properties of class
    public $id;
    public $name;
    public $assignment_Id;
    public $relationships = array();

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){
        
        if($this->id){

            $query = "SELECT s.Id, s.Name FROM {$this->tableName} s WHERE t.Assignment_Id = :assignment_Id AND t.id = :id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            $this->task_id = htmlspecialchars(strip_tags($this->assignment_Id));
            $this->id = htmlspecialchars(strip_tags($this->id));
            // bind values
            $stmt->bindParam(":assignment_Id", $this->assignment_Id);
            $stmt->bindParam(":id", $this->id);
        }else{
            $query = "SELECT s.Id, s.Name FROM {$this->tableName} s WHERE Assignment_Id = :assignment_Id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            $this->task_id = htmlspecialchars(strip_tags($this->assignment_Id));
            // bind values
            $stmt->bindParam(":assignment_Id", $this->assignment_Id);
        }

        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    function create(){

        $query = "INSERT INTO {$this->tableName} (Name, Assignment_Id) VALUES (':name', ':assignment_Id')";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->assignment_Id = htmlspecialchars(strip_tags($this->task_id));
        $this->name = htmlspecialchars(strip_tags($this->name));

        // bind values
        $stmt->bindParam(":assignment_Id", $this->assignment_Id);
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

    function getEmployeeTasks(){
        
        $query = "SELECT task.Name as 'Task', subtask.Name as 'Subtask'
                  FROM employee
                  INNER JOIN employee_task ON employee.Id = employee_task.Employee_Id
                  INNER JOIN task ON task.Id = employee_task.Task_Id
                  INNER JOIN subtask ON subtask.Task_Id = task.Id
                  WHERE employee.Id = :employee_Id";
        

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        $employeeId = htmlspecialchars(strip_tags($_SESSION['employeeId']));

        // bind values
        $stmt->bindParam(":employee_Id", $employeeId);
    
        // execute query
        $stmt->execute();
        
        $shiftProp = array_fill_keys(array("task", "subtask"),"");
        $dataArr = parent::fetchRows($stmt, $shiftProp);

        return $dataArr;
    }
}