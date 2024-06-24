<?php
	require_once(INCLUDES.'db_connect.tpl');
	$db_connect = new DatabaseConnection();

	$partnerID = $_POST['partner_id'];

	if($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['decision'] == '1') {
		$partnerID = $_POST['partner_id'];
		$query1 = "UPDATE partners SET approved = '1' WHERE id = $partnerID";
		$query2 = "SELECT email FROM partners WHERE id = $partnerID";
		$result1 = $db_connect->update_query($query1);
		$result2 = $db_connect->read_query($query2);
		$tuple = mysqli_fetch_assoc($result2);
		if ($result1) {
			$email_from = "support@kalamanthan.in";
			$email_to = $tuple["email"];
			$email_subject = "Partner Approval";
			$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n".'X-Mailer: PHP/'.phpversion();
			$msg = "Congratulations! You've been approved as KalaManthan partner. Our team will shortly be in contact with you.";
			mail($email_to, $email_subject, $msg, $headers);
			$_SESSION['alert-msg'] = "Partner approval successful!";
			$_SESSION['alert-type'] = "success";
		}
		else {
			$_SESSION['alert-msg'] = "Status change failure! Please Try Again.";
			$_SESSION['alert-type'] = "danger";
		}
	}
	elseif ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['decision'] == '0') {
		$query1 = "UPDATE partners SET rejected = '1', reject_reason = '".$_POST['reason']."' WHERE id = $partnerID";
		$result1 = $db_connect->update_query($query1);
		if ($result1) {
			$_SESSION['alert-msg'] = "Rejection Successful.";
			$_SESSION['alert-type'] = "success";
		}
		else {
			$_SESSION['alert-msg'] = "Status change failure! Please Try Again.";
			$_SESSION['alert-type'] = "danger";
		}
	}
	$db_connect->close();
	header('location:ptickets');
?>