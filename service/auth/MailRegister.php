<?php


class MailRegister{
	public function register(){
		$user_id = filter_input(INPUT_POST, 'user_id');

		$result = User::findById($user_id, true);

		if ($result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['すでに登録済のユーザーIDです。'];
			return;
		}

		//token作る
		$user = new User;
		$user->createToken();
		$token = $user->getToken();


		//メールアドレスを記録する
		$user->setMailAddress($user_id);

		//DBに登録
		$result = $user->temporaryRegistration();
		if (!$result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['DBの登録に失敗しました。'];
			return;
		}

		//メールを送る
		$to = $user_id;
		$subject = "Access this URL to finish signup";
		$message = "http://localhost:8000/app/view/user/signup_form.php?token=" . $token;
		$headers = "From: mail_auth@todo_test.com";
		mail($to, $subject, $message, $headers);

		header("Location: ./registed_message.php");
		exit;

	}


	public function validateToken(){
		$token = filter_input(INPUT_GET, 'token');

		$user = new User;
		$user_id = $user->getRegistedMail($token);

		if (!$user_id){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['URLが正しくありません。'];
			header("Location: ../error/token_error.php");
			exit;
		}

		//URL直打ちでsignupされるのを防ぐためにセッションの情報を使う
		self::createSession($user_id);

		return $user_id;
	}

	static function createSession($mail){
		if (!isset($_SESSION)){
			session_start();
		}
		$_SESSION['mail'] = $mail;
	}

	static function checkSession($mail){
		if (!isset($_SESSION)){
			session_start();
		}

		if (empty($_SESSION['mail'])){
			return false;
		}

		if ($_SESSION['mail'] !== $mail){
			return false;
		}

		return true;
	}

	static function destroySession(){
		if (!isset($_SESSION)){
			session_start();
		}
		unset($_SESSION['mail']);
	}

	public function resendToken(){
		$user_id = filter_input(INPUT_POST, 'user_id');

		$result = User::findById($user_id);

		if (!$result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['このメールアドレスは未登録です'];
			return;
		}

		//token作る
		$user = new User;
		$user->createToken();
		$token = $user->getToken();


		//メールアドレスを記録する
		$user->setMailAddress($user_id);

		//DBに登録
		$result = $user->temporaryTokenUpdate();
		if (!$result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['DBの登録に失敗しました。'];
			return;
		}

		//メールを送る
		$to = $user_id;
		$subject = "Access this URL to reset password";
		$message = "http://localhost:8000/app/view/user/pw_form.php?token=" . $token;
		$headers = "From: mail_auth@todo_test.com";
		mail($to, $subject, $message, $headers);

		header("Location: ./registed_message.php");
		exit;

	}

}


?>