<?php 

session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

if(isset($_SESSION['employeeSession'])){
    session_destroy();
    unset($_SESSION['employeeSession']);
    unset($_SESSION['employeeId']);
}
