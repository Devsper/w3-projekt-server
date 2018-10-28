<?php 

require_once('method.php');

class Assignment extends Method{
    
    private $conn;
    private $tableName = "assignment";

    // Properties of class
    public $id;
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
        $query = "SELECT DISTINCT a.Name, a.Id, 
                  (CASE WHEN a.Id = t.Assignment_Id THEN 'True' ELSE 'False' END) as 'HasTasks'
                  FROM assignment a
                  INNER JOIN task t
                  INNER JOIN employee_assignment ON a.id = employee_assignment.Assignment_Id 
                  INNER JOIN employee ON employee.Id = employee_assignment.Employee_Id 
                  WHERE employee.Id = :employeeId";

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
}
