<?php


class MailRegister{
	static public function register(){
		$user_id = $_POST['user_id'];

		$result = User::findById($user_id);

		if ($result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['すでに登録済のユーザーIDです。'];
			header("Location: ./mail_register.php");
			exit;
		}

		//token作る
		$token = MailRegister::createToken();

		//メールを送る
		$to = $user_id;
		$subject = "Access this URL to finish signup";
		$message = "http://localhost:8000/app/view/user/signup_form.php?token=" . $token;
		$headers = "From: mail_auth@todo_test.com";
		mail($to, $subject, $message, $headers);


		//セッションにトークンとメールアドレスを記録する
		//まだuser_idを登録する前なので$_SESSION['user_id']は使わない
		//（他のURLを入れてもログインできないように）
		MailRegister::setRegisterToken($token);
		MailRegister::setRegisterMail($user_id);

		header("Location: ./registed_message.php");
		exit;

	}

	static function createToken(){
		return uniqid();
	}

	static function setRegisterToken($token){
		if (!isset($_SESSION)){
			session_start();
		}
		$_SESSION['token'] = $token;

	}

	static function getRegisterToken(){
		if (!isset($_SESSION)){
			session_start();
		}
		return $_SESSION['token'];

	}

	static function setRegisterMail($user_id){
		if (!isset($_SESSION)){
			session_start();
		}
		$_SESSION['unregisted_user_id'] = $user_id;

	}

	static function getRegisterMail(){
		if (!isset($_SESSION)){
			session_start();
		}
		return $_SESSION['unregisted_user_id'];
	}


	static function validateToken($token){
		if (!isset($_SESSION)){
			session_start();
		}

		if ($token !== $_SESSION['token']){
			return false;
		}
		return true;
	}

	static function registrationFinished(){
		if (!isset($_SESSION)){
			session_start();
		}
		unset($_SESSION['unregisted_user_id']);
		unset($_SESSION['token']);
	}

}


?>