<?php 
	session_start();
	require('new-connection.php');
	if(!isset($_SESSION['logged_in'])) 
	{
		$_SESSION['errors'][] = "Oops! You have to be logged in to view this page.";
			header('location: index.php');
		die();
	}
	if(isset($_SESSION['errors'])) 
	{
		echo "<div class='alert error'>{$_SESSION['errors']}</div>";
		unset($_SESSION['errors']);
	}
	if(isset($_SESSION['success_message'])) 
	{
		echo "<div class='alert success'>{$_SESSION['success_message']}</div>";
		unset($_SESSION['success_message']);
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>The Wall</title>
	<link rel="stylesheet" href="style.css">
	<!-- jQuery 1.11.2 -->
	<script src="jquery-1.11.0.min.js"></script>
</head>
<body>
	<script>
		$('div.alert').delay('3500').fadeOut();
	</script>
	<div class="header">
		<a href='wall.php'><h3>The Wall</h3></a>
		<p> Hello <a href="profile.php"><?= $_SESSION['first_name']; ?></a></p>
		<a href='logout.php'><p>Logout</p></a>
		<a href='wall.php'><p>Post</p></a>
	</div>
	<div class="body">
		<div class="col-30">
			<h4 class="header">Picture</h4>
			<img class="profile-pic" src="uploads/<?=$_SESSION['profile_img']?>.jpg">
			<p><a href="upload_photo.php">Change Image</a></p>
		</div>
		<div class="col-70">
			<h4 class="header">Information</h4>
			<h5>Account Info:</h5>
			<label>Name:</label>
			<p><?= $_SESSION['user_name']; ?></p><br>
			<label>Member Since:</label>
			<p><?= $_SESSION['member_since']; ?></p>
			<h5>Contact Info:</h5>
			<label>Email:</label>
			<p><?= $_SESSION['email']; ?></p>
		</div>
	</div>
</body>
</html>