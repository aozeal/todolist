<?php


class User{
	public $data;

	public $token;
	public $user_id;
	
	public $error_msgs;

	public function setData($data){
		$this->data = $data;
	}

	public function getErrorMessages(){
		return $this->error_msgs;
	}

	//詳細データを取得
	public static function findById($user_id, $with_deleted=false){
		$dbh = new PDO(DSN, USERNAME, PASSWORD);
		$query = "SELECT * FROM users WHERE id=:id";
		if (!$with_deleted){
			$query .= " AND deleted_at is NULL";
		}
		$query .= ";";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result;
	}


	public function registration(){
		$result = $this->update();
		return $result;
	}


	public function update(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt = $dbh->prepare("UPDATE users SET name=:name, detail=:detail, encrypted_password=:encrypted_password, token=NULL, icon_path=:icon_path, updated_at=NOW() WHERE id=:id;");
			$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR);
			$stmt->bindParam(':detail', $this->data['detail'], PDO::PARAM_STR);
			$stmt->bindParam(':encrypted_password', $this->data['encrypted_password'], PDO::PARAM_STR);
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_STR);
			$stmt->bindParam(':icon_path', $this->data['icon_path'], PDO::PARAM_STR);
			$result = $stmt->execute();

			$dbh->commit();

			$result=true;

		} catch(PDOException $e){
			$dbh->rollBack();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;
	}


	public function createToken(){
		$this->token = uniqid();		
	}

	public function getToken(){
		return $this->token;
	}

	public function setMailAddress($address){
		$this->user_id = $address;
	}

	public function getMailAddress(){
		return $this->user_id;
	}

	public function temporaryRegistration(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt = $dbh->prepare("INSERT INTO users (id, token, created_at, updated_at) VALUES (:id, :token, NOW(), NOW());");
			$stmt->bindParam(':id', $this->user_id, PDO::PARAM_STR);
			$stmt->bindParam(':token', $this->token, PDO::PARAM_STR);
			$result = $stmt->execute();
			$dbh->commit();

			$result = true;

		} catch (PDOException $e){
			$dbh->rollback();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;
	}

	public function getRegistedMail($token){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$query = "SELECT id FROM users WHERE token=:token;";

			$stmt = $dbh->prepare($query);
			$stmt->bindParam(':token', $token);
			$stmt->execute();
			$data = $stmt->fetch(PDO::FETCH_ASSOC);

			$result = $data['id'];

		} catch (PDOException $e){
			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}
		return $result;
	}

	public function temporaryTokenUpdate(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt = $dbh->prepare("UPDATE users SET token=:token, updated_at=NOW() WHERE id=:id;");
			$stmt->bindParam(':id', $this->user_id, PDO::PARAM_STR);
			$stmt->bindParam(':token', $this->token, PDO::PARAM_STR);
			$result = $stmt->execute();
			$dbh->commit();

			$result = true;

		} catch (PDOException $e){
			$dbh->rollback();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;

	}

	public function passwordUpdate(){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();
			$stmt = $dbh->prepare("UPDATE users SET encrypted_password=:encrypted_password, token=NULL, updated_at=NOW() WHERE id=:id");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_STR);
			$stmt->bindParam(':encrypted_password', $this->data['encrypted_password'], PDO::PARAM_STR);
			$stmt->execute();
			$dbh->commit();

			$result = true;

		} catch (PDOException $e){
			$dbh->rollback();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;
	}


	static function signout($id){
		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();
			$stmt = $dbh->prepare("UPDATE users SET updated_at=NOW(), deleted_at=NOW() WHERE id=:id");
			$stmt->bindParam(':id', $id, PDO::PARAM_STR);
			$stmt->execute();
			$dbh->commit();

			$result = true;

		} catch (PDOException $e){
			$dbh->rollback();

			$result = false;
		}

		return $result;
	}
}


?>