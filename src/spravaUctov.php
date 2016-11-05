<?php
include 'includes/functions_editAcc.php';
page_head("Správa účtov");
page_nav();
get_topright_form();
if (!isset($_SESSION["loggedUser"]) || $_SESSION["loggedUser"] == null) dieWithError("err-not-logged-in");
if (get_class($_SESSION["loggedUser"]) != "Administrator") dieWithError("err-manage-acc-only-Administrator");

  if (isset($_POST["zrus"])){
      zmaz_acc($_POST['zrus']);}

if (isset($_POST["active"])){
      set_jury($_POST['active']);}

if ($_GET['id'] == '0'){
sprava_uctov();	
}else{
	sprava_uctov_jury();
}     

page_footer()
?>

