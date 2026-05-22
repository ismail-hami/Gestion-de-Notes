<?php
require_once "passprotection.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: pages/403.php");
    exit();
}
