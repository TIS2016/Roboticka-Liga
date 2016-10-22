<?php
class Jury extends Organisator {
    private $validated;

    public function __construct($id, $mail, $validated){
        parent::__construct($id, $mail);
        $this->validated = $validated;
    }

}
?>