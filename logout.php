<?php 
	session_start();
	session_destroy();
	$_SESSION['success_message'] = "You have been logged out successfully!";
	header('location: index.php');
	die();
?>