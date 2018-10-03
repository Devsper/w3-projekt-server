<?php 

class Shift{
    
    private $conn;
    private $tableName = "shift";

    // Properties of class
    public $id;
    public $registerDate;
    public $startTime;
    public $endTime;
    public $employee_Id;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function get($shiftId = null){
        
        // Fetches all shifts if no parameter is passed else fetch specific shift
        if(empty($shiftId)){
            // select all query
            $query = "SELECT * FROM {$this->tableName}";
        }else{
            $query = "SELECT * FROM {$this->tableName} WHERE {$this->tableName}.Id = $shiftId";
        }
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    function getUserShift($userId){
        
        // Fetches all shifts if no parameter is passed else fetch specific shift
        if(!empty($userId)){
            // select all query
            $query = "SELECT e.Name, s.StartTime, s.EndTime, st.name as SubName
                      FROM employee e, {$this->tableName} s 
                      INNER JOIN shift_subtask ss ON s.Id = ss.Shift_Id
                      INNER JOIN subtask st ON ss.SubTask_Id = st.Id
                      WHERE e.Id = $userId
                      AND s.employee_Id = $userId";
        }
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    function post(){

        $query = "INSERT INTO {$this->tableName}
                  SET StartTime:startTime, EndTime:endTime, Employee_Id:employee_Id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->startTime = htmlspecialchars(strip_tags($this->startTime));
        $this->endTime = htmlspecialchars(strip_tags($this->endTime));
        $this->employee_Id = htmlspecialchars(strip_tags($this->employee_Id));
    
        // bind values
        $stmt->bindParam(":startTime", $this->startTime);
        $stmt->bindParam(":endTime", $this->endTime);
        $stmt->bindParam(":employee_Id", $this->employee_Id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
        }
}