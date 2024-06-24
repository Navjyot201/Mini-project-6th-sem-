<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		require_once(INCLUDES.'validations.tpl');

		$validate = new Validation();

		if ($validate->name($_POST['name']) && $validate->phone($_POST['phone']) && $validate->name($_POST['company']) && !empty(basename($_FILES["logo"]["name"])) && $validate->email($_POST['email']) && !empty($_POST['category']) && !empty($_POST['message'])) {

			$name = $_POST["name"];
			$phone = $_POST["phone"];
			$company = $_POST["company"];
			$logo = basename($_FILES["logo"]["name"]);
			$email = $_POST["email"];
			$cat = $_POST["category"];
			$message = $validate->test_input($_POST["message"]);

			
			$email_from = "contact@kalamanthan.in";
			$email_to = "jayant@kalamanthan.in";
			$email_subject = "Partner Request";
			$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email."\r\n".'X-Mailer: PHP/'.phpversion();

			$logoExt = "Valid";
			if($logo != "") {
				$logoFileType = strtolower(pathinfo($logo, PATHINFO_EXTENSION));
				if (!($logoFileType == "jpg" || $logoFileType == "jpeg" || $logoFileType == "png" || $logoFileType == "bmp")) {
					$logoExt = "Invalid";
				}
			}
			if ($logoExt == "Invalid") {
				$_SESSION['partner_req'] = "Invalid File! Please try again!";
			}
			else {
				require_once(INCLUDES.'db_connect.tpl');
				$db_connect = new DatabaseConnection();
				
				$query = "INSERT INTO partners (name, company, email, phone, category, message, logo) VALUES ('$name', '$company', '$email', '$phone', '$cat', '$message', '$logo');";
				
				if($db_connect->insert_query($query)) {
					$target_dir = "images/partners/";
					$target_pic = $target_dir . basename($_FILES["logo"]["name"]);
					move_uploaded_file($_FILES["logo"]["tmp_name"], $target_pic);
					$msg = "You have a new partner request.\n\n";
					$msg .= $message."\n\n- ".$name.", ".$email;
					if(mail($email_to, $email_subject, $msg, $headers)) {
						$_SESSION['alert-msg'] = "Request Submitted! Please wait for our response.";
						$_SESSION['alert-type'] = "success";
					}
					else {
						$_SESSION['alert-msg'] = "Your message coudn't reach us! Please try again.";
						$_SESSION['alert-type'] = "danger";
					}
				}
				else {
					$_SESSION['alert-msg'] = "Error! Please try again.";
					$_SESSION['alert-type'] = "danger";
				}
			}
			$db_connect->close();
			header('location:partners');
		}
		else {
			$_SESSION["alert-msg"] = "Invalid Input!";
			$_SESSION["alert-type"] = "danger";
		}
	}
	else
		echo "Trying to be smart? Han?";
?>