<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if(isset($_POST['password1']) && $_POST['password1'] != '' && isset($_POST['password2']) && $_POST['password2'] != '') {
			
			require_once(INCLUDES.'validations.tpl');

			require_once(INCLUDES.'db_connect.tpl');

			if ($_POST['password1'] == $_POST['password2']) {
				$validate = new Validation();
				$pwd = $validate->encryptME($_POST['password1']);
				$token = $_SESSION["token_id"];
				$db_connect = new DatabaseConnection();
				$query = "UPDATE users SET password = '$pwd', reset_token = 'NULL' WHERE reset_token = '$token'";
				if($db_connect->update_query($query)) {
					$_SESSION['alert-msg'] = "Password changed successfully";
					$_SESSION['alert-type'] = "success";
					unset($_SESSION['token_id']);
				}
				else {
					$_SESSION['alert-msg'] = "Error! Please try again.";
					$_SESSION['alert-type'] = "danger";
				}
			}
			else {
				$_SESSION['alert-msg'] = "Passwords don't match!";
				$_SESSION['alert-type'] = "warning";
			}
		}
		else {
			$_SESSION['alert-msg'] = "Invalid Input!";
			$_SESSION['alert-type'] = "danger";
		}
		$db_connect->close();
		header('location:resetPwd');
	}
	else
		echo "Trying to be smart? Han?";
?>