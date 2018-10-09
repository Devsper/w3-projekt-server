<?php 

require_once('method.php');
require_once('relationship.php');

class Shift extends Method{
    
    private $conn;
    private $tableName = "shift";

    // Properties of class
    public $id;
    public $registerDate;
    public $startTime;
    public $endTime;
    public $employee_Id;

    public $date;
    public $shiftType;
    public $shiftTypeChanged;
    public $relationship = array();

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function readAll(){

        $query = "SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName 
                    FROM employee e, {$this->tableName} s 
                    INNER JOIN {$this->relationship[0]->relTableName} ss ON s.Id = ss.Shift_Id 
                    INNER JOIN {$this->relationship[0]->tableName} st ON ss.{$this->relationship[0]->idColumnName} = st.Id 
                    WHERE e.Id = :userId 
                    AND s.employee_Id = :userId 
                    
                    UNION 
                    
                    SELECT e.Name, s.StartTime, s.EndTime, a.Name
                    FROM employee e, {$this->tableName} s 
                    INNER JOIN {$this->relationship[1]->relTableName} sa ON s.Id = sa.Shift_Id 
                    INNER JOIN {$this->relationship[1]->tableName} a ON sa.{$this->relationship[1]->idColumnName} = a.Id 
                    WHERE e.Id = :userId 
                    AND s.employee_Id = :userId;";

         // prepare query statement
         $stmt = $this->conn->prepare($query);
        
         $this->employee_Id = htmlspecialchars(strip_tags($this->employee_Id));

         // bind values
         $stmt->bindParam(":userId", $this->employee_Id);

         // execute query
         $stmt->execute();
         
         $shiftProp = array_fill_keys(array("name", "startTime", "endTime", "taskName"),"");
         $dataArr = parent::fetchRows($stmt, $shiftProp);

         echo json_encode($dataArr);
    }

    function readAllDate(){

        $query = "SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName
                    FROM employee e, {$this->tableName} s 
                    INNER JOIN {$this->relationship[0]->relTableName} ss ON s.Id = ss.Shift_Id
                    INNER JOIN {$this->relationship[0]->tableName} st ON ss.{$this->relationship[0]->idColumnName} = st.Id
                    WHERE e.Id = :userId
                    AND s.employee_Id = :userId
                    AND YEAR(s.StartTime) = :y
                    AND MONTH(s.StartTime) = :m
                    
                    UNION 

                    SELECT e.Name, s.StartTime, s.EndTime, st.name
                    FROM employee e, {$this->tableName} s 
                    INNER JOIN {$this->relationship[1]->relTableName} ss ON s.Id = ss.Shift_Id
                    INNER JOIN {$this->relationship[1]->tableName} st ON ss.{$this->relationship[1]->idColumnName} = st.Id
                    WHERE e.Id = :userId
                    AND s.employee_Id = :userId
                    AND YEAR(s.StartTime) = :y 
                    AND MONTH(s.StartTime) = :m;";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        $dateArr = explode("-", $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];
        
        $this->employee_Id = htmlspecialchars(strip_tags($this->employee_Id));
        $year = htmlspecialchars(strip_tags($year));
        $month = htmlspecialchars(strip_tags($month));

        // bind values
        $stmt->bindParam(":userId", $this->employee_Id);
        $stmt->bindParam(":y", $year);
        $stmt->bindParam(":m", $month);

        // execute query
        $stmt->execute();
        
        $shiftProp = array_fill_keys(array("name", "startTime", "endTime", "taskName"),"");
        $dataArr = parent::fetchRows($stmt, $shiftProp);

        echo json_encode($dataArr);
    }

    function readSingle(){

        // Get a single shift for a user
        $query = "SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName
                  FROM employee e, {$this->tableName} s 
                  INNER JOIN {$this->relationship[0]->relTableName} ss ON s.Id = ss.Shift_id
                  INNER JOIN {$this->relationship[0]->tableName}  st ON ss.{$this->relationship[0]->idColumnName} = st.Id
                  WHERE e.Id = :userId
                  AND s.employee_Id = :userId
                  AND s.Id = :shiftId
                  
                  UNION

                  SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName
                  FROM employee e, {$this->tableName} s 
                  INNER JOIN {$this->relationship[1]->relTableName} ss ON s.Id = ss.Shift_Id
                  INNER JOIN {$this->relationship[1]->tableName} st ON ss.{$this->relationship[1]->idColumnName} = st.Id
                  WHERE e.Id = :userId
                  AND s.employee_Id = :userId
                  AND s.Id = :shiftId;";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->employee_Id = htmlspecialchars(strip_tags($this->employee_Id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":userId", $this->employee_Id);
        $stmt->bindParam(":shiftId", $this->id);

        // execute query
        $stmt->execute();
        
        
        $shiftProp = array_fill_keys(array("name", "startTime", "endTime", "taskName"),"");
        $dataArr = parent::fetchRows($stmt, $shiftProp);

        echo json_encode($dataArr);
    }

    function create(){

        $idName = $this->relationship[0]->idColumnName;
        $relationshipTable = $this->relationship[0]->relTableName;

        $query = "START TRANSACTION;
                  INSERT INTO {$this->tableName} (StartTime, EndTime, Employee_Id)
                  VALUES (:startTime, :endTime, :employee_Id);
                  SET @last_inserted_id = LAST_INSERT_ID();
                  
                  INSERT INTO {$relationshipTable} (Shift_Id, {$idName}) 
                  VALUES (@last_inserted_id, :relation_Id);
                  COMMIT";

        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->startTime = htmlspecialchars(strip_tags($this->startTime));
        $this->endTime = htmlspecialchars(strip_tags($this->endTime));
        $this->employee_Id = htmlspecialchars(strip_tags($this->employee_Id));
        $this->relationship[0]->relationship_Id = htmlspecialchars(strip_tags($this->relationship[0]->relationship_Id));

        // bind values
        $stmt->bindParam(":startTime", $this->startTime);
        $stmt->bindParam(":endTime", $this->endTime);
        $stmt->bindParam(":employee_Id", $this->employee_Id);
        $stmt->bindParam(":relation_Id", $this->relationship[0]->relationship_Id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    function update(){
        
        if($this->shiftType == "subTask"){
            $idName = $this->relationship[0]->idColumnName;
            $relationshipTable = $this->relationship[0]->relTableName;
            $relationship_Id = $this->relationship[0]->id;
            $deleteTable = $this->relationship[1]->relTableName;
        }

        if($this->shiftType == "assignment"){
            $idName = $this->relationship[1]->idColumnName;
            $relationshipTable = $this->relationship[1]->relTableName;
            $relationship_Id = $this->relationship[1]->id;
            $deleteTable = $this->relationship[0]->relTableName;
        }

        if($this->shiftTypeChanged == true){

            $query = "START TRANSACTION;
                      UPDATE {$this->tableName} s
                      SET s.StartTime = :startTime, s.EndTime = :endTime
                      WHERE s.id = :shift_Id;
                      
                      DELETE FROM {$deleteTable}
                      WHERE Shift_Id = :shift_Id;
                                          
                      INSERT INTO {$relationshipTable}(Shift_Id, {$idName})
                      VALUES (:shift_Id, :relation_Id);
                      COMMIT;";
        }else{

            $query = "UPDATE {$this->tableName} s, {$relationshipTable} rt
                      SET s.StartTime = :startTime, s.EndTime = :endTime, rt.{$idName} = :relation_Id
                      WHERE s.Id = :shift_Id
                      AND rt.Shift_Id = :shift_Id;";
        }
        
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->startTime = htmlspecialchars(strip_tags($this->startTime));
        $this->endTime = htmlspecialchars(strip_tags($this->endTime));
        $relationship_Id = htmlspecialchars(strip_tags($relationship_Id));

        // bind values
        $stmt->bindParam(":startTime", $this->startTime);
        $stmt->bindParam(":endTime", $this->endTime);
        $stmt->bindParam(":shift_Id", $this->id);
        $stmt->bindParam(":relation_Id", $relationship_Id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    public function addRelationshipTables($relationship){

        if($relationship == "subTask"){
            array_push($this->relationship, new Relationship("SubTask_Id", "shift_subtask", "subtask"));
        }

        if($relationship == "assignment"){
            array_push($this->relationship, new Relationship("Assignment_Id", "shift_assignment", "assignment"));
        }

        if($relationship == "all"){
            array_push($this->relationship, new Relationship("SubTask_Id", "shift_subtask", "subtask"));
            array_push($this->relationship, new Relationship("Assignment_Id", "shift_assignment", "assignment"));
        }
    }
}