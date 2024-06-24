<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		require_once(INCLUDES.'validations.tpl');

		$validate = new Validation();

		if ($validate->email($_POST['email']) && !empty($_POST['password'])) {			

			require_once(INCLUDES.'db_connect.tpl');
			$db_connect = new DatabaseConnection();
			
			$email = $_POST['email'];
			$pwd = $validate->encryptME($_POST['password']);
			
			$query = "SELECT * FROM users WHERE email = '$email'";
			$result = $db_connect->read_query($query);
			if ($result) {
				$tuple = mysqli_fetch_assoc($result);
				if ($pwd == $tuple["password"]) {
					if ($tuple["verified"] == 1) {
						$_SESSION["login"] = "success";
						$_SESSION["user_id"] = $tuple["id"];
						$_SESSION["login_prof_id"] = $tuple["profile_id"];
						$_SESSION["name"] = $tuple["user"];
						header('location:profile?id='.$_SESSION["login_prof_id"]);
					}
					else {
						$_SESSION["alert-msg"] = "You have not verified your email yet!";
						$_SESSION["alert-type"] = "warning";
						header('location:login');
					}
				}
				else {
					$_SESSION["alert-msg"] = "Incorrect password!";
					$_SESSION["alert-type"] = "danger";
					header('location:login');
				}
			}
			else {
				$_SESSION["alert-msg"] = "Email-id not in our records!";
				$_SESSION["alert-type"] = "danger";
				header('location:login');
			}
			$db_connect->close();
		}
		else {
			$_SESSION["alert-msg"] = "Invalid Input!";
			$_SESSION["alert-type"] = "danger";
		}
	}
	else
		echo "Trying to be smart? Han?";

?>