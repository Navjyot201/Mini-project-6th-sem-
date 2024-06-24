<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		require_once(INCLUDES.'db_connect.tpl');
		$db_connect = new DatabaseConnection();

		$id = $_SESSION["user_id"];
		$desc = mysqli_real_escape_string($db_connect->access(), $_POST['desc']);
		$pic = basename($_FILES["dp"]["name"]);
		$add = $_POST['address'];
		$tel = $_POST['tel'];
		$username = $_POST['userid'];
		$gender = $_POST['genderRadios'];

		$picExt = "Valid";
		if($pic != "") {
			$picFileType = strtolower(pathinfo($pic, PATHINFO_EXTENSION));
			if (!($picFileType == "jpg" || $picFileType == "jpeg" || $picFileType == "png" || $picFileType == "bmp")) {
				$picExt = "Invalid";
			}
		}
		if ($picExt == "Invalid") {
			$_SESSION['prof_update'] = "Invalid File! Please try again!";
		}
		else {
			if ($tel != "") {
				$query = "UPDATE users SET phone = '$tel' WHERE id = '$id'";
				$db_connect->update_query($query);
			}
			if ($add != "") {
				$query = "UPDATE users SET address = '$add' WHERE id = '$id'";
				$db_connect->update_query($query);
			}
			if ($desc != "") {
				$query = "UPDATE users SET user_desc = '$desc' WHERE id = '$id'";
				$db_connect->update_query($query);
			}
			if ($username != "") {
				$query = "UPDATE users SET profile_id = '$username' WHERE id = '$id'";
				$db_connect->update_query($query);
				$_SESSION["login_prof_id"] = $username;
			}
			if ($gender != "") {
				$query = "UPDATE users SET gender = '$gender' WHERE id = '$id'";
				$db_connect->update_query($query);
			}
			if ($pic != "") {
				$query = "SELECT avatar FROM users WHERE id = '$id'";
				$result = $db_connect->read_query($query);
				if ($result) {
					$tuple = mysqli_fetch_assoc($result);
					unlink("images/dps/".$tuple["avatar"]);
				}
				$query = "UPDATE users SET avatar = '$pic' WHERE id = '$id'";
				if($db_connect->update_query($query)) {
					$target_dir = "images/dps/";
					$target_pic = $target_dir . basename($_FILES["dp"]["name"]);
					move_uploaded_file($_FILES["dp"]["tmp_name"], $target_pic);
				}
			}
			$_SESSION['alert-msg'] = "Profile updated!";
			$_SESSION['alert-type'] = "success";
		}
		$db_connect->close();
		header('location:profile?id='.$_SESSION["login_prof_id"]);
	}
	else
		echo "Trying to be smart? Han?";
?>