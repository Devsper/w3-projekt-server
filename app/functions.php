<?php 

function get($obj, $objProp){   

    // If parameter is available fetch id from database
    if(!empty($_GET['id'])){

        // Uses parameter to fetch single object
        $stmt = $obj->get($_GET['id']);
    }else{
        // Fetches all objects
        $stmt = $obj->get();
    }

    $dataArr = array();
    $dataArr["data"] = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        $item = array();

        foreach($objProp as $key => $value){

            $objProp[$key] = $row[ucwords($key)];
        }

        array_push($dataArr["data"], $objProp);
    }

    echo json_encode($dataArr);

}