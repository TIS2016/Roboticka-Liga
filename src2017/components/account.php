<?php

if (Security::post("login")) {
	$_SESSION["user"] = $this->database->login(
		Security::post("mail"),
		Security::post("password")
	);
	$this->database->last_logged($_SESSION["user"]["id"]);
	header("Location: " . $this->get("current_url"));
}

if(Security::get("logout") == "ok") {
	unset($_SESSION["user"]);
	header("Location: " . $this->get("current_url", "logout="));
}

if(isset($_SESSION["user"])) {
	return $this->get("include_html", "navigator/account-logout.htm");
}

return $this->get("include_html", "navigator/account-login.htm");

?>