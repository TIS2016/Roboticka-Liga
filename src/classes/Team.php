<?php
require_once(dirname(__FILE__)."/../includes/functions.php");

class Team extends User {
    protected $name;
    protected $description;
    protected $skLeague;

	public function __construct($id, $mail, $name, $description, $skLeague){
		parent::__construct($id, $mail);
		$this->name = $name;
		$this->description = $description;
		$this->skLeague = $skLeague;
	}

	public static function getFromDatabaseByID($conn, $id){
		$sql_get_team = "SELECT u.mail as 'mail',t.name as 'name', t.description as 'desc', t.sk_league as 'sk' FROM users u, teams t WHERE t.user_id = u.user_id AND u.user_id = ".$id;
		$team = mysqli_query($conn,$sql_get_team);
		if ($team != false) {
			$team_pole = mysqli_fetch_array($team);
			return new self($id, $team_pole['mail'],$team_pole['name'],$team_pole['desc'],$team_pole['sk']);
		}
		return null;
    }

	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}

		return null;
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}
	
	public function getId() {
		return $this->id;
	}

	public function getShortName()
	{
		return "team";
	}
	
	public function getName(){
    return $this->name;
  }

	public function getDescription() {
		return $this->description;
	}
}
?>