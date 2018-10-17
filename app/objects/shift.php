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

    function read(){

        if($this->id){
             // Get a single shift for a user
            $query = $this->getSqlQuery("getSingle");
        }elseif($this->date){
            $query = $this->getSqlQuery("getAllDate");
        }else{
            $query = $this->getSqlQuery("getAll");
        }

         // prepare query statement
        $stmt = $this->conn->prepare($query);

        if($this->id){
           
            $this->id = parent::sanitize($this->id);
            $stmt->bindParam(":shiftId", $this->id);
        }
         
        if($this->date){
            
            $this->date = parent::sanitize($this->date);
            
            $dateArr = explode("-", $this->date);

            $year = $dateArr[0];
            $month = $dateArr[1];

            $stmt->bindParam(":y", $year);
            $stmt->bindParam(":m", $month);
        }

        $this->employee_Id = parent::sanitize($this->employee_Id);
        $stmt->bindParam(":employee_Id", $this->employee_Id);

         // execute query
         $stmt->execute();
         
         $shiftProp = array_fill_keys(array("name", "startTime", "endTime", "taskName"),"");
         $dataArr = parent::fetchRows($stmt, $shiftProp);

         echo json_encode($dataArr);
    }

    function create(){

        $query = "START TRANSACTION;
                  INSERT INTO {$this->tableName} (StartTime, EndTime, Employee_Id)
                  VALUES (:startTime, :endTime, :employee_Id);
                  SET @last_inserted_id = LAST_INSERT_ID();
                  
                  INSERT INTO {$this->relationship[0]->relTableName} (Shift_Id, {$this->relationship[0]->idColumnName}) 
                  VALUES (@last_inserted_id, :relation_Id);
                  COMMIT";

        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->startTime = parent::sanitize($this->startTime);
        $this->endTime = parent::sanitize($this->endTime);
        $this->employee_Id = parent::sanitize($this->employee_Id);
        $this->relationship[0]->relationship_Id = parent::sanitize($this->relationship[0]->relationship_Id);

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
        $this->startTime = parent::sanitize($this->startTime);
        $this->endTime = parent::sanitize($this->endTime);
        $relationship_Id = parent::sanitize($relationship_Id);

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

    function delete(){

        $query = "DELETE FROM {$this->tableName}
                  WHERE id = :shift_Id";

        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = parent::sanitize($this->id);
        
        // Bind
        $stmt->bindParam(":shift_Id", $this->id);

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

    private function getSqlQuery($query){

        switch($query){
            case "getSingle": 

                $string = "SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName
                            FROM employee e, {$this->tableName} s 
                            INNER JOIN {$this->relationship[0]->relTableName} ss ON s.Id = ss.Shift_id
                            INNER JOIN {$this->relationship[0]->tableName}  st ON ss.{$this->relationship[0]->idColumnName} = st.Id
                            WHERE e.Id = :employee_Id
                            AND s.employee_Id = :employee_Id
                            AND s.Id = :shiftId
                            
                            UNION

                            SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName
                            FROM employee e, {$this->tableName} s 
                            INNER JOIN {$this->relationship[1]->relTableName} ss ON s.Id = ss.Shift_Id
                            INNER JOIN {$this->relationship[1]->tableName} st ON ss.{$this->relationship[1]->idColumnName} = st.Id
                            WHERE e.Id = :employee_Id
                            AND s.employee_Id = :employee_Id
                            AND s.Id = :shiftId;";
                
                return $string;
            case "getAll":
                $string = "SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName 
                           FROM employee e, {$this->tableName} s 
                           INNER JOIN {$this->relationship[0]->relTableName} ss ON s.Id = ss.Shift_Id 
                           INNER JOIN {$this->relationship[0]->tableName} st ON ss.{$this->relationship[0]->idColumnName} = st.Id 
                           WHERE e.Id = :employee_Id 
                           AND s.employee_Id = :employee_Id 
                           
                           UNION 
                           
                           SELECT e.Name, s.StartTime, s.EndTime, a.Name
                           FROM employee e, {$this->tableName} s 
                           INNER JOIN {$this->relationship[1]->relTableName} sa ON s.Id = sa.Shift_Id 
                           INNER JOIN {$this->relationship[1]->tableName} a ON sa.{$this->relationship[1]->idColumnName} = a.Id 
                           WHERE e.Id = :employee_Id 
                           AND s.employee_Id = :employee_Id;";

                return $string;
                break;
            case "getAllDate":

                $string = "SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName
                           FROM employee e, {$this->tableName} s 
                           INNER JOIN {$this->relationship[0]->relTableName} ss ON s.Id = ss.Shift_Id
                           INNER JOIN {$this->relationship[0]->tableName} st ON ss.{$this->relationship[0]->idColumnName} = st.Id
                           WHERE e.Id = :employee_Id
                           AND s.employee_Id = :employee_Id
                           AND YEAR(s.StartTime) = :y
                           AND MONTH(s.StartTime) = :m
                           
                           UNION   
   
                           SELECT e.Name, s.StartTime, s.EndTime, st.name
                           FROM employee e, {$this->tableName} s 
                           INNER JOIN {$this->relationship[1]->relTableName} ss ON s.Id = ss.Shift_Id
                           INNER JOIN {$this->relationship[1]->tableName} st ON ss.{$this->relationship[1]->idColumnName} = st.Id
                           WHERE e.Id = :employee_Id
                           AND s.employee_Id = :employee_Id
                           AND YEAR(s.StartTime) = :y 
                           AND MONTH(s.StartTime) = :m;";

                return $string;
                break;
            default: 
                return;
        }


    }
}