<?php


class TodoHistory{
	public $error_msg;

	public function getErrorMessage(){
		return $this->error_msg;
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

}


?>