<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	if(isset($_SESSION["panelist"]) && $_SESSION["panelist"] == "success" && $_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['status']) && isset($_POST['post_id']) && isset($_POST['reason'])) {

		require_once(INCLUDES.'db_connect.tpl');
		$db_connect = new DatabaseConnection();
		
		$post_id = $_POST['post_id'];
		$p_id = $_SESSION["user_id"];
		$reason =  $_POST['reason'];
		$query1 = "UPDATE user_posts SET rejected = ".$_POST['status']." WHERE id = $post_id";
		$query2 = "INSERT INTO reject_reasons (post_id, reason, panelist_id) VALUES ('$post_id', '$reason', '$p_id')";
		if ($db_connect->update_query($query1) && $db_connect->insert_query($query2)) {
			if($_POST['status'] == 1){
				$_SESSION['alert-msg'] = "Rejection Successful!";
				$_SESSION['alert-type'] = "success";
			}
			else{
				$_SESSION['alert-msg'] = "Something went wrong!";
				$_SESSION['alert-type'] = "warning";
			}
		}
		else {
			$_SESSION['alert-msg'] = "Status change failure! Please Try Again.";
			$_SESSION['alert-type'] = "danger";
		}
		$db_connect->close();
	}
	else {
		$_SESSION["alert-msg"] = "Invalid Input!";
		$_SESSION["alert-type"] = "danger";
	}
	header('location:panelProfile');
}
else
	echo "Trying to be smart? Han?";
?>