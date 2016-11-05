<?php
require_once(dirname(__FILE__)."/includes/functions.php");
page_head("Prehľad zadaní");
page_nav();
get_topright_form();

if (isset($_GET["action"]) && !empty($_GET["action"])) {
	$action = (integer) $_GET["action"];
	if ($action == 1 && isUserTypeLogged("Administrator")) {
		if (!isset($_POST["start"]) || empty($_POST["start"])) {
			echoError("err-no-start-date");
		}
		else if (!isset($_POST["stop"]) || empty($_POST["stop"])) {
			echoError("err-no-end-date");
		}
		else if (!isset($_POST["id"]) || empty($_POST["id"])) {
			echoError("err-no-assignment-chosen");
		}
		else {
			$start = date("Y-m-d H:i",strtotime($_POST["start"])); 
			$end = date("Y-m-d H:i",strtotime($_POST["stop"]));
			if ($start >= $end) {
				echoError("err-time-logic");
			}
			else {
				if ($link = db_connect()) {
					$sql = "UPDATE assignments SET begin = '".$start."', end='".$end."' WHERE context_id='".$_POST["id"]."'";
					$result = mysqli_query($link,$sql);
					if ($result) {
						echoMessage('m-date-changed');
					}
					else {
						echoError('err-date-changing');
					}
					mysqli_close($link);
				}
				else {
					echoError('err-db-connection-fail');
				}
			}
		}
	}
	else if ($action == 2 && isUserTypeLogged("Administrator")) {
		if (!isset($_POST["id"]) || empty($_POST["id"])) {
			echoError("err-no-assignment-chosen");
		}
		delete_assignment($_POST["id"]);
	}
	else if ($action == 3 && (isUserTypeLogged("Administrator") || isUserTypeLogged("Jury"))) {
		if (!isset($_POST["id"]) || empty($_POST["id"])) {
			echoError("err-no-assignment-chosen");
		}
		else if ($link = db_connect()) {
			$sql = "SELECT * FROM assignments a, contexts c, organisators o WHERE c.user_id = o.user_id AND
																				c.context_id = a.context_id AND
																				c.context_id = ".$_POST["id"]." AND
																				c.user_id = ".$_SESSION["loggedUser"]->getId()." ";
			$result = mysqli_query($link,$sql);
			if (($result && mysqli_num_rows($result) != 0) || isUserTypeLogged("Administrator")) {
				?> <meta http-equiv="refresh" content="0;url=addAssignment.php?id=<?php echo $_POST["id"]; ?>"><?php
			}
			else {
				echoError('err-edit-assignment-rights');
			}
			mysqli_close($link);
		}
		else {
			echoError('err-db-connection-fail');
		}
	}
}    
 
if (isUserTypeLogged("Administrator") || isUserTypeLogged("Jury")) {
	prehlad_zadani(false);
	?>
	<br>
	<form action="addAssignment.php">
		<input type="submit" data-trans-key="new-assignment">
	</form>
	<br>
	<?php
}
prehlad_zadani(true);

page_footer()
?>