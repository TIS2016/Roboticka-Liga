<?php

class Database {
	
	private $conn;
	
	function __construct() {
		$config = parse_ini_file("config/db_conn.ini");
		$this->conn = mysqli_connect(
			$config["server"],
			$config["name"],
			$config["password"],
			$config["database"]);

		if (!$this->conn) {
			echo mysqli_connect_error();
		}
		@mysqli_query($this->conn, "SET sql_mode = ''");
		@mysqli_query($this->conn, "SET CHARACTER SET 'utf8'");
	}
	

	public function login($mail, $password) {
		$sql = "SELECT * ";
		$sql.= "FROM users ";
		$sql.= "LEFT JOIN banned ";
		$sql.= "ON id = id_user ";
		$sql.= "WHERE mail = '" . $mail . "' AND ";
		$sql.= "password = '" . md5($password) . "' AND ";
		$sql.= "id_user IS NULL";
		$result = mysqli_query($this->conn, $sql);
		return mysqli_fetch_assoc($result);
	}
	
	
	public function last_logged($id) {
		$sql = "UPDATE users ";
		$sql.= "SET last_logged = NOW() ";
		$sql.= "WHERE id = '" . $id . "'";
		mysqli_query($this->conn, $sql);
	}


	public function menu_assignments() {
		$sql = "SELECT id ";
		$sql.= "FROM assignments_group ";
		$sql.= "WHERE year = ";
		$sql.= "(SELECT year ";
		$sql.= "FROM assignments_group ";
		$sql.= "ORDER BY year DESC ";
		$sql.= "LIMIT 0,1) AND begin < NOW()";
		$sql.= "ORDER BY end ASC";
		return mysqli_query($this->conn, $sql);
	}


	public function menu_archive_year() {
		$sql = "SELECT year ";
		$sql.= "FROM assignments_group ";
		$sql.= "WHERE year != ";
		$sql.= "(SELECT year ";
		$sql.= "FROM assignments_group ";
		$sql.= "ORDER BY year DESC ";
		$sql.= "LIMIT 0,1) ";
		$sql.= "GROUP BY year ";
		$sql.= "ORDER BY year ";
		return mysqli_query($this->conn, $sql);
	}


	public function menu_archive_assignments($year) {
		$sql = "SELECT id ";
		$sql.= "FROM assignments_group ";
		$sql.= "WHERE year = '" . $year . "' " ;
		$sql.= "ORDER BY id ASC";
		return mysqli_query($this->conn, $sql);
	}


	public function assignment_group($id) {
		$sql = "SELECT * ";
		$sql.= "FROM assignments_group ";
		$sql.= "WHERE id = '" . $id . "'";
		$result_assignment_group = mysqli_query($this->conn, $sql);
		return mysqli_fetch_assoc($result_assignment_group);
	}

	
	public function assignment($id) {
		$sql = "SELECT * ";
		$sql.= "FROM assignments ";
		$sql.= "WHERE id = '" . $id . "'";
		$result_assignment = mysqli_query($this->conn, $sql);
		return mysqli_fetch_assoc($result_assignment);
	}
	

	public function assignments($id) {
		$sql = "SELECT A.id, G.begin, G.end, A.sk_title, IF(A.en_title!='', A.en_title, A.sk_title) AS en_title, ";
		$sql.= "A.sk_description, IF(A.en_description!='', A.en_description, A.sk_description) AS en_description ";
		$sql.= "FROM assignments_group AS G ";
		$sql.= "LEFT JOIN assignments AS A ";
		$sql.= "ON G.id = A.id_group ";
		$sql.= "WHERE G.id = '" . $id . "'";
		return mysqli_query($this->conn, $sql);
	}
	
	
	public function assignment_solutions($id_group) {
		$sql = "SELECT S.id, IF(S.best = 1, 'best', '') AS best, T.name AS team, T.id_user AS id_team ";
		$sql.= "FROM assignments AS A ";
		$sql.= "LEFT JOIN solutions AS S ";
		$sql.= "ON A.id = S.id_assignment ";
		$sql.= "LEFT JOIN teams AS T ";
		$sql.= "ON S.id_user = T.id_user ";
		$sql.= "WHERE A.id_group = '" . $id_group . "' ";
		$sql.= "GROUP BY T.name ";
		$sql.= "ORDER BY S.best DESC, T.name ASC";
		$result = mysqli_query($this->conn, $sql);
		return $result;
	}

	public function solution_content($id_group, $id_user) {
		$sql = "SELECT T.name AS team, A.sk_title, IF(A.en_title!='', A.en_title, A.sk_title) AS en_title, ";
		$sql.= "S.text, S.id AS id_solution ";
		$sql.= "FROM assignments as A ";
		$sql.= "LEFT JOIN solutions as S ";
		$sql.= "ON A.id = S.id_assignment ";
		$sql.= "LEFT JOIN teams as T ";
		$sql.= "ON S.id_user = T.id_user ";
		$sql.= "WHERE A.id_group = '" . $id_group . "' AND S.id_user = '" . $id_user . "'";
		return mysqli_query($this->conn, $sql);
	}

	
	public function get_solution_photos($id) {
		$sql = "SELECT * ";
		$sql.= "FROM images_solution ";
		$sql.= "WHERE id_solution = '" . $id . "'";
		return mysqli_query($this->conn, $sql);
	}

	
	public function get_solution_video($id) {
		$sql = "SELECT * ";
		$sql.= "FROM videos ";
		$sql.= "WHERE context = '0' AND id_context = '" . $id . "'";
		return mysqli_query($this->conn, $sql);
	}


	public function get_coment($id) {
		$sql = "SELECT * ";
		$sql.= "FROM comments ";
		$sql.= "WHERE id_solution = '" . $id . "'";
		$result = mysqli_query($this->conn, $sql);
		return mysqli_fetch_assoc($result);	
	}
	
	
	public function show_coment($id_group) {
		$sql = "SELECT * ";
		$sql.= "FROM assignments as A ";
		$sql.= "LEFT JOIN solutions as S ";
		$sql.= "ON A.id = S.id_assignment ";
		$sql.= "LEFT JOIN comments AS C ";
		$sql.= "ON C.id_solution = S.id AND C.id_user = '1'";
		$sql.= "WHERE A.id_group = '" . $id_group . "' AND C.text IS NULL";
		$result = mysqli_query($this->conn, $sql);
		return mysqli_num_rows($result);
	}


	public function get_solution_program($id) {
		$sql = "SELECT * ";
		$sql.= "FROM programs ";
		$sql.= "WHERE id_solution = '" . $id . "'";
		return mysqli_query($this->conn, $sql);
	}

	
	public function new_assignment_content($id) {
		$sql = "SELECT id, sk_title, en_title, sk_description, en_description ";
		$sql.= "FROM assignments ";
		$sql.= "WHERE id = '" . $id . "'";
		$result_assignment = mysqli_query($this->conn, $sql);
		return mysqli_fetch_assoc($result_assignment);
	}
	
	
	public function create_new_assignment($id_user) {
		$sql = "INSERT INTO assignments ";
		$sql.= "(id_user, sk_title, en_title, sk_description, en_description) VALUES ";
		$sql.= "('" . $id_user . "', 'Nové zadanie', 'New assignment', '', '')";
		mysqli_query($this->conn, $sql);
		return mysqli_insert_id($this->conn);
	}
	
	
	public function edit_assignment($id, $sk_title, $en_title, $sk_description, $en_description) {
		$sql = "UPDATE assignments ";
		$sql.= "SET sk_title = '" . $sk_title . "', en_title = '" . $en_title . "', ";
		$sql.= "sk_description = '" . $sk_description . "', en_description = '" . $en_description . "' ";
		$sql.= "WHERE id = '" . $id . "'";
		mysqli_query($this->conn, $sql);
	}


	public function unpublished_assignment_jury($id_user) {
		$sql = "SELECT * ";
		$sql.= "FROM assignments ";
		$sql.= "WHERE id_group = '0' AND id_user = '" . $id_user . "'";
		return mysqli_query($this->conn, $sql);
	}


	public function unpublished_assignment() {
		$sql = "SELECT * ";
		$sql.= "FROM assignments ";
		$sql.= "WHERE id_group = '0'";
		return mysqli_query($this->conn, $sql);
	}


	public function published_group() {
		$sql = "SELECT * ";
		$sql.= "FROM assignments_group ";
		$sql.= "WHERE end > NOW() ";
		$sql.= "GROUP BY id";
		return mysqli_query($this->conn, $sql);
	}
	
	
	public function published_assignment($id_group) {
		$sql = "SELECT * ";
		$sql.= "FROM assignments ";
		$sql.= "WHERE id_group = '" . $id_group . "'";
		return mysqli_query($this->conn, $sql);
	}


	public function remove_assignment($id) {
		$sql = "DELETE FROM assignments ";
		$sql.= "WHERE id='" . $id . "'";
		return mysqli_query($this->conn, $sql);
	}
	
	
	public function new_assignment_image($id_assignment, $extension) {
		$sql = "INSERT INTO images_assignment ";
		$sql.= "(id_assignment, extension) VALUES ";
		$sql.= "('" . $id_assignment . "', '" . $extension . "')";
		mysqli_query($this->conn, $sql);
		return mysqli_insert_id($this->conn);
	}
	

	public function solution_image($id_solution, $extension, $token) {
		$sql = "INSERT INTO images_solution ";
		$sql.= "(id_solution, extension, token) VALUES ";
		$sql.= "('" . $id_solution . "', '" . $extension . "', '" . $token . "')";
		mysqli_query($this->conn, $sql);
		return mysqli_insert_id($this->conn);
	}
	

	public function solution_program($id_solution, $extension, $original_name, $token) {
		$sql = "INSERT INTO programs ";
		$sql.= "(id_solution, extension, original_name, token) VALUES ";
		$sql.= "('" . $id_solution . "', '" . $extension . "', '" . $original_name . "', '" . $token . "')";
		mysqli_query($this->conn, $sql);
		return mysqli_insert_id($this->conn);
	}

	
	public function get_image_assignment($id_assignment) {
		$sql = "SELECT * ";
		$sql.= "FROM images_assignment ";
		$sql.= "WHERE id_assignment = '" . $id_assignment . "'";
		return mysqli_query($this->conn, $sql);
	}

	
	public function get_image_solution($id_solution) {
		$sql = "SELECT * ";
		$sql.= "FROM images_solution ";
		$sql.= "WHERE id_solution = '" . $id_solution . "'";
		return mysqli_query($this->conn, $sql);
	}

	
	public function get_programs_solution($id_solution) {
		$sql = "SELECT * ";
		$sql.= "FROM programs ";
		$sql.= "WHERE id_solution = '" . $id_solution . "'";
		return mysqli_query($this->conn, $sql);
	}
	
	
	public function new_assignment_remove_image($id) {
		$sql = "SELECT * ";
		$sql.= "FROM images_assignment ";
		$sql.= "WHERE id = '" . $id . "'";
		$result_image = mysqli_query($this->conn, $sql);
		$row_image = mysqli_fetch_assoc($result_image);
		$sql = "DELETE FROM images_assignment ";
		$sql.= "WHERE id='" . $id . "'";
		mysqli_query($this->conn, $sql);
		return $row_image;
	}
	
	
	public function new_solution_remove_program($id) {
		$sql = "SELECT * ";
		$sql.= "FROM programs ";
		$sql.= "WHERE token = '" . $id . "'";
		$result_image = mysqli_query($this->conn, $sql);
		$row_image = mysqli_fetch_assoc($result_image);
		$sql = "DELETE FROM programs ";
		$sql.= "WHERE token='" . $id . "'";
		mysqli_query($this->conn, $sql);
		return $row_image["id"] . "." . $row_image["extension"];
	}
	
	
	public function new_solution_remove_image($id) {
		$sql = "SELECT * ";
		$sql.= "FROM images_solution ";
		$sql.= "WHERE token = '" . $id . "'";
		$result_image = mysqli_query($this->conn, $sql);
		$row_image = mysqli_fetch_assoc($result_image);
		$sql = "DELETE FROM images_solution ";
		$sql.= "WHERE token='" . $id . "'";
		mysqli_query($this->conn, $sql);
		return $row_image;
	}
	
	
	public function add_youtube_link($context, $id_context, $link) {
		$sql = "INSERT INTO videos ";
		$sql.= "(context, id_context, link) VALUES ";
		$sql.= "('" . $context . "', '" . $id_context . "', '" . $link . "')";
		mysqli_query($this->conn, $sql);
	}
	
	
	public function get_all_youtube_link($context, $id_context) {
		$sql = "SELECT * ";
		$sql.= "FROM videos ";
		$sql.= "WHERE context = '" . $context . "' AND id_context = '" . $id_context . "'";
		return mysqli_query($this->conn, $sql);
	}
	
	
	public function remove_video($id, $id_context) {
		$sql = "DELETE FROM videos ";
		$sql.= "WHERE id='" . $id . "' AND id_context = '" . $id_context . "'";
		mysqli_query($this->conn, $sql);
	}
	
	
	public function create_assignemnt_group($begin, $end) {
		$sql = "INSERT INTO assignments_group ";
		$sql.= "(begin, end, year) VALUES ";
		$sql.= "('" . $begin . "', '" . $end . "', '" . date("Y") . "')";
		mysqli_query($this->conn, $sql);
		return mysqli_insert_id($this->conn);
	}
	
	
	public function publish_assignemnts($id_group, $id) {
		$sql = "UPDATE assignments ";
		$sql.= "SET id_group = '" . $id_group . "' ";
		$sql.= "WHERE id = '" . $id . "'";
		mysqli_query($this->conn, $sql);
	}
	
	
	public function find_email($mail) {
		$sql = "SELECT * ";
		$sql.= "FROM users ";
		$sql.= "WHERE mail = '" . $mail . "'";
		$result = mysqli_query($this->conn, $sql);
		return mysqli_num_rows($result);
	}
	
	
	public function registration_user($mail, $password, $team, $about, $league) {
		$sql = "INSERT INTO users ";
		$sql.= "(mail, password, admin, jury) VALUES ";
		$sql.= "('" . $mail . "', '" . md5($password) . "', '0', '0')";
		mysqli_query($this->conn, $sql);
		$id_user = mysqli_insert_id($this->conn);
		
		$sql = "INSERT INTO teams ";
		$sql.= "(id_user, name, description, sk_league) VALUES ";
		$sql.= "('" . $id_user . "', '" . $team . "', '" . $about . "', '" . $league . "')";
		mysqli_query($this->conn, $sql);
	}


	public function exist_solution($id_assignment, $id_user) {
		$sql = "SELECT * ";
		$sql.= "FROM solutions ";
		$sql.= "WHERE id_assignment = '" . $id_assignment . "' AND id_user = '" . $id_user . "'";
		$result = mysqli_query($this->conn, $sql);
		return mysqli_fetch_assoc($result);
	}
	
	
	public function create_solution($id_assignment, $id_user, $text) {
		$sql = "INSERT INTO solutions ";
		$sql.= "(id_assignment, id_user, text) VALUES ";
		$sql.= "('" . $id_assignment . "', '" . $id_user . "', '" . $text . "')";
		mysqli_query($this->conn, $sql);
		return mysqli_insert_id($this->conn);
	}
	
	
	public function update_solution($id_assignment, $id_user, $text) {
		$sql = "UPDATE solutions ";
		$sql.= "SET text = '" . $text . "' ";
		$sql.= "WHERE id_assignment = '" . $id_assignment . "' AND id_user = '" . $id_user . "'";
		mysqli_query($this->conn, $sql);
	}
	
	
	public function expired_assignment($id_assignment) {
		$sql = "SELECT * ";
		$sql.= "FROM assignments_group AS AG ";
		$sql.= "WHERE AG.id = '" . $id_assignment . "' AND AG.end > NOW()";
		$result = mysqli_query($this->conn, $sql);
		return mysqli_num_rows($result);
	}
	
	
	public function get_jury_comment($solution, $id_user) {
		$sql = "SELECT * ";
		$sql.= "FROM comments ";
		$sql.= "WHERE id_solution = '" . $solution . "' AND id_user = '" . $id_user . "'";
		$result = mysqli_query($this->conn, $sql);
		return mysqli_fetch_assoc($result);
	}
	
	
	public function set_jury_comment($solution, $id_user, $text, $points) {
		$sql = "INSERT INTO comments ";
		$sql.= "(id_solution, id_user, text, points) VALUES ";
		$sql.= "('" . $solution . "', '" . $id_user . "', '" . $text . "', '" . $points . "')";
		mysqli_query($this->conn, $sql);
	}
	
	
	public function update_jury_comment($solution, $id_user, $text, $points) {
		$sql = "UPDATE comments ";
		$sql.= "SET text = '" . $text . "', points = '" . $points . "' ";
		$sql.= "WHERE id_solution = '" . $solution . "' AND id_user = '" . $id_user . "'";
		mysqli_query($this->conn, $sql);
	}
	
	
	public function get_admin_comment($solution, $id_user) {
		$sql = "SELECT * ";
		$sql.= "FROM comments ";
		$sql.= "WHERE id_solution = '" . $solution . "' AND id_user != '" . $id_user . "'";
		return mysqli_query($this->conn, $sql);
	}

	
	public function find_team($team) {
		$sql = "SELECT * ";
		$sql.= "FROM teams ";
		$sql.= "WHERE name = '" . $team . "'";
		return mysqli_query($this->conn, $sql);
	}

	function __destruct() {
		mysqli_close($this->conn);
	}

}

?>