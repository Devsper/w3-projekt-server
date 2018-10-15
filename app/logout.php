<?php 

session_destroy();
unset($_SESSION['employeeSession']);
header("Location: login.php");