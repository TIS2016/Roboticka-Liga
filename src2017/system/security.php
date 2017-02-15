<?php

class Security {
	
	function get($key) {
		if (isset($_GET[$key])) {
			return stripslashes(htmlspecialchars($_GET[$key]));
		}
		return NULL;
	}
	
	
	function post($key) {
		if (isset($_POST[$key])) {
			return stripslashes(htmlspecialchars($_POST[$key]));
		}
		return NULL;
	}
	
	
	function post_db($key) {
		if (isset($_POST[$key])) {
			return mysql_real_escape_string($_POST[$key]);
		}
		return NULL;
	}

	
	function get_db($key) {
		if (isset($_GET[$key])) {
			return mysql_real_escape_string($_GET[$key]);
		}
		return NULL;
	}
	
}

?>