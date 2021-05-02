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
		$todo = new Todo;
		$todo->setTitle($_POST['title']);
		$todo->setDetail($_POST['detail']);
		$todo->setDeadline($_POST['deadline_at']);

		$result = $todo->save();

		if ($result){
			header("Location: ./index.php");
		}
		else{
			header("Location: ./new.php");
		}
	}


}

?>