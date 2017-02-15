<?php

class Pages extends Translate {


	/*
	* Nacitanie defaultných funkcií
	*
	*/	
	function init_functions() {

	
		/**
		* Zoznam stránok s názvom
		*
		*/
		$this->set("page", function() {
			return array(
				"home"								=> "content/home.htm",
				"assignment"						=> "content/assignment.htm",
				"assignments-overview"		=> "content/assignments-overview.htm",
				"new-assignment"				=> "content/new-assignment.htm",
				"new-registration"				=> "content/new-registration.htm",
				"new-solution"					=> "content/new-solution.htm",
				"results"							=> "content/results.htm",
				"solution"							=> "content/solution.htm",
				"archive"							=> "content/archive.htm",
				"error-404"						=> "error/error-404.htm");
		});
		
		
		
		$this->set("home_content", "home-sk.htm", "home-en.htm");
		$this->set("home_translate", function() {
			return $this->get("include_html", "content/" . $this->get("home_content"));
		});
	}
}

?>