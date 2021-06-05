<?php


class TodoHistoryController{

	public $target_date;

	public function getTargetDate(){
		return $this->target_date;
	}

	public function index(){
		$user_id = Auth::getUserId();

		$input_date = '';
		if (isset($_GET['target_date'])){
			$input_date = $_GET['target_date'];
		}

		$validation = new TodoHistoryValidation;
		$validation->setTargetDate($input_date);
		$result = $validation->check();

		if (!$result){

			if (!isset($_SESSION)){
				session_start();
			}
			$_SESSION['error_msgs'] = $validation->getErrorMessages();
		}

		$this->target_date = $validation->getTargetDate();

		$history_list = TodoHistory::findAll($user_id, $this->target_date);

		return $history_list;
	}


	public function detail(){
		$user_id = Auth::getUserId();

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