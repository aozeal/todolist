<?php

class TodoController{
	public function index(){
		$todo_list = Todo::findAll();
		
		return $todo_list;	
	}


}

?>