<?php

$remove_id = Security::get("remove-assignment");

if ($remove_id) {
	$this->database->remove_assignment($remove_id);
}

$today = date("Y-m-d\T20:00");
$after = date("Y-m-d\T20:00", strtotime("+2 week"));


if (Security::post("new-group")) {
	$id_group = $this->database->create_assignemnt_group(
		Security::post("begin"),
		Security::post("end")
	);
	
	foreach ($_POST["selected_assignment"] as $key => $id) {
		$this->database->publish_assignemnts($id_group, $id);
	}
}


$html = "<h2>Zoznam zadaní</h2><form method='post'>";

if($this->get("user", "admin") == 1) {
	$html.= "<div>
			<a href='?page=new-assignment' class='btn btn-primary'><span class='glyphicon glyphicon-file'></span> Nové zadanie</a>
			<input type='hidden' name='new-group' value='ok'>
			<button type='submit' class='btn btn-success disabled' id='new-assignment-group'><span class='glyphicon glyphicon-ok'></span> Zverejniť zadanie</button>
	</div>
	<ul class='assignments-overview'>
		<li>
			<span>Dátum zverejnenia</span>
			<input type='datetime-local' name='begin' value='".$today."'>
		</li>
		<li>
			<span>Dátum ukončenia</span>
			<input type='datetime-local' name='end' value='".$after."'>
		</li>
	</ul>";
	$unpublished = $this->database->unpublished_assignment();
} else {
	$html.= "	<div><a href='?page=new-assignment' class='btn btn-primary'><span class='glyphicon glyphicon-file'></span> Nové zadanie</a></div>";
	$unpublished = $this->database->unpublished_assignment_jury($this->get("user", "id"));
}

	$html.= "<ul class='assignments-overview list'>";
	while($row = mysqli_fetch_assoc($unpublished)) {
		$html.= "<li>";
		$html.= "<input type='checkbox' name='selected_assignment[]' value='" . $row["id"] . "'>";
		$html.= $row["sk_title"];
		$html.= "<a href='./?page=assignments-overview&remove-assignment=" . $row["id"] . "' class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-trash'></span> Zmazať</a>";
		$html.= "<a href='./?page=new-assignment&id=" . $row["id"] . "' class='btn btn-warning btn-xs'><span class='glyphicon glyphicon-wrench'></span> Upraviť</a>";
		$html.= "</li>";
	}
	$html.= "</ul>";
	$html.= "</form>";


if ($this->get("user", "admin") == 1) {
	$html.= "<h2>Zverejnené zadania</h2>";
	$published_group = $this->database->published_group();
	while($row_group = mysqli_fetch_assoc($published_group)) {
		$html.= "<div><strong>Zadanie od: " . $row_group["begin"] . " do: " . $row_group["end"] . "</strong>";
		$published_assignment = $this->database->published_assignment($row_group["id"]);
		$html.= "<ul class='assignments-overview list published'>";
		while($row_assignment = mysqli_fetch_assoc($published_assignment)) {
			$html.= "<li>" . $row_assignment["sk_title"];
			$html.= "<a href='./?page=new-assignment&id=" . $row_assignment["id"] . "' class='btn btn-warning btn-xs'><span class='glyphicon glyphicon-wrench'></span> Upraviť</a>";
			$html.= "</li>";
		}
		$html.= "</ul></div>";
	}
}

return $html;

?>