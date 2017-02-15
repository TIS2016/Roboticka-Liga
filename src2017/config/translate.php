<?php

class Translate {
	function init_translate() {
		
		// Error 404
		$this->set("error-404-title", "Stránka sa nenašla!", "Page not found!");
		$this->set("error-404-content", "Skúste to neskôr.", "Try later again.");
		
		
		// Header
		$this->set("title", "Letná liga FLL", "Summer league FLL");
		$this->set("keywords", "fll, lego, letna liga");
		$this->set("author", "TIS TEAM");

		
		// Navigator
		$this->set("nav_band", "Liga FLL", "League FLL");
		$this->set("nav_assingments", "Zadania", "Assignments");
		$this->set("nav_results",	"Výsledky", "Results");
		$this->set("nav_archive", "Archív", "Archive");
		$this->set("nav_language", "English", "Slovenský");
		$this->set("nav_login", "Prihlásiť", "Login");
		
		
		// Navigator dropdown menu		
		$this->set("nav_assignments_assignment", "Zadanie", "Assignment");
		$this->set("nav_assignments_overview", "Prehľad zadaní", "Assignments overview");
		$this->set("nav_assignments_results", "Výsledky", "Results");


		// Navigator - Login
		$this->set("nav_account_mail", "E-mail");
		$this->set("nav_account_password", "Heslo", "Password");
		$this->set("nav_account_registration", "Registrácia", "Registration");
		$this->set("nav_account_submit", "Prihlásiť", "Login");
		$this->set("nav_account_logout", "Odhlásiť", "Logout");
		
		
		// Assignment content
		$this->set("assignment_task", "Úloha", "Task");
		$this->set("assignment_deadline", "Riešenie možno odovzdávať do", "Deadline of this assignment is set to");
		$this->set("assignment_time_format", "d.m.Y H:i:s", "Y-m-d H:i:s");
		$this->set("assignment_solutions", "Riešenia", "Solutions");
	}
}

?>