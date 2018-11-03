<?php 

class Method{

    /**
     * Fetches all assignment for given employee
     * @param object PDO statement that holds result from database
     * @param array $objProp associative array with object properties to be filled with database result
     * @param boolean $wrapData if fetched rows should be wrapped in an extra "data" array
     * @return array $dataArr Fetched rows from database as associative array.
     */ 
    protected function fetchRows($stmt, $objProp, $wrapData = true){   

        // Creates arrays to send as JSON
        $dataArr = array();

        // Wrap result in extra array
        if($wrapData){
            $dataArr["data"] = array();
        }

        // Work through all rows from statement
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            
            // Add values from database to associative array
            foreach($objProp as $key => $value){

                // Add value of column to associative array
                // $row keys has same name as objProp keys just starting capital letter
                $objProp[$key] = $row[ucwords($key)];
            }
    
            // Add row to array
            if($wrapData){
                array_push($dataArr["data"], $objProp);
            }else{
                array_push($dataArr, $objProp);
            }
        }
    
        return $dataArr;
    }

    /**
     * Sanitize values
     * 
     * @param mixed $input values to be sanitized
     * @return mixed sanitized values
     */ 
    protected function sanitize($input){

        return htmlspecialchars(strip_tags($input));
    }
}