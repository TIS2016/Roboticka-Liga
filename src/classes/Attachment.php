<?php
abstract class Attachment {
	
	protected $id;
	protected $context_id;
	protected $name;
	    
    public function __construct($id, $context_id, $name) {
        $this->id = $id;
		$this->context_id = $context_id;
		$this->name = $name;
    }
	
	public function getId() {
		return $this->id;
	}
	
	public function getContext_id() {
		return $this->context_id;
	}
	
	public function getName() {
		return $this->name;
	}
}
?>