<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if(isset($_POST['email']) && $_POST['email'] != '') {

			require_once(INCLUDES.'db_connect.tpl');
			$db_connect = new DatabaseConnection();

			$target = $_POST['email'];
			
			$query = "SELECT user FROM users WHERE email = '$target'";
			
			$result = $db_connect->read_query($query);
			
			if ($result) {
				$tuple = mysqli_fetch_assoc($result);
				$token = md5(uniqid(rand(), true));
				
				$query = "UPDATE users SET reset_token = '$token' WHERE email = '$target'";
				
				if($db_connect->update_query($query)) {
					$email_from = "support@kalamanthan.in";
					$email_subject = "Password reset request | KalaManthan";
					$msg = "";
					$msg .= "Hi ".$tuple["user"].", here is the password reset link that you requested:\n\n".WEBPATH."reset?id=".$token;
					$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n".'X-Mailer: PHP/'.phpversion();
					if(mail($target, $email_subject, $msg, $headers)) {
						$_SESSION['alert-msg'] = "Password reset link sent to your mail.";
						$_SESSION['alert-type'] = "success";
					}
					else {
						$_SESSION['alert-msg'] = "There was some error! Please try again.";
						$_SESSION['alert-type'] = "danger";
					}
				}
				else {
					$_SESSION['alert-msg'] = "There was some error! Please try again.";
					$_SESSION['alert-type'] = "danger";
				}
			}
			else {
				$_SESSION['alert-msg'] = "Email doesn't exist in our records!";
				$_SESSION['alert-type'] = "warning";
			}
			$db_connect->close();
			header('location:forgot');
		}
		else {
			$_SESSION["alert-msg"] = "Invalid Input!";
			$_SESSION["alert-type"] = "danger";
		}
	}
	else
		echo "Trying to be smart? Han?";
	
?>