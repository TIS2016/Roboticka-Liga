<?php

class Language {
	
	const SK = 0;
	const EN = 1;
	const STR = ["sk", "en"];


	function __construct() {
		$this->initialize();
		$this->set();
	}


	private function initialize() {
		if (!isset($_SESSION["lang"])) {
			$_SESSION["lang"] = $this->language_detection();
		}
	}
	
	
	private function language_detection() {
		$detect = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
		if ($detect == "sk" || $detect == "cs") {
			return self::SK;
		}
		return self::EN;
	}


	function get() {
		return $_SESSION["lang"];
	}


	function set() {
		if (isset($_GET["lang"])) {
			if ($_GET["lang"] == "en") {
				$_SESSION["lang"] = self::EN;
			} else {
				$_SESSION["lang"] = self::SK;
			}
		}
	}

}

new Language();

?>