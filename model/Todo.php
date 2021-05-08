<?php


class Todo{
	public $id;
	public $title;
	public $detail;
	public $deadline_at;
	
	public $savedData;

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

	public function getSavedData(){
		return $this->savedData;
	}

	public function setSavedData($data){
		$this->savedData = $data;
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
		$query = "SELECT * FROM todos WHERE user_id='TestUser' AND deleted_at IS NULL AND done_at IS NULL;";
		return self::findByQuery($query);
	}

	//達成済も含めて全レコードを取得
	public static function findAllWithDone(){
		#user_idは後ほど修正
		$query = "SELECT * FROM todos WHERE user_id='TestUser' AND deleted_at IS NULL;";
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


	//todo_historiesに履歴を残すためのQuery文を作る
	private function historyQuery($data){
		$key_str = '(todo_id';
		$value_str = '(' . $data['id'];
		foreach ($data as $key=>$value){
			if ($key === "id"){
				continue;
			}

			if (is_null($value)){ //bindParamを使うかNULLをSQL側に処理させるか
				continue;
			}
			$key_str = $key_str . "," . $key;
			$value_str = $value_str . ",'" . $value . "'";
		}
		$key_str = $key_str . ')';
		$value_str = $value_str . ')';
		return sprintf("INSERT INTO todo_histories %s VALUES %s" , $key_str, $value_str);
	}


	public function save(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt1 = $dbh->prepare("INSERT INTO todos (user_id, title, detail, created_at, updated_at) VALUES ('TestUser', :title, :detail,  NOW(), NOW());");
			$stmt1->bindParam(':title', $this->title, PDO::PARAM_STR);
			$stmt1->bindParam(':detail', $this->detail, PDO::PARAM_STR);
			$stmt1->execute();

			$todo_id = $dbh->lastInsertId();
			$this->setId($todo_id);
			$query2 = sprintf("SELECT * FROM todos WHERE id=%d" , $this->id);
			$stmt2 = $dbh->prepare($query2);
			$stmt2->execute();
			$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			$this->setSavedData($result2);

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

			$stmt1 = $dbh->prepare("UPDATE todos SET title=:title, detail=:detail, updated_at=NOW() WHERE id=:id;");
			$stmt1->bindParam(':title', $this->title, PDO::PARAM_STR);
			$stmt1->bindParam(':detail', $this->detail, PDO::PARAM_STR);
			$stmt1->bindParam(':id', $this->id, PDO::PARAM_INT);
			$stmt1->execute();

			$query2 = sprintf("SELECT * FROM todos WHERE id=%d", $this->id);
			$stmt2 = $dbh->prepare($query2);
			$stmt2->execute();
			$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			$this->setSavedData($result2);

			$dbh->commit();
			
			$result = true;
		} catch(PDOException $e){
			$dbh->rollBack();

			echo $e->getMessage();
			$result = false;
		}

		return $result;
	}


	public function delete(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);

			$dbh->beginTransaction();
			$query1 = sprintf("UPDATE todos SET deleted_at=NOW() WHERE id=%d;", $this->id);

			$stmt1 = $dbh->prepare($query1);
			$stmt1->execute();

			$query2 = sprintf("SELECT * FROM todos WHERE id=%d", $this->id);
			$stmt2 = $dbh->prepare($query2);
			$stmt2->execute();
			$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			$this->setSavedData($result2);

			$dbh->commit();
			
			$result = true;
		} catch (PDOException $e){
			$dbh->rollBack();
			echo $e->getMessage();
			$result = false;
		}

		return $result;
	}


/*
	//以下は物理削除
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
*/


	public function done(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);

			$dbh->beginTransaction();
			$query1 = sprintf("UPDATE todos SET done_at=NOW() WHERE id=%d;", $this->id);

			$stmt1 = $dbh->prepare($query1);
			$stmt1->execute();

			$query2 = sprintf("SELECT * FROM todos WHERE id=%d", $this->id);
			$stmt2 = $dbh->prepare($query2);
			$stmt2->execute();
			$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			$this->setSavedData($result2);

			$dbh->commit();
			
			$result = true;
		} catch (PDOException $e){
			$dbh->rollBack();
			echo $e->getMessage();
			$result = false;
		}

		return $result;
	}



}


?>