<?php
require_once(dirname(__FILE__)."/includes/functions.php");
page_head("Pridanie zadania");
page_nav();
get_topright_form();

if (!isset($_SESSION["loggedUser"]) || $_SESSION["loggedUser"] == null) dieWithError("err-not-logged-in");
if (get_class($_SESSION["loggedUser"]) == "Team") dieWithError("err-add-assignment-rights");

if ($conn = db_connect()) {
	$id = new_assignment($conn, $_SESSION["loggedUser"]->getId());
	
	$assignment = new Assignment($conn, $id);
	
	if (isset($_POST['skName']) && $_POST['skName'] != $assignment->getSkName()) {
		$assignment->setSkName($conn, $_POST['skName']);	
	}

	if (isset($_POST['engName']) && $_POST['engName'] != $assignment->getEngName()) {
		$assignment->setEngName($conn, $_POST['engName']);	
	}

	if (isset($_POST['skTextPopis']) && $_POST['skTextPopis'] != $assignment->getSkTxt()) {
		$assignment->setSkTxt($conn, $_POST['skTextPopis']);	
	}

	if (isset($_POST['engTextPopis']) && $_POST['engTextPopis'] != $assignment->getEngTxt()) {
		$assignment->setEngTxt($conn, $_POST['engTextPopis']);	
	}

	if (isset($_POST['textVideo']) && $_POST['textVideo'] != "" ) {
		$assignment->uploadVideo($conn, $_POST['textVideo']);
	}

	if (isset($_FILES['uploadedFiles'])) {
		$fileCount = count($_FILES['uploadedFiles']["name"]);
		if ($fileCount != 0 && $_FILES['uploadedFiles']["name"][0] != "") {
			$assignment->uploadFiles($conn, $_FILES['uploadedFiles']);
		}
	}
	
	mysqli_close($conn);
	if (isset($_GET['action'])) {
		$action = (integer) $_GET['action'];
		if ($action == 1) {
			?> <meta http-equiv="refresh" content="0;url=assignment.php?id=<?php echo $assignment->getId(); ?>"><?php
		}
		else if ($action == 2) {
			?> <meta http-equiv="refresh" content="0;url=prehladZadani.php"><?php
		}
	}
	else {
		?> <meta http-equiv="refresh" content="0;url=addAssignment.php?id=<?php echo $id; ?>"><?php
	}
	
}

page_footer();
?>