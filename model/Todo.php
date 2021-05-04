<?php


class Todo{
	public $id;
	public $title;
	public $detail;
	public $deadline_at;

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

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
		$stmt = $dbh->query($query);

		if($stmt){
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
		$stmt = $dbh->query($query);

		if($stmt){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		else{
			$result = [];
		}
		return $result;
	}


	//レコードの存在を確認
	public static function isExistById($todo_id){
		$query = "SELECT * FROM todos WHERE id=${todo_id}";

		$dbh = new PDO(DSN, USERNAME, PASSWORD);
		$stmt = $dbh->query($query);

		if(!$stmt){
			return false;
		}
		return true;
	}



	public function save(){
		
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$query1 = sprintf(
				"INSERT INTO todos (user_id, title, detail, created_at, updated_at) VALUES ('TestUser', '%s', '%s',  NOW(), NOW());",
				$this->title, $this->detail
			);
			$stmt1 = $dbh->prepare($query1);
			$stmt1->execute();

			$todo_id = $dbh->lastInsertId();
			$query2 = sprintf(
				"INSERT INTO todo_histories (todo_id, user_id, title, detail, created_at, updated_at) VALUES (%d, 'TestUser', '%s', '%s',  NOW(), NOW());",
				$todo_id, $this->title, $this->detail
			);
			$stmt2 = $dbh->prepare($query2);
			$stmt2->execute();

			$dbh->commit();
			
			$result = true;
		} catch(PDOException $e){
			$dbh->rollBack();

			echo $e->getMessage();
			$result = false;
		}

		return $result;
	}


	public function update(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$query1 = sprintf(
				"UPDATE todos SET title='%s', detail='%s', updated_at=NOW() WHERE id=%d;",
				$this->title, $this->detail, $this->id
			);
			error_log($query1);
			$stmt1 = $dbh->prepare($query1);
			$stmt1->execute();

/*			//本来は一度selectして全内容を読み取ってから書き込みだが、後ほど修正。created_atとかもそれに伴って修正する点に注意
			$query2 = sprintf(
				"INSERT INTO todo_histories (todo_id, user_id, title, detail, updated_at) VALUES (%d, 'TestUser', '%s', '%s', NOW());",
				$this->id, $this->title, $this->detail
			);
			$stmt2 = $dbh->prepare($query2);
			$stmt2->execute();
*/
			$dbh->commit();
			
			$result = true;
		} catch(PDOException $e){
			$dbh->rollBack();

			echo $e->getMessage();
			$result = false;
		}

		return $result;
	}

	//deleteは論理削除にしたいのであとで変更する
	public function delete(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);

			$dbh->beginTransaction();
			$query1 = sprintf("DELETE FROM todos WHERE id=%d", $this->id);

			$stmt1 = $dbh->prepare($query1);
			$result = $stmt1->execute();

			$dbh->commit();

		} catch (PDOException $e){
			$dbh->rollBack();
			echo $e->getMessage();
			$result = false;
		}

		return $result;
	}

}


?>