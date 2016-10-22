<?php

abstract class User
{
    protected $id;
    protected $mail;

    public function __construct($id, $mail){
        $this->id = $id;
        $this->mail = $mail;
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

    public static function getById($id) {
        if ($db = db_connect()){
            $result = mysqli_query($db, "SELECT * FROM USERS WHERE user_id=$id");
            if ($row = mysqli_fetch_array($result)) {
                switch ($row['type']){
                    case 0:
                        $user = new Team($row['user_id'], $row['mail'], $row['name'], '', false);
                        break;
                    case 1:
                        $user = new Administrator($row['user_id'], $row['mail']);
                        break;
                    case 2:
                        $user = new Jury($row['user_id'], $row['mail'], true);
                        break;
                    default:
                        return null;
                }

                return $user;
            }
        }
        return null;
    }

}

?>