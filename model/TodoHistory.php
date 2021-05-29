<?php


class TodoHistory{
	public $error_msg;

	public function getErrorMessage(){
		return $this->error_msg;
	}
	public function getErrorMessages(){
		return [$error_msg];
	}

	public function save($todo_id, $dbh){
		try{
			$query1 = sprintf("SELECT * FROM todos WHERE id=%d" , $todo_id);
			$stmt1 = $dbh->prepare($query1);
			$stmt1->execute();
			$saved_data = $stmt1->fetch(PDO::FETCH_ASSOC);

			$stmt2 = $dbh->prepare("INSERT INTO todo_histories (todo_id, user_id, title, detail, deadline_at, done_at, created_at, updated_at, deleted_at) 
				VALUES (:todo_id, :user_id, :title, :detail, :deadline_at, :done_at, :created_at, :updated_at, :deleted_at);");
			$stmt2->bindParam(':todo_id', $saved_data['id'], PDO::PARAM_INT);
			$stmt2->bindParam(':user_id', $saved_data['user_id'], PDO::PARAM_STR);
			$stmt2->bindParam(':title', $saved_data['title'], PDO::PARAM_STR);
			$stmt2->bindParam(':detail', $saved_data['detail'], PDO::PARAM_STR);
			$stmt2->bindParam(':deadline_at', $saved_data['deadline_at'], PDO::PARAM_STR);
			$stmt2->bindParam(':done_at', $saved_data['done_at'], PDO::PARAM_STR);
			$stmt2->bindParam(':created_at', $saved_data['created_at'], PDO::PARAM_STR);
			$stmt2->bindParam(':updated_at', $saved_data['updated_at'], PDO::PARAM_STR);
			$stmt2->bindParam(':deleted_at', $saved_data['deleted_at'], PDO::PARAM_STR);
			$stmt2->execute();

			$result = true;
		} catch(PDOException $e){
			$this->error_msg = $e->getMessage();
			$result = false;
		}

		return $result;
	}

	public static function findAll($user_id, $target_date){
		$dbh = new PDO(DSN, USERNAME, PASSWORD);
		$stmt = $dbh->prepare("select * from todo_histories as h1 where  h1.updated_at = (
			select MAX(updated_at) from todo_histories as h2
			where h1.todo_id = h2.todo_id
			and h2.updated_at < :updated_at
			and user_id = :user_id
		);");
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
		$stmt->bindParam(':updated_at', $target_date);
		$stmt->execute();
		
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public static function findById($history_id, $user_id){
		$dbh = new PDO(DSN, USERNAME, PASSWORD);
		$stmt = $dbh->prepare("SELECT * FROM todo_histories WHERE id=:id AND user_id=:user_id;");
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
		$stmt->bindParam(':id', $history_id, PDO::PARAM_INT);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result;
	}

}


?>