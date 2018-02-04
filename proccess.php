<?php 
	session_start();
	require('new-connection.php');

	if(isset($_POST['action']) && $_POST['action'] == 'register') 
	{
		//call to function
		register_user($_POST); 
	}

	elseif(isset($_POST['action']) && $_POST['action'] == 'login') 
	{
		//call to function
		login_user($_POST);
	}

	if(isset($_POST['action']) && $_POST['action'] == 'post_message') 
	{
		post_message($_POST, $_SESSION);
	}

	if(isset($_POST['action']) && $_POST['action'] == 'post_comment') 
	{
		post_comment($_POST, $_SESSION);
	}

	if(isset($_POST['action']) && $_POST['action'] == 'delete_comment') 
	{
		delete_comment($_POST, $_SESSION);
	}

	if(isset($_POST['action']) && $_POST['action'] == 'upload_photo') 
	{
		upload_photo();
	}

	function register_user($post) 
	{
		$_SESSION['errors'] = array();

		if(empty($post['first_name']))
		{
			$_SESSION['errors'][] = "First name can't be blank.";
		}
		if(empty($post['last_name']))
		{
			$_SESSION['errors'][] = "Last name can't be blank.";
		}
		if(empty($post['password']))
		{
			$_SESSION['errors'][] = "Password field is required!";
		}
		if(empty($post['confirm_password']))
		{
			$_SESSION['errors'][] = "You must confirm your password!";
		}
		if(!empty($post['password']) && !empty($post['confirm_password']) && $post['password'] !== $post['confirm_password']) {
			$_SESSION['errors'][] = "Your passwords don't match!";
		}
		if(!empty($post['password']) && !empty($post['confirm_password']) && $post['password'] == $post['confirm_password'] && strlen($post['password']) < 6) {
			$_SESSION['errors'][] = "Your password must be longer than 6 characters!";
		}
		if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
			$_SESSION['errors'][] = "Your email address is invalid.";
		}
	
		// Validation Checks COMPLETE
		
		if(count($_SESSION['errors']) > 0) 
		{
			header('location: index.php');
			die();
		}
		else { //Data is validated so we can insert user info into database
			$firstName = escape_this_string($post['first_name']);
			$lastName = escape_this_string($post['last_name']);
			$email = escape_this_string($post['email']);
			$password = escape_this_string($post['password']);
			$query = "INSERT INTO users (first_name, last_name, email, password, profile_img, created_at, updated_at) VALUES ('{$firstName}', '{$lastName}', '{$email}', '{$password}', 'user', NOW(), NOW())";
			
			if(run_mysql_query($query)) 
			{
				$_SESSION['success_message'] = "Account successfully created";
				header('location: index.php');
				die();
			}
			else 
			{
				$_SESSION['errors'][] = "Oops! That email address already registerd!";
				header('location: index.php');
				die();
			}
		}
	}

	function login_user($post) 
	{
		$query = "SELECT * FROM users WHERE users.password = '{$post['password']}' AND users.email = '{$post['email']}'";
		$user = fetch_all($query); //Grabs user with the above credentials
		if(count($user) > 0) 
		{
			$_SESSION['user_id'] = $user[0]['id'];
			$_SESSION['first_name'] = $user[0]['first_name'];
			$_SESSION['last_name'] = $user[0]['last_name'];
			$_SESSION['user_name'] = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
			$_SESSION['profile_img'] = $user[0]['profile_img'];
			$_SESSION['member_since'] = $user[0]['created_at'];
			$_SESSION['email'] = $user[0]['email'];
			$_SESSION['logged_in'] = TRUE;
			header('location: wall.php');
			die();
		}
		else 
		{
			$_SESSION['errors'][] = "Oops! Something went wrong! Please check your email and password and try again.";
			header('location: index.php');
			die();
		}
	}

	function post_message($post, $session) 
	{
		if(!empty($post['message']) && isset($session['user_id'])) 
		{
			$message = escape_this_string($post['message']);
			$query = "INSERT INTO messages (messages.user_id, messages.message, messages.created_at, messages.updated_at) VALUES ('{$session['user_id']}', '{$message}', NOW(), NOW())";
			run_mysql_query($query);
			$_SESSION['success_message'] = "Your message was added successfully!";
			header('location: wall.php');
			die();
		}
		else 
		{
			$_SESSION['errors'] = "Oops! Something went wrong. Your message wasn't posted.";
			header('location: wall.php');
			die();
		}
	}

	function post_comment($post, $session) 
	{
		if(!empty($post['comment']) && isset($session['user_id'])) 
		{
			$comment = escape_this_string($post['comment']);
			$query = "INSERT INTO comments (user_id, comment, message_id, created_at, updated_at) VALUES ('{$session['user_id']}', '{$comment}', '{$post['message_id']}', NOW(), NOW())";
			run_mysql_query($query);			
			$_SESSION['success_message'] = "Yay! Your comment was added successfully!";
			header('location: wall.php');
			die();
		}
		else 
		{
			$_SESSION['errors'] = "Oops! Something went wrong. Your comment wasn't posted.";
			header('location: wall.php');
			die();
		}
	}

	 function delete_comment($post, $session) 
	 {
		if($session['user_id'] == $post['authorization']) 
		{		
			$query = "DELETE FROM comments WHERE comment_id = '{$post['comment_id']}'";
			run_mysql_query($query);
			$_SESSION['success_message'] = "Great job! You deleted your comment!";
			header('location: wall.php');
			die();
		}
		else 
		{
			$_SESSION['errors'] = "Oops! You can only delete your own comments!";
			header('location: wall.php');
			die();
		}
	}

	function upload_photo() 
	{
		function randomKey() 
		{
		  $key = '';  
		  for ($i=0; $i < 20 ; $i++) 
		  { 
		  	$key = $key.rand(0,100);
		  }
		  return $key;
		}

		$rand = randomKey();
		$target_dir = "uploads/";
		$originalName = basename($_FILES["fileToUpload"]["name"]);
		$imageFileType = pathinfo($originalName,PATHINFO_EXTENSION);
		$target_file = $target_dir . $rand . "." . $imageFileType;
		// $newImageName = $rand . "." . $imageFileType;
		$uploadOk = 1;
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) 
		{
	    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	    if($check !== false) 
	    {
	      echo "File is an image - " . $check["mime"] . ".";
	      $uploadOk = 1;
	    } 
	    else 
	    {
	      echo "File is not an image.";
	      $uploadOk = 0;
	    }
		}
		// Check if file already exists
		if (file_exists($target_file)) 
		{
		  echo "Sorry, file already exists.";
		  $uploadOk = 0;
		}
		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 500000) 
		{
		  echo "Sorry, your file is too large.";
		  $uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) 
		{
		  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		  $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) 
		{
		  echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} 
		else 
		{
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
	    {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        $query = "UPDATE users SET profile_img = '$rand', users.updated_at = NOW() WHERE users.id = '{$_SESSION['user_id']}'";
        run_mysql_query($query);
        $_SESSION['profile_img'] = $rand;
        header('location: profile.php');
        die();
	    } 
	    else 
	    {
		    echo "Sorry, there was an error uploading your file.";
		  }
		}
	}
?>