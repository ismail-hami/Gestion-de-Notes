<?php
require_once "passprotection.php";

if ($_SESSION['role'] !== 'user') {
    header("Location: pages/403.php");
    exit();
}
