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
		$('div.alert').fadeOut(3000, function() {
			
		});
	</script>
	<div class="header">
		<a href='wall.php'><h3>The Wall</h3></a>
		<p> Welcome <a href="profile.php"><?php echo $_SESSION['first_name']; ?></a></p>
		<a href='logout.php'><p>Logout</p></a>
	</div>
	<div class="body">
		<h4>Post a message</h4>
		<form action="proccess.php" method="post">
			<textarea type="text" class="message" name="message"></textarea>
			<input type="hidden" name="action" value="post_message">
			<div class="align-right">
				<button class="align-right" type="submit">Post a message</button>
			</div>
		</form>
<?php 
		// Fetches messages from database
		$query = "SELECT * FROM messages JOIN users ON users.id = messages.user_id ORDER BY messages.created_at DESC";
		$messages = fetch_all($query);
		// Loops through messages
		foreach ($messages as $message) 
		{ 
?>		
			<div class="messages">
				<img class="profile-pic tiny" src="uploads/<?= $message['profile_img']?>.jpg">
				<h4><?= $message['first_name'] . ' ' . $message['last_name'] ?> on <?= $message['created_at']?></h4>
				<p class="message"><?= $message['message'] ?></p>	
				<div class="comments-container">
<?php 
				// Fetches comments from database
				$query = "SELECT * FROM comments LEFT JOIN users ON users.id = comments.user_id WHERE comments.message_id = {$message['id']}";
				$comments = fetch_all($query);
				// Loops through comments
				foreach ($comments as $comment) 
				{ 
	?>			<div class='comments-box'>
						<img class="profile-pic tiny" src="uploads/<?= $comment['profile_img']?>.jpg">
						<h4>Comment from <?= $comment['first_name'] .' '. $comment['last_name'] ?> on <?= $comment['created_at'] ?></h4>
						<p><?= $comment['comment'] ?></p>
						<form action="proccess.php" method="post">
							<input type="hidden" name="action" value="delete_comment">
							<input type="hidden" name="authorization" value="<?= $comment['user_id'] ?>">
							<input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
							<button class="btn-delete" type="submit">Delete Comment</button>
						</form>
					</div>
<?php	
				}
?>
					<!-- Display comment box below each message -->
					<form action="proccess.php" method="post">
						<h5>Post a comment</h5>
						<textarea type="text" class="comment" name="comment"></textarea>
						<input type="hidden" name="action" value="post_comment">
						<input type="hidden" name="message_id" value="<?= $message['id'] ?>">
						<div class="align-right">
							<button class="align-right" type="submit">Post a comment</button>
						</div>
					</form>
				</div>
			</div>
<?php
		}
?>
	</div>
</body>
</html>