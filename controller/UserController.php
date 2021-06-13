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

		$valid_data = $validation->getValidData();

		Auth::setLoginSession($valid_data['id'], $valid_data['name'], $valid_data['icon_path']); 

		header("Location: ../todo/index.php");		
		exit;	
	}


	public function signup(){
		//$user_id = $_POST['user_id'];
		$user_id = MailRegister::getRegisterMail();
		$result = User::findById($user_id);

		if ($result !== false){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['登録済のユーザーIDです。'];
			header("Location: ./signup.php");
			exit;
		}

		$token = MailRegister::getRegisterToken();
		$result = MailRegister::validateToken($token);
		if (!$result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['URLが不正です。'];
			header("Location: ./signup.php");
			exit;
		}

		$validation = new UserValidation;
		//$validation->setId($_POST['user_id']);
		$validation->setId($user_id);
		$validation->setPassword1($_POST['password1']);
		$validation->setPassword2($_POST['password2']);
		$validation->setName($_POST['name']);
		$validation->setDetail($_POST['detail']);

		$result = $validation->checkSignup();

		if (!$result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = $validation->getErrorMessages();
			header("Location: ./signup_form.php&" . $token);
			exit;
		}

		$data = $validation->getValidData();

		//画像の保存
		$icon_path = null;
		if(!empty($_FILES['avatar']) && is_uploaded_file($_FILES['avatar'][tmp_name])){
			$icon_path = sprintf('../../images/avatar/%s.%s', uniqid(), substr(strrchr($_FILES['avatar']['name'], '.'), 1));
			move_uploaded_file($_FILES['avatar']['tmp_name'], $icon_path);
		}
		$data['icon_path'] = $icon_path;

		$user = new User;
		$user->setData($data);
		$result = $user->registration();

		if ($result === false){
			$error_msgs = $user->getErrorMessages();

			//セッションにエラーメッセージを追加
			session_start();
			$_SESSION['error_msgs'] = $error_msgs;
			
			$params = sprintf("?token=%s&user_id=%s&name=%s&detail=%s", 
				$token, $data['id'], $data['name'], $data['detail']);
			header("Location: ./signup_form.php" . $params);
			exit;	
		}

		Auth::setLoginSession($data['id'], $data['name'], $data['icon_path']);

		//本登録されたので仮登録中のメールアドレスを削除
		MailRegister::registrationFinished();

		header("Location: ../todo/index.php");
		exit;	
	}


	public function detail(){
		$user_id = Auth::getUserId();

		$user_detail = User::findById($user_id);

		return $user_detail;

	}

	public function edit(){
		$user_id = Auth::getUserId();

		$user_detail = User::findById($user_id);

		if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
			return $user_detail;
		}

		$validation = new UserValidation;
		$validation->setId($_POST['user_id']);
		$validation->setPassword1($_POST['password1']);
		$validation->setPassword2($_POST['password2']);
		$validation->setName($_POST['name']);
		$validation->setDetail($_POST['detail']);

		$result = $validation->checkSignup();

		if (!$result){
			$error_msgs = $validation->getErrorMessages();

			session_start();
			$_SESSION['error_msgs'] = $error_msgs;

			$params = sprintf("?name=%s&detail=%s", $data['name'], $data['detail']);
			header("Location: ./edit.php" . $params);
			exit;	

		}

		$data = $validation->getValidData();

		//画像の保存
		$icon_path = null;
		if(!empty($_FILES['avatar']) && is_uploaded_file($_FILES['avatar'][tmp_name])){
			$icon_path = sprintf('../../images/avatar/%s.%s', uniqid(), substr(strrchr($_FILES['avatar']['name'], '.'), 1));
			move_uploaded_file($_FILES['avatar']['tmp_name'], $icon_path);
		}
		if (isset($user_detail['icon_path'])){
			unlink($user_detail['icon_path']);
		}
		$data['icon_path'] = $icon_path;


		$user = new User;
		$user->setData($data);
		$result = $user->update();

		if ($result === false){
			$error_msgs = $user->getErrorMessages();

			//セッションにエラーメッセージを追加
			session_start();
			$_SESSION['error_msgs'] = $error_msgs;
			
			$params = sprintf("?name=%s&detail=%s", $data['name'], $data['detail']);
			header("Location: ./edit.php" . $params);
			exit;
		}

		Auth::setLoginSession($data['id'], $data['name'], $data['icon_path']);

		header("Location: ./detail.php");
		exit;	

	}


}

?>