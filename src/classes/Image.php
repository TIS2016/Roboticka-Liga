<?php
class Image extends Attachment {
	
	protected static $icon = "";

    public function __construct($id, $context_id, $name){
        parent::__construct($id, $context_id, $name);
    }
	
	public static function setIcon($ic) {
		Image::$icon = $ic;
	}
	
	public static function getIcon() {
		return Image::$icon;
	}

}
?>