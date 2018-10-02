<?php 

/**
 * Fetches rows from chosen table in database
 *
 * @param Object $obj - Object representing table to fetch from
 * @param Array $objProp - Object properties representing columns in database
 */
function get($obj, $objProp){   

    // If parameter is available fetch id from database
    if(!empty($_GET['id'])){

        // Uses parameter to fetch single object
        $stmt = $obj->get($_GET['id']);
    }else{
        // Fetches all objects
        $stmt = $obj->get();
    }

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

    // Send back JSON-response
    echo json_encode($dataArr);

}