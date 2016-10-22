<?php
header('Content-type: text/plain; charset=utf-8');
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
session_start();

error_reporting(0);
@ini_set('display_errors', 0);
require_once(dirname(__FILE__)."/functions.php");
if ($link = db_connect()) {
    $mail = strtolower($_POST['mail']);
    $password = $_POST['password'];
    $sql = "SELECT u.user_id, u.mail, u.password, t.name, t.description, t.sk_league, o.admin, o.validated
            FROM users u
            LEFT OUTER JOIN teams t ON (t.user_id = u.user_id)
            LEFT OUTER JOIN organisators o ON (o.user_id = u.user_id)
            WHERE LOWER(u.mail) = '$mail'";
    $result = mysqli_query($link, $sql);
    $error = null;
    if ($row = mysqli_fetch_array($result)) {
        if (md5($password) == $row['password']) {
            if (is_null($row['admin'])) {
                $_SESSION['loggedUser'] = new Team($row['user_id'], $row['mail'], $row['name'], $row['description'], $row['sk_league']);
            }
            else {
                if (!$row['admin']) {
                    if ($row['validated']) {
                        $_SESSION['loggedUser'] = new Jury($row['user_id'], $row['mail'], $row['validated']);
                    }
                    else {
                        $error = 'err-jury-acc-not-validated';
                    }
                }
                else {
                    $_SESSION['loggedUser'] = new Administrator($row['user_id'], $row['mail']);
                }
            }
        }
        else {
            $error = 'err-wrong-password';
        }
    }
    else {
        $error = 'err-non-existent-acc';
    }
}
if ($error !== null){
    echo $error;
}
die;
?>