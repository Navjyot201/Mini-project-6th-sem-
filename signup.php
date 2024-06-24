<?php

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\SMTP;

	require 'PHPMailer/src/Exception.php';
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		require_once(INCLUDES.'validations.tpl');

		$validate = new Validation();

		if ($validate->name($_POST['name']) && $validate->email($_POST['email']) && !empty($_POST['genderRadios']) && !empty($_POST['dob']) && !empty($_POST['password1']) && !empty($_POST['password2'])) {
				
			if ($_POST['password1'] == $_POST['password2']) {
				
				$name = ucwords($_POST['name']);
				$email = $_POST['email'];
				$gender = $_POST["genderRadios"];
				$dob = $_POST["dob"];
				$prof_id = strtolower(strtok($name, " ")).$dob;
				$pwd = $validate->encryptME($_POST['password1']);
				$token = md5(uniqid(rand(), true));
				
				require_once(INCLUDES.'db_connect.tpl');
				$db_connect = new DatabaseConnection();
				
				$query = "INSERT INTO users (user, email, password, profile_id, gender, dob, token) VALUES ('$name', '$email', '$pwd', '$prof_id', '$gender', '$dob', '$token');";
				
				if($db_connect->insert_query($query)) {
					$_SESSION["alert-msg"] = "Signed up successfully! Welcome to KalaManthan club. :)";
					$email_subject = "Account Verification Link | Kalamanthan";
					$email_from = "Kalamanthan Support Team";
					$msg = "";
					$msg .= "Dear ".strtok($name, " ").", kindly verify your account by clicking this link below:<br><br><a>".WEBPATH."user_verify?id=".$token."</a><br><br>Regards<br>Food Formula Support Team";
					$ourEmail = "arslanb321@gmail.com";
					//$headers = 'From: '.$email_from."\r\n".'X-Mailer: PHP/'.phpversion();
					$mail = new PHPMailer(true);
					try {
						//Server settings
						$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
						$mail->isSMTP();                                            //Send using SMTP
						$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
						$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
						$mail->Username   = $ourEmail;                     //SMTP username
						$mail->Password   = 'ndmt ltsa wczm gjqx';                               //SMTP password
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
						$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

						//Recipients
						$mail->setFrom($ourEmail, $email_from);
						$mail->addAddress($email);     //Add a recipient
						
						//Content
						$mail->isHTML(true);                                  //Set email format to HTML
						$mail->Subject = $email_subject;
						$mail->Body    = $msg;
						//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

						$sentUser = $mail->send();
						if ($sentUser)								
							$_SESSION["alert-msg-2"] ="Just one more step, we've mailed a verification link to you. Kindly click on the link in order to verify your account.";
						else
							$_SESSION["alert-msg-2"] ="We couldn't mail the verification link to you due to some error. Kindly provide your email id through the contact form so that we can get back to you.";
					} 
					catch (Exception $e) {
						$_SESSION["alert-msg-2"] ="We couldn't mail the verification link to you due to some error. Kindly provide your email id through the contact form so that we can get back to you.\n\n".$e->getMessage()."\n\n".$mail->ErrorInfo;
					}				
					
					
					$_SESSION["alert-type"] = "success";
				}
				else {
					$_SESSION["alert-msg"] = "Error! Perhaps the email is already in use.";
					$_SESSION["alert-type"] = "warning";
				}
				$db_connect->close();
			}
			else {
				$_SESSION["alert-msg"] = "Passwords don't match! Try again.";
				$_SESSION["alert-type"] = "danger";
			}
			
		}
		else {
			$_SESSION['alert-msg'] = "Invalid Input!";
			$_SESSION['alert-type'] = "danger";
		}
		header('location:signup');
	}
	else
		echo "Trying to be smart? Han?";
?>