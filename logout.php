<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';

$db   = new Database();
$conn = $db->getConnection();
$auth = new Auth($conn);
$auth->logout();
?>
