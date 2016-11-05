<?php
require_once(dirname(__FILE__)."/includes/functions.php");
page_head("Pridanie riešenia");
page_nav();
get_topright_form();

if (!isset($_SESSION["assignment"]) || $_SESSION["assignment"] == null) dieWithError("err-no-assignment-chosen");
if ($_SESSION["assignment"]->isAfterDeadline()) dieWithError("err-assignment-deadline");
if (!isset($_SESSION["loggedUser"]) || $_SESSION["loggedUser"] == null) dieWithError("err-not-logged-in");
if (get_class($_SESSION["loggedUser"]) != "Team") dieWithError("err-add-solution-rights");

$sql_get_solution = "SELECT c.context_id as 'context_id' FROM solutions s, contexts c WHERE s.context_id = c.context_id AND s.assignment_id = ".$_SESSION["assignment"]->getId()." AND c.user_id = ".$_SESSION["loggedUser"]->getId();
$conn = db_connect();
$solution = mysqli_query($conn,$sql_get_solution);

if (mysqli_num_rows($solution) == 0) {
	$cid = new_solution($conn, $_SESSION["loggedUser"]->getId(),$_SESSION["assignment"]->getId());
}
else {
	$cid = mysqli_fetch_array($solution)['context_id'];
}

$solution = new Solution($conn, $cid, $_SESSION["loggedUser"], $_SESSION["assignment"]);

if (isset($_POST['checkbox'])) {
	$solution->deleteAttachments($conn, $_POST['checkbox']);
}

if (isset($_POST['textPopis']) && $_POST['textPopis'] != $solution->getTxt()) {
	$solution->setTxt($conn, $_POST['textPopis']);	
}

if (isset($_POST['textVideo']) && $_POST['textVideo'] != "" ) {
	$solution->uploadVideo($conn, $_POST['textVideo']);
}

if (isset($_FILES['uploadedFiles'])) {
	$fileCount = count($_FILES['uploadedFiles']["name"]);
	if ($fileCount != 0 && $_FILES['uploadedFiles']["name"][0] != "") {
		$solution->uploadFiles($conn, $_FILES['uploadedFiles']);
	}
}

$solution->setAttachments($conn);
mysqli_close($conn);

if (isset($_GET['action'])) {
	$action = (integer) $_GET['action'];
	if ($action == 1) {
		?> <meta http-equiv="refresh" content="0;url=solution.php?id=<?php echo $solution->getId(); ?>"><?php
	}
	else if ($action == 2) {
		?> <meta http-equiv="refresh" content="0;url=assignment.php?id=<?php echo $_SESSION["assignment"]->getId(); ?>"><?php
	}
}

$solution->getEditingHtml();

page_footer();
?>