<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		require_once(INCLUDES.'db_connect.tpl');
		$db_connect = new DatabaseConnection();

		$id = $_SESSION["user_id"];
		$post_id = $_POST['postID'];
		
		$query = "SELECT img, vid, file FROM user_posts WHERE id = '$post_id'";
		
		$result = $db_connect->read_query($query);
		
		if ($result) {
			$tuple = mysqli_fetch_assoc($result);
			if ($tuple["img"] != '')
				unlink("posts/".$tuple["img"]);
			if ($tuple["vid"] != '')
				unlink("posts/".$tuple["vid"]);
			if ($tuple["file"] != '')
				unlink("posts/".$tuple["file"]);
			$query = "DELETE FROM user_posts WHERE id = '$post_id'";
			if ($db_connect->delete_query($query)) {
				$_SESSION["alert-msg"] = "Post deleted successfully!";
				$_SESSION["alert-type"] = "success";
			}
		}
		else {
			$_SESSION["alert-msg"] = "Something went wrong! Please try again.";
			$_SESSION["alert-type"] = "danger";
		}
		$db_connect->close();
		header('location:profile?id='.$_SESSION["login_prof_id"]);
	}
	else
		echo "Trying to be smart? Han?";
?>