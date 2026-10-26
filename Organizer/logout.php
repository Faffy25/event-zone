<?php
session_start(); 

session_unset(); 
session_destroy(); 

header("Location: /event-zone/Event-Management/Goer/home.php");
exit(); 
?>