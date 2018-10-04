<?php 

require_once('method.php');

class Shift extends Method{
    
    private $conn;
    private $tableName = "shift";

    // Properties of class
    public $id;
    public $registerDate;
    public $startTime;
    public $endTime;
    public $employee_Id;
    public $relationshipTable;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function get($shiftId){
        
        // Fetches all shifts if no parameter is passed else fetch specific shift
        if(!empty($shiftId)){
            // select all query
            $query = "SELECT * FROM {$this->tableName} WHERE {$this->tableName}.Id = $shiftId";
            
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // execute query
            $stmt->execute();
            
            $shiftProp = array_fill_keys(array("id", "startTime", "endTime"),"");
            $dataArr = parent::fetchRows($stmt, $shiftProp);

            echo json_encode($dataArr);
        }

        return; 
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

        $shiftProp = array_fill_keys(array("name", "startTime", "endTime", "subName"),"");

        $dataArr = parent::fetchRows($stmt, $shiftProp);

        echo json_encode($dataArr);
    }

    function post(){

        if($this->relationshipTable == "shift_subtask"){
            $idName = "SubTask_Id";
        }

        if($this->relationshipTable == "shift_assignment"){
            $idName = "Assignment_Id";
        }

        $query = "START TRANSACTION;
                  INSERT INTO {$this->tableName} (StartTime, EndTime, Employee_Id)
                  VALUES (:startTime, :endTime, :employee_Id);
                  SET @last_inserted_id = LAST_INSERT_ID();
                  
                  INSERT INTO {$this->relationshipTable} (Shift_Id, {$idName}) 
                  VALUES (@last_inserted_id, :relation_Id);
                  COMMIT";

        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->startTime = htmlspecialchars(strip_tags($this->startTime));
        $this->endTime = htmlspecialchars(strip_tags($this->endTime));
        $this->employee_Id = htmlspecialchars(strip_tags($this->employee_Id));
        $relationId = htmlspecialchars(strip_tags(3));

        // bind values
        $stmt->bindParam(":startTime", $this->startTime);
        $stmt->bindParam(":endTime", $this->endTime);
        $stmt->bindParam(":employee_Id", $this->employee_Id);
        $stmt->bindParam(":relation_Id", $relationId);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
        }
}