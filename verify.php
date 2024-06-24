<?php
	require_once(INCLUDES.'db_connect.tpl');
	$db_connect = new DatabaseConnection();

	$query = "SELECT * FROM users WHERE token = '$token'";
	$result = $db_connect->read_query($query);
	if ($result) {
		$tuple = mysqli_fetch_assoc($result);
		$target_id = $tuple["id"];
		$query = "UPDATE users SET verified = 1 WHERE token = '$token'";
		if($db_connect->update_query($query)) {
			$query = "UPDATE users SET token = 'NULL' WHERE id = '$target_id'";
			$db_connect->update_query($query);
			$_SESSION["alert-msg"] = "Verification successful! You can now login and access your profile.";
			$_SESSION["alert-type"] = "success";
		}
		else {
			$_SESSION["alert-msg"] = "Something went wrong! Please try again.";
			$_SESSION["alert-type"] = "danger";
		}
	}
	else {
		$_SESSION["alert-msg"] = "Invalid Link";
		$_SESSION["alert-type"] = "danger";
	}
	$db_connect->close();
	header('location:login');
?>