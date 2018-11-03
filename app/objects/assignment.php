<?php 

require_once('method.php');

class Assignment extends Method{
    
    private $conn;
    private $tableName = "assignment";

    // Properties of class
    public $id;
    public $ids = array();
    public $name;
    public $employee_Id;

    // Constructs a database connection
    public function __construct($db){
        $this->conn = $db;
    }

    /**
     * Fetches all assignment for given employee
     * 
     * @return array $dataArr Fetched rows from database as associative array.
     */ 
    function getEmployeeAssignments(){
				
        // SQL query to select all assignments for given employee. 
        // Adds extra column if assignment has tasks 
        $query = "SELECT a.Name, a.Id, 
                  (CASE WHEN a.Id = t.Assignment_Id THEN 'True' ELSE 'False' END) as 'HasTasks'
                  FROM assignment a
                  INNER JOIN task t
                  INNER JOIN employee_assignment ON a.id = employee_assignment.Assignment_Id 
                  INNER JOIN employee ON employee.Id = employee_assignment.Employee_Id 
                  WHERE employee.Id = :employeeId
                  GROUP BY a.Name";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);
				
        // Santize and bind property
        $this->employee_Id =  parent::sanitize($this->employee_Id);
        $stmt->bindParam(":employeeId", $this->employee_Id);
    
        // Execute query
        $stmt->execute();
			 
        // Creates associative array with keys to contain values from fetched from database
        $employeeProp = array_fill_keys(array("name", "id", "hasTasks"),"");
        
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $employeeProp);
        
        // Return associative array
        return $dataArr;
    }

    /**
     * Fetches all assignment and its tasks for given employee
     * 
     * @return array $dataArr Fetched rows from database as associative array.
     */ 
    function getEmployeeAssignmentTasks(){
        
        // SQL query to select all assignments and its tasks for given employee. 
        $query = "SELECT a.Name as 'AssignmentName', a.Id as 'AssignmentId', t.Name as 'TaskName', t.Id as 'TaskId'
                  FROM assignment a 
                  LEFT JOIN employee_assignment ea ON a.Id = ea.Assignment_Id 
                  LEFT JOIN employee e ON e.Id = ea.Employee_Id 
                  LEFT JOIN task t ON t.Assignment_Id = a.Id 
                  WHERE e.Id = :employeeId
                  GROUP BY a.Name, a.Id, t.Name, t.Id
                  ORDER BY a.Name";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);
				
        // Santize and bind property
        $this->employee_Id =  parent::sanitize($this->employee_Id);
        $stmt->bindParam(":employeeId", $this->employee_Id);
    
        // Execute query
        $stmt->execute();
			 
        // Creates associative array with keys to contain values from fetched from database
        $employeeProp = array_fill_keys(array("assignmentName", "assignmentId", "taskName", "taskId"),"");
        
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $employeeProp, false); 

        // Return associative array
        return $dataArr;
    }

    /**
     * Updates relationship between Employee and Assignments
     * 
     * @return boolean Update status
     */ 
    function updateEmployeeAssignments(){

        $sqlValues = [];

        // Dynamically add values to add into VALUES clause
        for($i = 0; $i< count($this->ids); $i++){
            array_push($sqlValues, "(:employeeId, :assignmentId".$i.")");
        }

        $query = "START TRANSACTION;
                  DELETE FROM employee_assignment WHERE Employee_Id = :employeeId;
                  INSERT INTO employee_assignment (Employee_Id, Assignment_Id)
                  VALUES"; 
        // Output all values that should be added
        $query .= implode(',', $sqlValues).";";
        $query .= "COMMIT;";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize and bind all assignment values
        for($i = 0; $i< count($this->ids); $i++){
            $this->ids[$i] =  parent::sanitize($this->ids[$i]);
            $stmt->bindParam(":assignmentId".$i, $this->ids[$i]);
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
     * Fetches assignments from database, single and multiple record
     * 
     * @return array $dataArr Fetched rows from database as associative array.
     */
    function read(){

        // If id property is present fetch single record
        if($this->id){
						
			// SQL Query to fetch a single assignment 
            $query = "SELECT a.Id, a.Name FROM {$this->tableName} a WHERE a.id = :id";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);
            
            // Santize and bind property
            $this->id = parent::sanitize($this->id);
            $stmt->bindParam(":id", $this->id);

        }else{
        	// SQL query to fetch all assignment records if no id is present 
            $query = "SELECT a.Id, a.Name FROM {$this->tableName} a";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);
        }

        // Execute query
        $stmt->execute();
        
        // Creates associative array with keys to contain values from fetched from database 				    
        $assignmentProp = array_fill_keys(array("name", "id"),"");
        // Populate array with values from database
        $dataArr = parent::fetchRows($stmt, $assignmentProp);
				
		// Return associative array    
        return $dataArr;
    }
    
    /**
     * Creates a record of assignment in database
     * 
     * @return boolean Creation status
     */
    function create(){
				
		// SQL query to create an assignment
        $query = "INSERT INTO {$this->tableName} (Name) VALUES (:name)";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
            
        // Santize and bind property
        $this->name = parent::sanitize($this->name);
        $stmt->bindParam(":name", $this->name);
         
        // Execute query
         if($stmt->execute()){
            return true;
        }
        return false;
    }
    
    /**
     * Updates assignment record in database
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

    function formatAssignmentTasks($arr){
        
        $regroupArr = array();

        // Fetches unique assignment names and their ids
        $uniqueNames = array_unique(array_column($arr, 'assignmentName'));
        $uniqueIds = array_unique(array_column($arr, 'assignmentId'));

        $i = 0;

        // Adds AssignmentNames to own array
        foreach($uniqueNames as $value){
            $regroupArr[$i]["name"] = $value;
            $i++;
        }

        $i = 0;

        // Adds assignment id and array of tasks to same array as above
        foreach($uniqueIds as $value){
            $regroupArr[$i]["id"] = $value;
            $regroupArr[$i]["tasks"] = array();
            $i++;
        }

        // Adds tasks to corresponding assignment
        foreach($arr as $row){
            
            if(is_null($row["taskName"])){
                continue;
            }

            // Creates task from current row
            $task = array();
            $task["name"] = $row["taskName"];
            $task["id"] = $row["taskId"];

            // Adds task to corrent assignment
            $key = array_search($row["assignmentName"], array_column($regroupArr, 'name'));
            array_push($regroupArr[$key]["tasks"], $task);
        }

        // Sorts array after amount of tasks
        array_multisort(array_map(function($element) {
            return count($element["tasks"]);
        }, $regroupArr), SORT_DESC, $regroupArr);

        return $regroupArr;
    }
}
