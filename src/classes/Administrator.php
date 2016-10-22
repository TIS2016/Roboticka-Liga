<?php
class Administrator extends Organisator {
    public function __construct($id, $mail){
        parent::__construct($id, $mail, true);
    }

    public function getShortName()
	{
		return "admin";
	}
}
?>