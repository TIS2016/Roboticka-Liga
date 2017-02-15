<?php

$id_group = Security::get("id-assignment");
$id_team = Security::get("id-team");

$expired_assignment = $this->database->expired_assignment($id_group);

if ($expired_assignment == 1) {
	echo "permission denied";
	exit;
}


if (Security::post("create_comment") == "ok") {
	foreach ($_POST["comment"] AS $key => $value) {
		
		$rating = $_POST["rating"][$key];
		if ($rating > 3) $rating = 3;
		if ($rating < 0) $rating = 0;
		
		$get_jury_comment = $this->database->get_jury_comment($key, $this->get("user", "id"));
		if (!$get_jury_comment) {
			$this->database->set_jury_comment(
				$key,
				$this->get("user", "id"),
				$_POST["comment"][$key],
				$rating
			);
		} else {
			$this->database->update_jury_comment(
				$key,
				$this->get("user", "id"),
				$_POST["comment"][$key],
				$rating
			);
		}
	}
	header("Location: ?page=solution&id-assignment=" . $id_group . "&id-team=" . $id_team);
}



$get_solution = $this->database->solution_content($id_group, $id_team);
$user = mysqli_fetch_assoc($get_solution);

$html = "<h3>Názov tímu: " . $user["team"] . "</h3>";
$html = "<form method='post'><input type='hidden' name='create_comment' value='ok'>";


$get_solution = $this->database->solution_content($id_group, $id_team);
while($row = mysqli_fetch_assoc($get_solution)) {
	$get_photos		= $this->database->get_solution_photos($row["id_solution"]);
	$get_video			= $this->database->get_solution_video($row["id_solution"]);
	$get_program		= $this->database->get_solution_program($row["id_solution"]);
	$show_coment	= $this->database->show_coment($id_group);
	$get_coment		= $this->database->get_coment($row["id_solution"]);

	$html.= "<h2>Riešenie úlohy: " . $row["sk_title"] . "</h2>";
	$html.= "<p>" . html_entity_decode ($row["text"]) . "</p>";

	$html.= "<h2>Fotografie:</h2>";
	$html.= "<ul id='photo_gallery'>";

	while($solution_photo = mysqli_fetch_assoc($get_photos)) {
		$html.= "<li><a href='components/get_image.php?id=" . $solution_photo["token"] . "&.jpg' rel='group'>";
		$html.="<img src='components/get_image.php?id=" . $solution_photo["token"] . "&min=1'>";
		$html.= "</a></li>";
	}
	$html.= "</ul>";

	$html.= "<h2>Video:</h2>";
	while($solution_video = mysqli_fetch_assoc($get_video)) {
		$search     = '/youtube\.com\/watch\?v=([a-zA-Z0-9]+)/smi';
		$replace    = "youtube.com/embed/$1";
		$url = preg_replace($search, $replace, $solution_video["link"]);
		$html.= "<p><iframe width='560' height='315' src='https://www.youtube.com/embed/" . $url . "?rel=0' frameborder='0' allowfullscreen></iframe></p>";
	}

	$html.= "<h2>Programy:</h2>";
	$html.= "<ul>";
	while($solution_program = mysqli_fetch_assoc($get_program)) {
		$html.= "<li><a href='components/download_attachment.php?id=" . $solution_program["token"] . "'>";
		$html.= $solution_program["original_name"];
		$html.= "</a></li>";
	}
	$html.= "</ul>";

	$html.= "<h2>Hodnotenie:</h2>";
	
	// all people
	if ($this->get("user", "admin") == 0 && $this->get("user", "jury") == 0) {
		if ($show_coment == 0) {
			$html.= "<p>" . $get_coment["text"] . "</p>";
		}
	}
	
	//for jury
	if ($this->get("user", "admin") == 0 && $this->get("user", "jury") == 1) {
		$get_jury_comment = $this->database->get_jury_comment($row["id_solution"], $this->get("user", "id"));		

		$html.= "<ul id='rating'><li><textarea name='comment[".$row["id_solution"]."]'>" . $get_jury_comment["text"] . "</textarea></li>";
		$html.= "<li>Body: <input type='text' name='rating[".$row["id_solution"]."]' value='" . $get_jury_comment["points"] . "'><input type='submit' value='Uložiť'></li></ul>";
	}
	
	//for admin
	if ($this->get("user", "admin") == 1) {
		$get_admin_comment = $this->database->get_admin_comment($row["id_solution"], $this->get("user", "id"));
		$count_jury = mysqli_num_rows($get_admin_comment);
		
		$admin_comment = "";
		$increment = 1;
		$rating = 0;
		while ($row = mysqli_fetch_assoc($get_admin_comment)) {
			$admin_comment.= $increment . ". " . $row["text"] . "\n" . $row["points"] . "\n\n";
			$rating += $row["points"];
			$increment++;
		}
		$ratio = round($rating / $count_jury, 2);
		$admin_comment.= "Celkový pocet: " . $ratio;

		$html.= "<ul id='rating'><li><textarea name='comment[".$row["id_solution"]."]'>" . $admin_comment . "</textarea></li>";
		$html.= "<li>Body: <input type='text' name='rating[".$row["id_solution"]."]' value='" . $ratio . "'><input type='submit' value='Uložiť'></li></ul>";
	}
}


$html.= "</html>";

return $html;

?>