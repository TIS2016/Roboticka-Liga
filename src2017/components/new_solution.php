<?php

include("resize_image.php");

$id_assignment = Security::get("id-assignment");

$assignment = $this->database->assignment($id_assignment);

//editable
$expired_assignment = $this->database->expired_assignment($assignment["id_group"]);
if ($expired_assignment == 0) {
	echo "permission denied";
	exit;
}

function is_image($img){
	return (bool)getimagesize($img);
}


$this->set("assignment_title", $assignment["sk_title"]);
$this->set("assignment_group_id", $assignment["id_group"]);

$solution = $this->database->exist_solution($id_assignment, $this->get("user", "id"));
$this->set("solution_text", $solution["text"]);


$id_solution = $solution["id"];

if (Security::post("save_solution")) {
	if ($solution["id"]) {
		$this->database->update_solution(
			$id_assignment,
			$this->get("user", "id"),
			Security::post("solution")
		);
	} else {
		$id_solution = $this->database->create_solution(
			$id_assignment,
			$this->get("user", "id"),
			Security::post("solution")
		);
	}
	

	$this->set("solution_text", Security::post("solution"));

	// save youtube link
	$url_youtube = Security::post("youtube_link");
	if (preg_match("~^(?:https?://)?(?:www\.)?(?:youtube\.com|youtu\.be)/watch\?v=([^&]+)~x", $url_youtube) == 1) {

		$parts = parse_url($url_youtube);
		parse_str($parts["query"], $query);

		$this->database->add_youtube_link(0, $id_solution, $query["v"]);
	}

	// upload image
	@mkdir("attachments/solutions/" . $id_solution, 0777);
	@mkdir("attachments/solutions/" . $id_solution . "/images", 0777);
	@mkdir("attachments/solutions/" . $id_solution . "/images/big", 0777);
	@mkdir("attachments/solutions/" . $id_solution . "/images/small", 0777);
	
	$total = count($_FILES["upload_images"]["name"]);

	for($i=0; $i<$total; $i++) {
		$tmpFilePath = $_FILES["upload_images"]["tmp_name"][$i];
		

		if ($tmpFilePath != "" && is_image($tmpFilePath)){
			$extension = pathinfo($_FILES["upload_images"]["name"][$i], PATHINFO_EXTENSION);
			$id_image = $this->database->solution_image($id_solution, $extension, $this->get("token"));

			$newFilePathBig = "attachments/solutions/" . $id_solution . "/images/big/" . $id_image . "." . $extension;
			$newFilePathSmall = "attachments/solutions/" . $id_solution . "/images/small/" . $id_image . ".jpg";
			
			resize_fit_width($tmpFilePath, $newFilePathSmall);
			move_uploaded_file($tmpFilePath, $newFilePathBig);
		}
	}
	
	
	// upload program and files
	@mkdir("attachments/solutions/" . $id_solution, 0777);
	@mkdir("attachments/solutions/" . $id_solution . "/programs", 0777);
	
	$total = count($_FILES["upload_programs"]["name"]);

	for($i=0; $i<$total; $i++) {
		$tmpFilePath = $_FILES["upload_programs"]["tmp_name"][$i];
		
		if ($tmpFilePath != "" && $_FILES["fileToUpload"]["size"] <= (pow(1024, 2)*10)){

			$extension = pathinfo($_FILES["upload_programs"]["name"][$i], PATHINFO_EXTENSION);
			$id_program = $this->database->solution_program($id_solution, $extension, $_FILES["upload_programs"]["name"][$i], $this->get("token"));

			$newFilePath = "attachments/solutions/" . $id_solution . "/programs/" . $id_program . "." . $extension;
			
			move_uploaded_file($tmpFilePath, $newFilePath);
		}
	}
	header("Location: ./?page=new-solution&id-assignment=" . $id_assignment);
}


// get youtube videos
$get_all_youtube_link = $this->database->get_all_youtube_link(0, $id_solution);
$youtube_link = "";
while ($row = mysqli_fetch_assoc($get_all_youtube_link)) {
	$youtube_link.= "<li>";
	$youtube_link.= "<a href='?page=new-solution&id-assignment=" . $id_assignment . "&delete_video=" . $row["id"] . "'>Zmazať</a>";
	$youtube_link.= "<span>http://www.youtube.com/watch?v=" . $row["link"] . "</span>";
	$youtube_link.= "</li>";
}
$this->set("get_youtube_videos", $youtube_link);

// delete video
if (Security::get("delete_video")) {
	$file = $this->database->remove_video(Security::get("delete_video"), $id_solution);
	header("Location: ./?page=new-solution&id-assignment=" . $id_assignment);
}


// get images
$images = "";
$get_image_solution = $this->database->get_image_solution($id_solution);
while ($row = mysqli_fetch_assoc($get_image_solution)) {
	$images.= "<li><img src='components/get_image.php?id=" . $row["token"] . "&min=1'>";
	$images.= "<a href='?page=new-solution&id-assignment=" . $id_assignment . "&delete_image=" . $row["token"] . "'>Zmazať</a>";
	$images.= "</li>";
}
$this->set("get_images_solution", $images);

// delete image
if (Security::get("delete_image")) {
	$file = $this->database->new_solution_remove_image(Security::get("delete_image"));
	@unlink("attachments/solutions/" . $id_solution . "/images/big/" . $file["id"] . "." . $file["extension"]);
	@unlink("attachments/solutions/" . $id_solution . "/images/small/" . $file["id"] . ".jpg");
	header("Location: ./?page=new-solution&id-assignment=" . $id_assignment);
}


// get programs
$programs = "";
$get_programs_solution = $this->database->get_programs_solution($id_solution);
while ($row = mysqli_fetch_assoc($get_programs_solution)) {
	$program.= "<li><a href='components/download_attachment.php?id=" . $row["token"] . "'>" . $row["original_name"] . "</a>";
	$program.= "<a href='?page=new-solution&id-assignment=".$id_assignment."&delete_program=" . $row["token"] . "'>Zmazať</a>";
	$program.= "</li>";
}
$this->set("get_programs_solution", $program);

// delete image
if (Security::get("delete_program")) {
	$file = $this->database->new_solution_remove_program(Security::get("delete_program"));
	@unlink("attachments/solutions/" . $id_solution . "/programs/" . $file);
	header("Location: ./?page=new-solution&id-assignment=" . $id_assignment);
}

return null;

?>