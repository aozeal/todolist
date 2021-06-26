<?php


class TodoController{
	public function index(){
		$keyword = filter_input(INPUT_GET, 'keyword');
		$view_done = filter_input(INPUT_GET, 'view_done');
		$view_deadline = filter_input(INPUT_GET, 'view_deadline');
		$sort_type = filter_input(INPUT_GET, 'sort_type');
		$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
		if (!$page){
			$page = 1;
		}

		//セッションからユーザー情報を取得
		$user_id = Auth::getUserId();
		$user_name = Auth::getUserName();
		$icon_path = Auth::getIconPath();

		//ページ情報を取得
		$total_row = Todo::countRowWithCondition($user_id, $view_done, $view_deadline, $keyword);
		$total_pages = ceil($total_row / Todo::MAX_ROW_PER_PAGE);

		//ToDOデータを取得
		$todo_list = Todo::findAllWithCondition($user_id, $page, $view_done, $view_deadline, $sort_type, $keyword);


		//viewに渡すデータを作成
		$data = array();
		$data['todo_list'] = $todo_list;

		$data['user'] = array();
		$data['user']['id'] = $user_id;
		$data['user']['name'] = $user_name;
		$data['user']['icon_path'] = $icon_path;

		$data['view_done'] = $view_done;
		$data['view_deadline'] = $view_deadline;
		$data['sort_type'] = $sort_type;
		$data['keyword'] = $keyword;
		$data['page'] = $page;
		$data['total_pages'] = $total_pages;

		$data['now'] = new DateTime();
		
		$data['error_msgs'] = ErrorMsgs::getErrorMessages();

		return $data;
	}


	public function detail(){
		$user_id = Auth::getUserId();

		$todo_id = $_GET['id'];

		$todo_detail = Todo::findById($todo_id, $user_id);

		return $todo_detail;
	}

	public function new(){
		$user_id = Auth::getUserId();
		
		$data = array(
			'title' => $_POST['title'],
			'detail' => $_POST['detail'],
			'deadline_at' => $_POST['deadline_at'],
			'user_id' => $user_id
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
		$user_id = $validate_data['user_id'];

		$todo = new Todo;
		$todo->setTitle($title);
		$todo->setDetail($detail);
		$todo->setDeadline($deadline_at);
		$todo->setUserId($user_id);

		$result = $todo->save();

		if ($result === false){
			$error_msgs = $todo->getErrorMessages();

			//セッションにエラーメッセージを追加
			session_start();
			$_SESSION['error_msgs'] = $error_msgs;
			
			$params = sprintf("?title=%s&detail=%s&deadline_at=%s", 
				$title, $detail, $deadline_at);
			header("Location: ./new.php" . $params);
			exit;	
		}


		header("Location: ./index.php");
	}

	public function edit(){
		$user_id = Auth::getUserId();

		$todo_id = $_GET['id'];
		$todo_detail = Todo::findById($todo_id, $user_id);

		if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
			return $todo_detail;
		}

		$data = array(
			'id' => $_POST['id'],
			'title' => $_POST['title'],
			'detail' => $_POST['detail'],
			'deadline_at' => $_POST['deadline_at'],
			'user_id' => $user_id
		);

		$validation = new TodoValidation();
		$validation->setData($data);

		if ($validation->check() === false){
			$error_msgs = $validation->getErrorMessages();

			//セッションにエラーメッセージを追加
			session_start();
			$_SESSION['error_msgs'] = $error_msgs;

			$params = sprintf("?id=%d&title=%s&detail=%s&deadline_at=%s", 
				$data['id'], $data['title'], $data['detail'], $data['deadline_at']);
			header("Location: ./edit.php" . $params);		
			exit;
		}

		$validate_data = $validation->getData();
		$id = $validate_data['id'];
		$title = $validate_data['title'];
		$detail = $validate_data['detail'];
		$deadline_at = $validate_data['deadline_at'];
		$user_id = $validate_data['user_id'];

		$todo = new Todo;
		$todo->setId($id);
		$todo->setTitle($title);
		$todo->setDetail($detail);
		$todo->setDeadline($deadline_at);
		$todo->setUserId($user_id);

		$result = $todo->update();

		if ($result === false){
			session_start();
			$_SESSION['error_msgs'] = "データの登録に失敗しました。";
			
			$params = sprintf("?id=%d&title=%s&detail=%s&deadline_at=%s", 
				$id, $title, $detail, $deadline_at);
			header("Location: ./edit.php" . $params);
			exit;
		}

		header("Location: ./detail.php?id=${id}");
		exit;
	}

	public function delete(){
		$user_id = Auth::getUserId();

		$todo_id = $_GET['todo_id'];
		$is_exist = Todo::isExistById($todo_id, $user_id);
		if(!$is_exist){
			session_start();
			$_SESSION['error_msgs'] = [
				sprintf ("id=%s, user_id=%sに該当するレコードが存在しませんでした", $todo_id, $user_ie)
			];
			header("Location: ./index.php");
			exit;
		}

		$todo = new Todo;
		$todo->setId($todo_id);
		$todo->setUserId($user_id);
		$result = $todo->delete();
		if ($result === false){
			session_start();
			$_SESSION['error_msgs'] = [sprintf("削除に失敗しました。id=%s", $todo_id)];
			header("Location: ./index.php");
			exit;
		}

		header("Location: ./index.php");
	}


	public function done(){
		$user_id = Auth::getUserId();

		$todo_id = $_GET['todo_id'];
		$is_exist = Todo::isExistById($todo_id, $user_id);

		if(!$is_exist){
			session_start();
			$_SESSION['error_msgs'] = [
				sprintf ("id=%s, user_id=%sに該当するレコードが存在しませんでした", $todo_id, $user_id)
			];
			header("Location: ./index.php");
			exit;
		}

		$todo = new Todo;
		$todo->setId($todo_id);
		$result = $todo->done();
		if ($result === false){
			session_start();
			$_SESSION['error_msgs'] = [sprintf("削除に失敗しました。id=%s", $todo_id)];
		}

		header("Location: ./index.php");
	}


}

?>