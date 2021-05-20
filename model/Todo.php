<?php


class Todo{
	public $id;
	public $title;
	public $detail;
	public $deadline_at;
	
	public $savedData;

	public $error_msgs;

	public $user_id;

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
		$this->deadline_at = $deadline_at;
		//$this->deadline_at = null;
	}

	public function getSavedData(){
		return $this->savedData;
	}

	public function setSavedData($data){
		$this->savedData = $data;
	}

	public function getErrorMessages(){
		return $this->error_msgs;
	}

	public function setUserId($user_id){
		$this->user_id = $user_id;
	}

	public function getUserId(){
		return $this->user_id;
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
	public static function findAll($user_id){
		$query = sprintf("SELECT * FROM todos WHERE user_id='%s' AND deleted_at IS NULL AND done_at IS NULL;", $user_id);
		return self::findByQuery($query);
	}

	//達成済も含めて全レコードを取得
	public static function findAllWithDone($user_id){
		$query = sprintf("SELECT * FROM todos WHERE user_id='%s' AND deleted_at IS NULL;", $user_id);
		return self::findByQuery($query);
	}
	

	//詳細データを取得
	public static function findById($todo_id, $user_id){
		$query = sprintf("SELECT * FROM todos WHERE id=%d AND user_id='%s';", $todo_id, $user_id);

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
	public static function isExistById($todo_id, $user_id){
		$query = sprintf("SELECT * FROM todos WHERE id=%d AND user_id='%s';", $todo_id, $user_id);

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

			$stmt = $dbh->prepare("INSERT INTO todos (user_id, title, detail, deadline_at, created_at, updated_at) VALUES (:user_id, :title, :detail, :deadline_at, NOW(), NOW());");
			$stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
			$stmt->bindParam(':detail', $this->detail, PDO::PARAM_STR);
			$stmt->bindParam(':deadline_at', $this->deadline_at);
			$stmt->bindParam(':user_id', $this->user_id);
			$stmt->execute();

			$todo_id = $dbh->lastInsertId();
			$history = new TodoHistory();
			$result = $history->save($todo_id, $dbh);

			if (!$result){
				throw new PDOException($history->getErrorMessage());
			}

			$dbh->commit();
			
			$result = true;
		} catch(PDOException $e){
			$dbh->rollBack();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;
	}


	public function update(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt = $dbh->prepare("UPDATE todos SET title=:title, detail=:detail, deadline_at=:deadline_at, updated_at=NOW() WHERE id=:id AND user_id=:user_id;");
			$stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
			$stmt->bindParam(':detail', $this->detail, PDO::PARAM_STR);
			$stmt->bindParam(':deadline_at', $this->deadline_at);
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_STR);
			$stmt->execute();

			$history = new TodoHistory();
			$result = $history->save($this->id, $dbh);

			if (!$result){
				throw new PDOException($history->getErrorMessage());
			}

			$dbh->commit();
			
			$result = true;
		} catch(PDOException $e){
			$dbh->rollBack();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;
	}


	public function delete(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);

			$dbh->beginTransaction();
			$query = sprintf("UPDATE todos SET deleted_at=NOW() WHERE id=%d;", $this->id);

			$stmt = $dbh->prepare($query);
			$stmt->execute();

			$history = new TodoHistory();
			$result = $history->save($this->id, $dbh);

			if (!$result){
				throw new PDOException($history->getErrorMessage());
			}

			$dbh->commit();
			
			$result = true;
		} catch (PDOException $e){
			$dbh->rollBack();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;
	}

	public function done(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);

			$dbh->beginTransaction();
			$query = sprintf("UPDATE todos SET done_at=NOW() WHERE id=%d;", $this->id);

			$stmt = $dbh->prepare($query);
			$stmt->execute();

			$history = new TodoHistory();
			$result = $history->save($this->id, $dbh);

			if (!$result){
				throw new PDOException($history->getErrorMessage());
			}

			$dbh->commit();
			
			$result = true;
		} catch (PDOException $e){
			$dbh->rollBack();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;
	}



}


?>