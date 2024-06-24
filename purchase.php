<?php
	require_once(INCLUDES.'db_connect.tpl');
	$db_connect = new DatabaseConnection();
	
	$post_id = $_GET["post"];
	$user_id = $_SESSION["user_id"];
	
	$query1 = "UPDATE user_posts SET purchase = '1' WHERE id = $post_id";
	$query2 = "INSERT INTO purchase_requests (post_id, user, status) VALUES ('$post_id', '$user_id', 'Unhandled');";
	
	$result1 = $db_connect->update_query($query1);
	$result2 = $db_connect->insert_query($query2);
	
	if ($result1 && $result2) {
		$email_from = "updates@kalamanthan.in";
		$email_to = "contact@kalamanthan.in";
		$email_subject = "Purchase Request Received";
		$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n".'X-Mailer: PHP/'.phpversion();
		$msg = "A user has requested purchase for some content. Kindly initiate the process for purchase from Admin Panel.";
		mail($email_to, $email_subject, $msg, $headers);
		$_SESSION['alert-msg'] = "Purchase Request Sent! You'll be contacted soon.";
		$_SESSION['alert-type'] = "success";
	}
	else {
		$_SESSION['alert-msg'] = "Error! Please Try Again.";
		$_SESSION['alert-type'] = "danger";
	}
	$db_connect->close();
	header('location:portfolio');
?>