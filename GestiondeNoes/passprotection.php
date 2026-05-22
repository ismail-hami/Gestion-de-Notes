<?php
session_start();
if(!isset($_SESSION['loggedIN'])){
    require_once("landingURL.php");
    header("Location: login.php");
    exit();
}
?>