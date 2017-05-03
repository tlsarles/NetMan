<?php
session_start();
if($_SERVER['REMOTE_ADDR'] == "10.1.14.115") $_SESSION['privilege'] = "admin";
include 'dbcon.php';
include 'bootstrap.php';
?>