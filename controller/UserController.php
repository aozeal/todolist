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
		$user_id = filter_input(INPUT_POST, 'user_id');

		$result = MailRegister::checkSession($user_id);
		if (!$result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['不正なデータです。'];
			header("Location: ../error/token_error.php");
			exit;
		}


		$result = User::findById($user_id, true);

		//encrypted_passwordが存在するのは登録済のため
		if ($result['encrypted_password']){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['登録済のユーザーIDです。'];
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
			$icon_path = sprintf('../../assets/images/avatar/%s.%s', uniqid(), pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENTION) );
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
			
			$token = filter_input(INPUT_GET, 'token');
			$params = sprintf("?token=%s&user_id=%s&name=%s&detail=%s", 
				$token, $data['id'], $data['name'], $data['detail']);
			header("Location: ./signup_form.php" . $params);
			exit;	
		}

		Auth::setLoginSession($data['id'], $data['name'], $data['icon_path']);

		//本登録されたので仮登録用のセッションを削除
		MailRegister::destroySession();

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
			$icon_path = sprintf('../../assets/images/avatar/%s.%s', uniqid(), pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENTION) );
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


	public function passwordReset(){
		$user_id = filter_input(INPUT_POST, 'user_id');
		$token = filter_input(INPUT_POST, 'token');

		$result = MailRegister::checkSession($user_id);
		if (!$result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = ['不正なデータです。'];
			header("Location: ../error/token_error.php");
			exit;
		}


		$result = User::findById($user_id);

		$validation = new UserValidation;
		$validation->setId($user_id);
		$validation->setPassword1(filter_input(INPUT_POST, 'password1'));
		$validation->setPassword2(filter_input(INPUT_POST, 'password2'));
		$validation->setName($result['name']);
		$validation->setDetail($result['detail']);

		$result = $validation->checkSignup();

		if (!$result){
			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = $validation->getErrorMessages();
			header("Location: ./pw_form.php?token=" . $token);
			exit;
		}

		$data = $validation->getValidData();

		$user = new User;
		$user->setData($data);
		$result = $user->passwordUpdate();

		if ($result === false){
			$error_msgs = $user->getErrorMessages();

			//セッションにエラーメッセージを追加
			session_start();
			$_SESSION['error_msgs'] = $error_msgs;
			
			$params = sprintf("?token=%s&user_id=%s", $token, $data['id']);
			header("Location: ./pw_form.php" . $params);
			exit;	
		}

		Auth::setLoginSession($data['id'], $data['name'], $data['icon_path']);

		//本登録されたので仮登録用のセッションを削除
		MailRegister::destroySession();

		header("Location: ../todo/index.php");
		exit;	
	}

	public function signout(){	
		$user_id = Auth::getUserId();

		$result = User::signout($user_id);
		if ($result === false){
			$error_msgs = $user->getErrorMessages();

			//セッションにエラーメッセージを追加
			session_start();
			$_SESSION['error_msgs'] = ['ユーザー情報の削除に失敗しました'];
			
			header("Location: ../todo/index.php");
			exit;	
		}

	}

}

?>