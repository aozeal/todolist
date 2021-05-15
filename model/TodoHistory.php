<?php


class TodoHistory{
	public $data;
	public $error_msg;

	public function setSavedData($data){
		$this->data = $data;
	}

	public function save(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt = $dbh->prepare("INSERT INTO todo_histories (todo_id, user_id, title, detail, deadline_at, done_at, created_at, updated_at, deleted_at) 
				VALUES (:todo_id, :user_id, :title, :detail, :deadline_at, :done_at, :created_at, :updated_at, :deleted_at);");
			$stmt->bindParam(':todo_id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR);
			$stmt->bindParam(':title', $this->data['title'], PDO::PARAM_STR);
			$stmt->bindParam(':detail', $this->data['detail'], PDO::PARAM_STR);
			$stmt->bindParam(':deadline_at', $this->data['deadline_at'], PDO::PARAM_STR);
			$stmt->bindParam(':done_at', $this->data['done_at'], PDO::PARAM_STR);
			$stmt->bindParam(':created_at', $this->data['created_at'], PDO::PARAM_STR);
			$stmt->bindParam(':updated_at', $this->data['updated_at'], PDO::PARAM_STR);
			$stmt->bindParam(':deleted_at', $this->data['deleted_at'], PDO::PARAM_STR);
			$stmt->execute();

			$dbh->commit();
			
			$result = true;
		} catch(PDOException $e){
			$dbh->rollBack();

			echo $e->getMessage();
			$result = false;
		}

		return $result;
	}


	public function save_h($dbh){
		try{
			#$dbh = new PDO(DSN, USERNAME, PASSWORD);
			#$dbh->beginTransaction();

			$stmt = $dbh->prepare("INSERT INTO todo_histories (todo_id, user_id, title, detail, deadline_at, done_at, created_at, updated_at, deleted_at) 
				VALUES (:todo_id, :user_id, :title, :detail, :deadline_at, :done_at, :created_at, :updated_at, :deleted_at);");
			$stmt->bindParam(':todo_id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR);
			$stmt->bindParam(':title', $this->data['title'], PDO::PARAM_STR);
			$stmt->bindParam(':detail', $this->data['detail'], PDO::PARAM_STR);
			$stmt->bindParam(':deadline_at', $this->data['deadline_at'], PDO::PARAM_STR);
			$stmt->bindParam(':done_at', $this->data['done_at'], PDO::PARAM_STR);
			$stmt->bindParam(':created_at', $this->data['created_at'], PDO::PARAM_STR);
			$stmt->bindParam(':updated_at', $this->data['updated_at'], PDO::PARAM_STR);
			$stmt->bindParam(':deleted_at', $this->data['deleted_at'], PDO::PARAM_STR);
			$stmt->execute();

			#$dbh->commit();

			throw new PDOException("データの登録に失敗しました。");
			
			$result = true;
		} catch(PDOException $e){
			#$dbh->rollBack();

			#echo $e->getMessage();
			$this->error_msg = $e->getMessage();
			$result = false;
		}

		return $result;
	}
	
	public function getErrorMessage(){
		return $this->error_msg;
	}


}


?>