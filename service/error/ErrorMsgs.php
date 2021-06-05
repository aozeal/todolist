<?php

class ErrorMsgs{
    static public function getErrorMessages(){
        if (!isset($_SESSION)){
            session_start();
        }

        $error_msgs = [];
        if (isset($_SESSION['error_msgs'])){
            $error_msgs = $_SESSION['error_msgs'];
            unset($_SESSION['error_msgs']);
    
        }

        return $error_msgs;
    }


}


?>
