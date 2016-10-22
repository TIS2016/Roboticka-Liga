<?php
require_once(dirname(__FILE__)."/includes/functions.php");
page_head("Pridanie zadania");
page_nav();
get_topright_form();

if (!isset($_SESSION["loggedUser"]) || $_SESSION["loggedUser"] == null) dieWithError("err-not-logged-in");
if (get_class($_SESSION["loggedUser"]) == "Team") dieWithError("err-add-assignment-rights");

$conn = db_connect();

if(isset($_GET["id"]) && !empty($_GET["id"])) {
	$sql_get_assignment = "SELECT * FROM assignments a, contexts c WHERE c.context_id = a.context_id AND c.context_id = ".$_GET["id"];
	$flag = false;
	$result = mysqli_query($conn,$sql_get_assignment); 
	if ($result == true && mysqli_num_rows($result) != 0) {
		$assignment = new Assignment($conn, $_GET["id"]);
		if (!isUserTypeLogged("Administrator") && $_SESSION["loggedUser"]->getId() != $assignment->getAuthor()->getId()) {
			dieWithError("err-edit-assignment-rights");
		}
	}
	else {
		$assignment = new Assignment($conn, 0);
		$flag = true;
	}
	if (isset($_POST['checkbox'])) {
		$assignment->deleteAttachments($conn, $_POST['checkbox']);
	}

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
	$assignment->setAttachments($conn);
	if (isset($_GET['action'])) {
		$action = (integer) $_GET['action'];
		if ($action == 1) {
			?> <meta http-equiv="refresh" content="0;url=assignment.php?id=<?php echo $assignment->getId(); ?>"><?php
		}
		else if ($action == 2) {
			?> <meta http-equiv="refresh" content="0;url=prehladZadani.php"><?php
		}
	}
	$assignment->getEditingHtml($flag);
}
else {
	$assignment = new Assignment($conn, 0);
	$assignment->getEditingHtml(true);
}

mysqli_close($conn);

page_footer();
?>