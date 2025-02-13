<?php
require 'includes/db.php';

// Destroy all session data
$_SESSION = array();
unset($_SESSION['user_id']);
unset($_SESSION['username']);

session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
