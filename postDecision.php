<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		if(isset($_SESSION["panelist"]) && $_SESSION["panelist"] == "success" && $_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['status']) && isset($_POST['post_id'])) {

			require_once(INCLUDES.'db_connect.tpl');
			$db_connect = new DatabaseConnection();
			
			$post_id = $_POST['post_id'];
			$p_id = $_SESSION["user_id"];
			$query = "UPDATE user_posts SET passed = ".$_POST['status'].", panelist_id = $p_id WHERE id = $post_id";
			$result = $db_connect->update_query($query);
			if ($result) {
				if($_POST['status'] == 1){
					$_SESSION['alert-msg'] = "Approval Successful!";
					$_SESSION['alert-type'] = "success";
				}
				elseif($_POST['status'] == 0){
					$_SESSION['alert-msg'] = "Disapproval Successful!";
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
			header('location:panelProfile');
		}
		else
			header('location:portfolio');
	}
	else
		echo "Trying to be smart? Han?";
?>