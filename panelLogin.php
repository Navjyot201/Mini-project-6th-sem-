<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		require_once(INCLUDES.'validations.tpl');

		$validate = new Validation();

		if (!empty($_POST['id']) && !empty($_POST['password'])) {

			require_once(INCLUDES.'db_connect.tpl');
			$db_connect = new DatabaseConnection();

			$p_id = $_POST['id'];
			$pwd = $validate->encryptME($_POST['password']);
			
			$query = "SELECT * FROM panelists WHERE user_id = '$p_id'";
			
			$result = $db_connect->read_query($query);
			if ($result) {
				$tuple = mysqli_fetch_assoc($result);
				if ($pwd == $tuple["password"]) {
					$_SESSION["panelist"] = "success";
					$_SESSION["user_id"] = $tuple["id"];
					$_SESSION["name"] = $tuple["name"];
				}
				else {
					$_SESSION["alert-msg"] = "Incorrect password!";
					$_SESSION["alert-type"] = "danger";
				}
			}
			else {
				$_SESSION["alert-msg"] = "ID not in our records!";
				$_SESSION["alert-type"] = "danger";
			}
			$db_connect->close();
			header('location:panelProfile');
		}
		else {
			$_SESSION["alert-msg"] = "Invalid Input!";
			$_SESSION["alert-type"] = "danger";
		}
	}
	else
		echo "Trying to be smart? Han?";

?>