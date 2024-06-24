<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		require_once(INCLUDES.'db_connect.tpl');
		$db_connect = new DatabaseConnection();
		
		$id = $_SESSION["user_id"];
		$post_id = $_POST['postID'];
		$postBody = mysqli_real_escape_string($db_connect->access(), $_POST['postBody']);
		$cat = $_POST['category'];

		$img = basename($_FILES["image"]["name"]);
		$vid = basename($_FILES["video"]["name"]);
		$doc = basename($_FILES["file"]["name"]);

		$imgExt = "Valid";
		$vidExt = "Valid";
		$docExt = "Valid";

		$target_dir = "posts/";

		if($img != "") {
			$imgFileType = strtolower(pathinfo($img, PATHINFO_EXTENSION));
			if (!($imgFileType == "jpg" || $imgFileType == "jpeg" || $imgFileType == "png" || $imgFileType == "bmp")) {
				$imgExt = "Invalid";
			}
			else {
				$query = "UPDATE user_posts SET img = '$img' WHERE id = '$post_id'";
				$db_connect->update_query($query);
				$target_img = $target_dir . basename($_FILES["image"]["name"]);
				if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_img)) {
					$_SESSION['alert-msg'] = "Post updated!";
					$_SESSION['alert-type'] = "success";
				}
			}
		}
		if ($vid != "") {
			$vidFileType = strtolower(pathinfo($vid, PATHINFO_EXTENSION));
			if (!($vidFileType == "mp4" || $imgFileType == "mpg" || $imgFileType == "mpeg" || $imgFileType == "mkv" || $imgFileType == "avi" || $imgFileType == "dvx" || $imgFileType == "dvdx" || $imgFileType == "dat")) {
				$vidExt = "Invalid";
			}
			else {
				$query = "UPDATE user_posts SET vid = '$vid' WHERE id = '$post_id'";
				$db_connect->update_query($query);
				$target_vid = $target_dir . basename($_FILES["video"]["name"]);
				if(move_uploaded_file($_FILES["video"]["tmp_name"], $target_vid)) {
					$_SESSION['alert-msg'] = "Profile updated!";
					$_SESSION['alert-type'] = "success";
				}
			}
		}
		if ($doc != "") {
			$docFileType = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
			if (!($docFileType == "pdf" || $docFileType == "doc" || $docFileType == "docx" || $docFileType == "odt")) {
				$docExt = "Invalid";
			}
			else {
				$query = "UPDATE user_posts SET file = '$doc' WHERE id = '$post_id'";
				$db_connect->update_query($query);
				$target_doc = $target_dir . basename($_FILES["file"]["name"]);
				if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_doc)) {
					$_SESSION['alert-msg'] = "Profile updated!";
					$_SESSION['alert-type'] = "success";
				}
			}
		}
		if ($imgExt == "Invalid" || $vidExt == "Invalid" || $docExt == "Invalid") {
			$_SESSION['alert-msg'] = "Invalid File/s! Please try again!";
			$_SESSION['alert-type'] = "warning";
		}
		else {
			$query = "UPDATE user_posts SET post_cat = '$cat', post_body = '$postBody', passed = '0', rejected = '0' WHERE id = '$post_id'";
			if($db_connect->update_query($query)) {
				$last_id = $conn->insert_id;
				$email_from = "submission@kalamanthan.in";
				$email_to = "panel@kalamanthan.in";
				$email_subject = "Updated Post";
				$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n".'X-Mailer: PHP/'.phpversion();
				$msg = "A user has updated his previous post. Approve/Reject it by logging in here: http://kalamanthan.in/panelist";
				mail($email_to, $email_subject, $msg, $headers);
				$_SESSION['alert-msg'] = "Updated successfully! Post will be reviewed by panelists before approval.";
				$_SESSION['alert-type'] = "success";
			}
			else {
				$_SESSION['alert-msg'] = "Error: " . $db_connect->error();
				$_SESSION['alert-type'] = "warning";
			}
		
			$db_connect->close();
			header('location:profile?id='.$_SESSION["login_prof_id"]);
		}
	}
	else
		echo "Trying to be smart? Han?";

?>