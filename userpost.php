<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		require_once(INCLUDES.'db_connect.tpl');
		$db_connect = new DatabaseConnection();

		$id = $_SESSION["user_id"];
		$postBody = mysqli_real_escape_string($db_connect->access(), $_POST['postBody']);
		$cat = $_POST['category'];

		$img = basename($_FILES["image"]["name"]);
		$vid = basename($_FILES["video"]["name"]);
		$doc = basename($_FILES["file"]["name"]);

		$imgExt = "Valid";
		$vidExt = "Valid";
		$docExt = "Valid";


		if($img != "") {
			$imgFileType = strtolower(pathinfo($img, PATHINFO_EXTENSION));
			if (!($imgFileType == "jpg" || $imgFileType == "jpeg" || $imgFileType == "png" || $imgFileType == "bmp")) {
				$imgExt = "Invalid";
			}
		}
		if ($vid != "") {
			$vidFileType = strtolower(pathinfo($vid, PATHINFO_EXTENSION));
			if (!($vidFileType == "mp4" || $imgFileType == "mpg" || $imgFileType == "mpeg" || $imgFileType == "mkv" || $imgFileType == "avi" || $imgFileType == "dvx" || $imgFileType == "dvdx" || $imgFileType == "dat")) {
				$vidExt = "Invalid";
			}
		}
		if ($doc != "") {
			$docFileType = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
			if (!($docFileType == "pdf" || $docFileType == "doc" || $docFileType == "docx" || $docFileType == "odt")) {
				$docExt = "Invalid";
			}
		}
		if ($imgExt == "Invalid" || $vidExt == "Invalid" || $docExt == "Invalid") {
			$_SESSION['file'] = "Invalid File/s! Please try again!";
		}
		else {
			$query = "INSERT INTO user_posts (user_id, post_cat, post_body, img, vid, file) VALUES ('$id', '$cat', '$postBody', '$img', '$vid', '$doc');";
			if($db_connect->insert_query($query)) {
				$last_id = $conn->insert_id;
				$target_dir = "posts/";
				$target_img = $target_dir . basename($_FILES["image"]["name"]);
				$target_vid = $target_dir . basename($_FILES["video"]["name"]);
				$target_doc = $target_dir . basename($_FILES["file"]["name"]);

				move_uploaded_file($_FILES["image"]["tmp_name"], $target_img);
				move_uploaded_file($_FILES["video"]["tmp_name"], $target_vid);
				move_uploaded_file($_FILES["file"]["tmp_name"], $target_doc);

				$email_from = "submission@kalamanthan.in";
				$email_to = "panel@kalamanthan.in";
				$email_subject = "New Post | KalaManthan";
				$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n".'X-Mailer: PHP/'.phpversion();
				$msg = "You have a new post by a KalaManthan user. Approve/Reject it by logging in here: http://kalamanthan.in/panelist";
				mail($email_to, $email_subject, $msg, $headers);
				$_SESSION['alert-msg'] = "Posted successfully! Post will be reviewed by panelists before approval.";
				$_SESSION['alert-type'] = "success";
			}
			else {
				$_SESSION['alert-msg'] = "Error: " . $db_connect->error();
				$_SESSION['alert-type'] = "warning";
			}
		}
		$db_connect->close();
		header('location:profile?id='.$_SESSION["login_prof_id"]);
	}
	else
		echo "Trying to be smart? Han?";
?>