<?php

class TodoController{
	public function index(){
		$todo_list = Todo::findAll();
		
		return $todo_list;
	}

	public function detail(){
		$todo_id = $_GET['todo_id'];

		$todo_detail = Todo::findById($todo_id);

		return $todo_detail;
	}

	public function new(){
		
		$data = array(
			'title' => $_POST['title'],
			'detail' => $_POST['detail'],
			'deadline_at' => $_POST['deadline_at']
		);
		$title = $data['title'];
		$detail = $data['detail'];
		$deadline_at = $data['deadline_at'];
		
		$validation = new TodoValidation();
		$validation->setData($data);

		if ($validation->check() === false){
			$error_msgs = $validation->getErrorMessages();

			//セッションにエラーメッセージを追加
			session_start();
			$_SESSION['error_msgs'] = $error_msgs;

			$params = sprintf("?title=%s&detail=%s&deadline_at=%s", 
				$title, $detail, $deadline_at);
			header("Location: ./new.php" . $params);		
			exit;	
		}

		$validate_data = $validation->getData();
		$title = $validate_data['title'];
		$detail = $validate_data['detail'];
		$deadline_at = $validate_data['deadline_at'];

		$todo = new Todo;
		$todo->setTitle($title);
		$todo->setDetail($detail);
		$todo->setDeadline($deadline_at);

		$result = $todo->save();

		if ($result === false){
			$params = sprintf("?title=%s&detail=%s&deadline_at=%s", 
				$title, $detail, $deadline_at);
			header("Location: ./new.php" . $params);
			exit;	
		}

		header("Location: ./index.php");
	}


}

?>