<?php

class Auth{
    public const DEFAULT_ICON_PATH = "../../assets/images/avatar/default.png";

    static public function logout(){
        if (!isset($_SESSION)){
            session_start();
        }
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);

        session_destroy();

    }

    static public function setLoginSession($user_id, $user_name, $icon_path){
        if (!isset($_SESSION)){
            session_start();
        }
		$_SESSION['user_id'] = $user_id;
		$_SESSION['user_name'] = $user_name;
        $_SESSION['icon_path'] = $icon_path;
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

    static public function getIconPath(){
        if (!isset($_SESSION)){
            session_start();
        }
        if (!isset($_SESSION['icon_path'])){
            return Auth::DEFAULT_ICON_PATH;
        }
        return $_SESSION['icon_path'];
    }
}


?>
