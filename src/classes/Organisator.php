<?php
class Organisator extends User {
	
	private $admin;
	private $validated;

    public function __construct($id, $mail, $admin = false, $validated = false){
        parent::__construct($id, $mail);
		$this->admin = $admin;
		$this->validated = $validated;
    }
	
    public static function getFromDatabaseByID($conn, $id){
		$sql_get_organisator = "SELECT * FROM organisators WHERE user_id = ".$id;
		$sql_get_user = "SELECT * FROM users WHERE user_id = ".$id;
		$organisator = mysqli_query($conn,$sql_get_organisator);
		$user = mysqli_query($conn,$sql_get_user);
		if ($organisator != false && $user != false) {
			$organisator_pole = mysqli_fetch_array($organisator);
			$user_pole = mysqli_fetch_array($user);
			return new self($id, $user_pole['mail'], $organisator_pole['admin'], $organisator_pole['validated']);
		}
		return null;
    }
	
	public function isAdmin() {
		return $this->admin;
	}
	
	public function getId() {
		return $this->id;
	}

	public function getShortName()
	{
		return "organisator";
	}
	
}
?>