<?php
$conn = new mysqli('localhost','root','','gestionnotes');
if ($conn->connect_errno){
    echo "Connection Failed:".$conn->connect_error;
    exit;
}
?>