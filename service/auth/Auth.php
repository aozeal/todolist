<?php

class Auth{
    static public function logout(){
        session_start();
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);

        header("Location: ../user/login.php");
    }


    static public function checkLoginSession(){
        session_start();
        if(!isset($_SESSION['user_id'])){
            header('Location: ../user/login.php');
            exit;
        }
    }
}


?>
