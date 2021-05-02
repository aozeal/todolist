<?php


class Todo{
	public $title;
	public $detail;
	public $deadline_at;

	public function getTitle(){
		return $this->title;
	}

	public function setTitle($title){
		$this->title = $title;
	}

	public function getDetail(){
		return $this->detail;
	}

	public function setDetail($detail){
		$this->detail = $detail;
	}

	public function getDeadline(){
		return $this->deadline_at;
	}

	public function setDeadline($deadline_at){
		#$this->deadline_at = $deadline_at;
		$this->deadline_at = null;
	}


	public static function findByQuery($query){
		$dbh = new PDO(DSN, USERNAME, PASSWORD);
		$stmh = $dbh->query($query);

		if($stmh){
			$result = $stmh->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			$result = [];
		}
		return $result;
	}

	//全レコードを取得
	public static function findAll(){
		#user_idは後ほど修正
		$query = "SELECT * FROM todos WHERE user_id='TestUser'";
		return self::findByQuery($query);
	}


	//詳細データを取得
	public static function findById($todo_id){
		#user_idは後ほど修正
		$query = "SELECT * FROM todos WHERE id=${todo_id} AND user_id='TestUser'";

		$dbh = new PDO(DSN, USERNAME, PASSWORD);
		$stmh = $dbh->query($query);

		if($stmh){
			$result = $stmh->fetch(PDO::FETCH_ASSOC);
		}
		else{
			$result = [];
		}
		return $result;
	}

	public function save(){
		$query = "INSERT INTO todos (user_id, title, detail, created_at, updated_at) VALUES ('TestUser', '{$this->title}', '{$this->detail}',  NOW(), NOW());";
		$dbh = new PDO(DSN, USERNAME, PASSWORD);
		$stmh = $dbh->prepare($query);
		$result = $stmh->execute();

		return $result;
	}

}


?>