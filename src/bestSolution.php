<?php
require_once(dirname(__FILE__)."/includes/functions.php");

page_head("Letná liga FLL");
page_nav();
get_topright_form();
if (!isset($_SESSION["loggedUser"]) || $_SESSION["loggedUser"] == null) dieWithError("err-not-logged-in");
if (get_class($_SESSION["loggedUser"]) != "Administrator") dieWithError("err-select-best-solution");
?>
<div id="content">
<?php

$id = (integer)$_GET["id"] ;
if($link = db_connect()){
  $_SESSION['assignment'] = new Assignment($link,$id);
}
if (isset($_SESSION['assignment'])){
  $_SESSION['assignment']->getBestSolutionSlovak();
  $_SESSION['assignment']->getBestSolutionOpen();
}
if (isset($_POST["saveOpen"])){
	if (isset($_POST['bestOpen'])){
     $_SESSION['assignment']->addBestSolutionOpen($_POST['bestOpen']); }}

if (isset($_POST["saveSlovak"])){
	if (isset($_POST['bestSlovak'])){
     $_SESSION['assignment']->addBestSolutionSlovak($_POST['bestSlovak']); }}


page_footer()
?>