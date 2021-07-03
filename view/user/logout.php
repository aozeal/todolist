<?php

require_once('../../service/auth/Auth.php');

Auth::logout();
header("Location: ../user/login.php");

?>