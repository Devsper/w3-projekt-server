<?php 

class Method{

    protected function fetchRows($stmt, $objProp){   

        // Creates arrays to send as JSON
        $dataArr = array();
        $dataArr["data"] = array();
    
        // Fetches all rows
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    
            foreach($objProp as $key => $value){
                // Add value of column to associative array
                // $row keys has same name as objProp keys just starting capital letter
                $objProp[$key] = $row[ucwords($key)];
            }
    
            // Add row to array
            array_push($dataArr["data"], $objProp);
        }
    
        return $dataArr;
    }
}