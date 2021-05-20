<?php


class User{
	public $id;
	public $name;
	public $detail;
	public $encrypted_password;
	
	public $data;

	public $error_msgs;

	public function setData($data){
		$this->data = $data;
	}

	public function getErrorMessages(){
		return $this->error_msgs;
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

	//詳細データを取得
	public static function findById($user_id){
		$dbh = new PDO(DSN, USERNAME, PASSWORD);
		$stmt = $dbh->prepare("SELECT * FROM users WHERE id=:id;");
		$stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result;
	}


	public function registration(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt = $dbh->prepare("INSERT INTO users (id, name, detail, encrypted_password, created_at, updated_at) VALUES (:id, :name, :detail, :encrypted_password, NOW(), NOW());");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_STR);
			$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR);
			$stmt->bindParam(':detail', $this->data['detail'], PDO::PARAM_STR);
			$stmt->bindParam(':encrypted_password', $this->data['encrypted_password'], PDO::PARAM_STR);
			$stmt->execute();

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

	}


	public function delete(){

	}


}


?>