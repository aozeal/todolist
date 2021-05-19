<?php


class UserController{
	public function login(){

		$user_id = $_POST['user_id'];
		$password = $_POST['password'];

		$result = User::findById($user_id);

		if ($result === false){
			session_start();
			$_SESSION['error_msgs'] = ['ユーザーIDが存在しません。'];
			header("Location: ./login.php");
			exit;
		}

		$validation = new UserValidation;
		$validation->setLoginId($user_id);
		$validation->setLoginPassword($password);
		$validation->setData($result);
		$result = $validation->checkLogin();

		if (!$result){
			session_start();
			$_SESSION['error_msgs'] = $validation->getErrorMessages();
			header("Location: ./login.php");
			exit;
		}

		session_start();
		$_SESSION['user_id'] = $user_id;

		header("Location: ../todo/index.php");		
		exit;	
	}


	public function signup(){
		$user_id = $_POST['user_id'];
		$result = User::findById($user_id);

		if ($result !== false){
			session_start();
			$_SESSION['error_msgs'] = ['登録済のユーザーIDです。'];
			header("Location: ./signup.php");
			exit;
		}

		$validation = new UserValidation;
		$validation->setId($_POST['user_id']);
		$validation->setPassword1($_POST['password1']);
		$validation->setPassword2($_POST['password2']);
		$validation->setName($_POST['name']);
		$validation->setDetail($_POST['detail']);

		$result = $validation->checkSignup();

		if (!$result){
			session_start();
			$_SESSION['error_msgs'] = $validation->getErrorMessages();
			header("Location: ./signup.php");
			exit;
		}

		$data = $validation->getValidData();

		$user = new User;
		$user->setData($data);
		$result = $user->registration();

		if ($result === false){
			$error_msgs = $user->getErrorMessages();

			//セッションにエラーメッセージを追加
			session_start();
			$_SESSION['error_msgs'] = $error_msgs;
			
			$params = sprintf("?user_id=%s&name=%s&detail=%s", 
				$data['id'], $data['name'], $data['detail']);
			header("Location: ./signup.php" . $params);
			exit;	
		}

		session_start();
		$_SESSION['user_id'] = $data['id'];

		header("Location: ../todo/index.php");
		exit;	
	}


}

?>