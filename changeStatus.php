<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	if(isset($_SESSION["admin"]) && $_SESSION["admin"] == "success" && isset($_POST['request_id']) && isset($_POST['status_select'])) {

		require_once(INCLUDES.'db_connect.tpl');
		$db_connect = new DatabaseConnection();
		
		$purchase_id = $_POST['request_id'];
		$status = $_POST['status_select'];
		$handler_id = $_SESSION["user_id"];

		$query = "UPDATE purchase_requests SET status = '$status', handler_id = '$handler_id' WHERE id = '$purchase_id'";
		
		if ($db_connect->update_query($query)) {
			$_SESSION['alert-msg'] = "Status changed!";
			$_SESSION['alert-type'] = "success";
		}
		else {
			$_SESSION['alert-msg'] = "Status change failure! Please Try Again.";
			$_SESSION['alert-type'] = "danger";
		}
		$db_connect->close();
	}
	else {
		$_SESSION["alert-msg"] = "Invalid Input!";
		$_SESSION["alert-type"] = "danger";
	}
	header('location:preq');
}
else
	echo "Trying to be smart? Han?";
?>