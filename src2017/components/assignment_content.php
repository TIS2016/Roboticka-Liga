<?php

$id = Security::get("id");
$lang = ["sk", "en"][$_SESSION["lang"]];
$today = Time();

$assignment_group = $this->database->assignment_group($id);
$deadline = date($this->get("assignment_time_format"), strtotime($assignment_group["end"]));
$html = "<h3>" . $this->get("assignment_deadline") . ": " . $deadline . "</h3>";

$num = 1;

$assignments = $this->database->assignments($id);
while ($row = mysqli_fetch_assoc($assignments)) {
	$html.= "<h2>" . $num . ". " . $this->get("assignment_task") . ": " . $row[$lang . "_title"] . "</h2>";
	$html.= "<div>" . html_entity_decode($row[$lang . "_description"]) . "</div>";
	if (
		isset($_SESSION["user"]) &&
		strtotime($assignment_group["end"]) > $today &&
		strtotime($assignment_group["begin"]) < $today &&
		$this->get("user", "jury") == 0 &&
		$this->get("user", "admin") == 0
	) {
		$html.= "<a href='?page=new-solution&id-assignment=" . $row["id"] . "'>Odovzdať riešenie k úlohe " . $num . "</a>";
	}
	$num++;
}


//Show solutions
if (strtotime($assignment_group["end"]) < $today) {
	$html.= "<h2>" . $this->get("assignment_solutions") . "</h2>";
	$html.= "<ul id=\"solutions\">";

	$solution = $this->database->assignment_solutions($id);
	while ($row = mysqli_fetch_assoc($solution)) {
		$html.= "<li class=\"" . $row["best"] . "\"><a href=\"?page=solution&id-assignment=" . $id . "&id-team=" . $row["id_team"] . "\">" . $row["team"] . "</a></li>";
	}

	$html.= "</ul>";
}

return $html;

?>