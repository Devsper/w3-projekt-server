<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

if(isset($_GET['logout'])){
    
    $res = array(
        "message" => "success",
    );

    echo json_encode($res);
}else{

    $res = array(
        "message" => "failed"
    );

    echo json_encode($res);
}
