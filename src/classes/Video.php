<?php
class Video extends Attachment {
	
	protected static $icon = "";

    public function __construct($id, $context_id, $name){
        parent::__construct($id, $context_id, $name);
    }
	
	public static function setIcon($ic) {
		Video::$icon = $ic;
	}
	
	public static function getIcon() {
		return Video::$icon;
	}

}
?>