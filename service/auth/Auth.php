<?php

class Auth{
    static public function logout(){
        if (!isset($_SESSION)){
            session_start();
        }
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);

        session_destroy();

        header("Location: ../user/login.php");
        exit;
    }

    static public function setLoginSession($user_id, $user_name){
        if (!isset($_SESSION)){
            session_start();
        }
		$_SESSION['user_id'] = $user_id;
		$_SESSION['user_name'] = $user_name;
    }


    static public function getUserName(){
        if (!isset($_SESSION)){
            session_start();
        }
        if(!isset($_SESSION['user_id'])){
            header('Location: ../user/login.php');
            exit;
        }
        return $_SESSION['user_name'];
    }

    static public function getUserId(){
        if (!isset($_SESSION)){
            session_start();
        }
        if(!isset($_SESSION['user_id'])){
            header('Location: ../user/login.php');
            exit;
        }
        return $_SESSION['user_id'];
    }
}


?>
