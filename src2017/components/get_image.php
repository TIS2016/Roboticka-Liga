<?php

$config = parse_ini_file("../config/db_conn.ini");
$conn = mysqli_connect(
	$config["server"],
	$config["name"],
	$config["password"],
	$config["database"]);

@mysqli_query($conn, "SET CHARACTER SET 'utf8'");

$sql = "SELECT * FROM images_solution WHERE token = '" . $_GET["id"] . "'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);


if (isset($_GET["min"])) {
	$image_path = "../attachments/solutions/" . $row["id_solution"] . "/images/small/" . $row["id"] . ".jpg";
} else {
	$image_path = "../attachments/solutions/" . $row["id_solution"] . "/images/big/" . $row["id"] . "." . $row["extension"];	
}

switch($row["id_solution"]) {
    case "gif": $type="image/gif"; break;
    case "png": $type="image/png"; break;
    case "jpeg":
    case "jpg": $type="image/jpeg"; break;
    default:
}

header("Content-Type: " . $type);
readfile($image_path);
die();

?>