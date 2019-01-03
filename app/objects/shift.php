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

    /**
     * Fetches shifts from database, single and multiple record
     * 
     * @return array $dataArr Fetched rows from database as associative array.
     */
    function read(){

        // If id property is present fetch single record
        if($this->id){

            // SQL query for a single shift, 
            // relationship is unknown so fetch from both relationship tables with UNION
            $query = "SELECT e.Name, s.StartTime, s.EndTime, st.name as TaskName
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
                      AND s.Id = :shiftId";

        }elseif($this->date){

            // SQL query to search shift by date
            // fetch shift from both assignment and subtasks so UNION table is needed
            $query = "SELECT e.Name, s.Id, s.StartTime, s.EndTime, st.name as TaskName
                      FROM employee e, {$this->tableName} s 
                      INNER JOIN {$this->relationship[0]->relTableName} ss ON s.Id = ss.Shift_Id
                      INNER JOIN {$this->relationship[0]->tableName} st ON ss.{$this->relationship[0]->idColumnName} = st.Id
                      WHERE e.Id = :employee_Id
                      AND s.employee_Id = :employee_Id
                      AND YEAR(s.StartTime) = :y
                      AND MONTH(s.StartTime) = :m
                      
                      UNION   
              
                      SELECT e.Name, s.Id, s.StartTime, s.EndTime, a.name
                      FROM employee e, {$this->tableName} s 
                      INNER JOIN {$this->relationship[1]->relTableName} sa ON s.Id = sa.Shift_Id
                      INNER JOIN {$this->relationship[1]->tableName} a ON sa.{$this->relationship[1]->idColumnName} = a.Id
                      WHERE e.Id = :employee_Id
                      AND s.employee_Id = :employee_Id
                      AND YEAR(s.StartTime) = :y 
                      AND MONTH(s.StartTime) = :m
                      ORDER BY StartTime DESC";
        }

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        if($this->id){
            
            // Santize and bind property
            $this->id = parent::sanitize($this->id);
            $stmt->bindParam(":shiftId", $this->id);
        }
         
        if($this->date){
            
            // Sanitizes property
            $this->date = parent::sanitize($this->date);
            
            // Format date to be bound to statement
            $dateArr = explode("-", $this->date);
            $year = $dateArr[0];
            $month = $dateArr[1];

            // Bind properties to statement
            $stmt->bindParam(":y", $year);
            $stmt->bindParam(":m", $month);
        }

        // Santize and bind property
        $this->employee_Id = parent::sanitize($this->employee_Id);
        $stmt->bindParam(":employee_Id", $this->employee_Id);

         // Execute query
         $stmt->execute();
         
         // Creates associative array with keys to contain values from fetched from database
         $shiftProp = array_fill_keys(array("id", "name", "startTime", "endTime", "taskName"),"");
         // Populate array with values from database
         $dataArr = parent::fetchRows($stmt, $shiftProp);

         return $dataArr;
    }

    /**
     * Creates a record of shift in database
     * 
     * @return boolean Creation status
     */
    function create(){

        // SQL query to create an shift
        // Adds to both shift table and relationship table with a transaction
        $query = "START TRANSACTION;
                  INSERT INTO {$this->tableName} (StartTime, EndTime, Employee_Id)
                  VALUES (:startTime, :endTime, :employee_Id);
                  SET @last_inserted_id = LAST_INSERT_ID();
                  
                  INSERT INTO {$this->relationship[0]->relTableName} (Shift_Id, {$this->relationship[0]->idColumnName}) 
                  VALUES (@last_inserted_id, :relation_Id);
                  COMMIT";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Santize and bind properties
        $this->startTime = parent::sanitize($this->startTime);
        $this->endTime = parent::sanitize($this->endTime);
        $this->employee_Id = parent::sanitize($this->employee_Id);
        $this->relationship[0]->id = parent::sanitize($this->relationship[0]->id);
        $stmt->bindParam(":startTime", $this->startTime);
        $stmt->bindParam(":endTime", $this->endTime);
        $stmt->bindParam(":employee_Id", $this->employee_Id);
        $stmt->bindParam(":relation_Id", $this->relationship[0]->id);

        // Execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    /**
     * Updates shift record in database
     * 
     * @return boolean Update status
     */
    function update(){
        
        // Checks which type of shift is being updated
        if($this->shiftType == "subtask"){
            // Adds values for relationship table subtask to be used in SQL-statement
            $idName = $this->relationship[0]->idColumnName;
            $relationshipTable = $this->relationship[0]->relTableName;
            $relationship_Id = $this->relationship[0]->id;
            $deleteTable = $this->relationship[1]->relTableName;
        }

        if($this->shiftType == "assignment"){
            // Adds values for relationship table assignment to be used in SQL-statement
            $idName = $this->relationship[1]->idColumnName;
            $relationshipTable = $this->relationship[1]->relTableName;
            $relationship_Id = $this->relationship[1]->id;
            $deleteTable = $this->relationship[0]->relTableName;
        }

        // Check if shift type has changed from previous state
        // Different SQL statements are needed depending on value 
        if($this->shiftTypeChanged == true){

            // If type has changed remove old relationship and add new and update shift
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
            // If type has not changed update current shift
            $query = "UPDATE {$this->tableName} s, {$relationshipTable} rt
                      SET s.StartTime = :startTime, s.EndTime = :endTime, rt.{$idName} = :relation_Id
                      WHERE s.Id = :shift_Id
                      AND rt.Shift_Id = :shift_Id;";
        }
        
         // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Santize and bind properties
        $this->startTime = parent::sanitize($this->startTime);
        $this->endTime = parent::sanitize($this->endTime);
        $relationship_Id = parent::sanitize($relationship_Id);
        $stmt->bindParam(":startTime", $this->startTime);
        $stmt->bindParam(":endTime", $this->endTime);
        $stmt->bindParam(":shift_Id", $this->id);
        $stmt->bindParam(":relation_Id", $relationship_Id);
    
        // Execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    /**
     * Deletes shift record in database
     * 
     * @return boolean Delete status
     */   
    function delete(){

        // SQL query to delete given record, 
        // deletion of relationships is handled by database
        $query = "DELETE FROM {$this->tableName}
                  WHERE id = :shift_Id";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Santize and bind property
        $this->id = parent::sanitize($this->id);
        $stmt->bindParam(":shift_Id", $this->id);

        // Execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    /**
     * Fetches all shifts for given month for every employee
     * 
     * @return array $dataArr Fetched rows from database as associative array.
     */ 
    function getAllEmployeeShifts(){

        // SQL query to get shifts by date, add extra column for total hours each shift
        // Needs to fetch from both relationships so UNION table is needed
        $query = "SELECT e.Name, e.Username, s.StartTime, s.EndTime, st.name as TaskName, TRUNCATE(TIMESTAMPDIFF(SECOND, s.StartTime, s.EndTime) / 3600, 2) as 'ShiftHours' 
                  FROM employee e, shift s 
                  INNER JOIN shift_subtask ss ON s.Id = ss.Shift_Id 
                  INNER JOIN subtask st ON ss.Subtask_Id = st.Id 
                  WHERE e.Id = s.employee_Id
                  AND YEAR(s.StartTime) = :y
                  AND MONTH(s.StartTime) = :m
                  
                  UNION 
                  
                  SELECT e.Name, e.Username, s.StartTime, s.EndTime, a.Name, TRUNCATE(TIMESTAMPDIFF(SECOND, s.StartTime, s.EndTime) / 3600, 2) as 'ShiftHours'
                  FROM employee e, shift s 
                  INNER JOIN shift_assignment sa ON s.Id = sa.Shift_Id 
                  INNER JOIN assignment a ON sa.Assignment_Id = a.Id 
                  WHERE e.Id = s.employee_Id
                  AND YEAR(s.StartTime) = :y
                  AND MONTH(s.StartTime) = :m
                  ORDER BY Username, StartTime DESC";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize properties
        $this->date = parent::sanitize($this->date);
        
        // Format date to be bound to statement
        $dateArr = explode("-", $this->date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        // Bind properties to statement
        $stmt->bindParam(":y", $year);
        $stmt->bindParam(":m", $month);

        // Execute query
        $stmt->execute();

        // Creates associative array with keys to contain values from fetched from database
        $shiftProp = array_fill_keys(array("name", "username", "startTime", "endTime", "taskName", "shiftHours"),"");
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $shiftProp, false);

        return $dataArr;
    }

    /**
     * Adds relationships to object to be used in SQL statement
     * 
     * @param string $relationship which relationship(s) to add to object
     */ 
    function addRelationshipTables($relationship){

        if($relationship == "subtask"){
            array_push($this->relationship, new Relationship("Subtask_Id", "shift_subtask", "subtask"));
        }

        if($relationship == "assignment"){
            array_push($this->relationship, new Relationship("Assignment_Id", "shift_assignment", "assignment"));
        }

        if($relationship == "all"){
            array_push($this->relationship, new Relationship("Subtask_Id", "shift_subtask", "subtask"));
            array_push($this->relationship, new Relationship("Assignment_Id", "shift_assignment", "assignment"));
        }
    }

    /**
     * Calculates total hours for each employee for the given month
     * 
     * @param array $shiftArr Array of arrays each array represent an employee
     * @return array $shiftArr Array of arrays with added key value pair of totalhours  
     */ 
    function calculateTotalHours($shiftArr){

        // Callback function to summarize shift hours
        function sum($carry, $item){ 
            return $carry += $item['shiftHours'];
        }

        // Calculates hours for each employee
        foreach($shiftArr as $key => $value){

            $arr = $shiftArr[$key]['shifts'];
            $shiftArr[$key]['totalHours'] = array_reduce($arr, 'sum');
        }
        
        return $shiftArr;
    }

    /**
     * Group fetched shifts by employee
     * 
     * @param array $shiftArr All employee shifts for specific month
     * @return array $regroupArr Array of arrays, one array for each employee with shifts
     */ 
    function groupShiftsByEmployee($shifts){

        // Declare first employee
        $currentUsername = $shifts[0]['username'];
        $previousUsername = $shifts[0]['username'];
        $index = 0;

        // Container array that will hold all employees and shifts
        $regroupArr = array(
                array(
                'name' => $shifts[0]['name'],
                'username' => $shifts[0]['username'],
                'shifts' => array()
            ));
        // Temporary array to hold shifts
        $tempArr = array();

        // Loop through each shift
        foreach($shifts as $key => $value){
            
             // Username of the current iteration
            $currentUsername = $shifts[$key]['username'];

            // If the employee username has changed 
            if($currentUsername != $previousUsername){

                $index++;

                // Create new array for nextt employee
                array_push($regroupArr, array());
                $regroupArr[$index]['name'] = $shifts[$key]['name'];
                $regroupArr[$index]['username'] = $shifts[$key]['username'];
                $regroupArr[$index]['shifts'] = array();
                $tempArr = array();
            }

            // Removes keys from shift array
            unset($shifts[$key]['name']);
            unset($shifts[$key]['username']);
            // Add shifts to employee
            array_push($tempArr, $shifts[$key]);
            $regroupArr[$index]['shifts'] = $tempArr;
            $previousUsername = $currentUsername;
        }

        return $regroupArr;

    }
}