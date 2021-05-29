<?php


class TodoHistoryController{

	public $target_date;

	public function getTargetDate(){
		return $this->target_date;
	}

	public function index(){
		session_start();
		$user_id = $_SESSION['user_id'];


		$input_date = $_GET['target_date'];

		$validation = new TodoHistoryValidation;
		$validation->setTargetDate($input_date);
		$result = $validation->check();

		if (!$result){
			session_start();
			$_SESSION['error_msgs'] = $validation->getErrorMessages();
		}

		$this->target_date = $validation->getTargetDate();

		$history_list = TodoHistory::findAll($user_id, $this->target_date);

		return $history_list;
	}


	public function detail(){
		session_start();
		$user_id = $_SESSION['user_id'];

		$history_id = $_GET['id'];
		$input_date = $_GET['target_date'];

		$validation = new TodoHistoryValidation;
		$validation->setTargetDate($input_date);
		$result = $validation->check();

		if (!$result){
			session_start();
			$_SESSION['error_msgs'] = $validation->getErrorMessages();
			header("Location: ./index.php");
			exit();
		}
		$this->target_date = $validation->getTargetDate();

		
		$history_list = TodoHistory::findById($history_id, $user_id);

		return $history_list;
	}

}

?>