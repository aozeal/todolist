<?php

class Auth{
    static public function logout(){
        session_start();
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);

        header("Location: ../user/login.php");
    }

    static public function setLoginSession($user_id, $user_name){
        session_start();
		$_SESSION['user_id'] = $user_id;
		$_SESSION['user_name'] = $user_name;
    }


    static public function getUserName(){
        session_start();
        if(!isset($_SESSION['user_id'])){
            header('Location: ../user/login.php');
            exit;
        }
        return $_SESSION['user_name'];
    }

    static public function getUserId(){
        session_start();
        if(!isset($_SESSION['user_id'])){
            header('Location: ../user/login.php');
            exit;
        }
        return $_SESSION['user_id'];
    }
}


?>
