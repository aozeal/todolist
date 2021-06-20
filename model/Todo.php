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

	//全レコードを取得（未使用）
	public static function findAll($user_id){
		$query = sprintf("SELECT * FROM todos WHERE user_id='%s' AND deleted_at IS NULL AND done_at IS NULL;", $user_id);
		return self::findByQuery($query);
	}
	

	//デフォルト設定で１ページ取得
	public static function findByDefault($user_id, $page){
		return self::findAllWithCondition($user_id, $page, null, null, null, null);
	}

	public static function countByDefault($user_id){
		return self::countRowWithCondition($user_id, null, null, null);
	}


	//検索条件などをつける
	public static function findAllWithCondition($user_id, $page, $view_done, $view_deadline, $sort_type, $keyword){

		$query_data = [];

		$query = "SELECT * FROM todos WHERE user_id=? AND deleted_at IS NULL ";
		$query_data[] = $user_id;

		if ($keyword){
			$query .= "AND title like ? ";
			$query_data[] = '%' . $keyword . '%';				
		}

		if($view_done === "only_done"){
			$query .= "AND done_at IS NOT NULL ";
		}
		else if($view_done === "with_done"){
			//両方（with_done）query追加なし
		}
		else{
			//($view_done === "without_done" || $view_done === ""){
			$query .= "AND done_at IS NULL ";
		}

		$now = new DateTime('Asia/Tokyo');
		if ($view_deadline === "after_deadline"){
			$query .= "AND ( deadline_at < ? ) ";
			$query_data[] = $now->format('Y-m-d H:i:s');
		}
		else if ($view_deadline === "before_deadline"){
			$query .= "AND ( deadline_at > ? OR deadline_at IS NULL ) ";
			$query_data[] = $now->format('Y-m-d H:i:s');
		}
		else if ($view_deadline === "close_deadline"){
			$query .= "AND ( deadline_at > ? AND deadline_at < ? ) ";
			$query_data[] = $now->format('Y-m-d H:i:s');
			$query_data[] = $now->modify('+1 day')->format('Y-m-d H:i:s');
		}
		else{
			//全て(all)query追加なし
		}

		if ($sort_type === "deadline_asc" || $sort_type === "deadline_desc"){
			$query .= "ORDER BY deadline_at ";
		}
		else{
			$query .= "ORDER BY created_at ";				
		}

		if ($sort_type === "deadline_desc" || $sort_type === "created_desc"){
			$query .= "DESC ";
		}
		else{
			$query .= "ASC ";				
		}

		#整数を疑問符プレースホルダーで扱うことができなかったため直接分に入れる
		$query .= "LIMIT " . TodoController::MAX_ROW_PER_PAGE;
		$query .= " OFFSET " . TodoController::MAX_ROW_PER_PAGE * ($page - 1) . ";";

		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt = $dbh->prepare($query);
			$stmt->execute($query_data);

			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e){
			$dbh->rollBack();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result;
	}
	
	//総件数をカウントする
	public static function countRowWithCondition($user_id, $view_done, $view_deadline, $keyword){

		$query_data = [];

		$query = "SELECT COUNT(*) AS count FROM todos WHERE user_id=? AND deleted_at IS NULL ";
		$query_data[] = $user_id;

		if ($keyword){
			$query .= "AND title like ? ";
			$query_data[] = '%' . $keyword . '%';				
		}

		if($view_done === "only_done"){
			$query .= "AND done_at IS NOT NULL ";
		}
		else if($view_done === "with_done"){
			//両方（with_done）query追加なし
		}
		else{
			//($view_done === "without_done" || $view_done === ""){
			$query .= "AND done_at IS NULL ";
		}

		$now = new DateTime('Asia/Tokyo');
		if ($view_deadline === "after_deadline"){
			$query .= "AND ( deadline_at < ? ) ";
			$query_data[] = $now->format('Y-m-d H:i:s');
		}
		else if ($view_deadline === "before_deadline"){
			$query .= "AND ( deadline_at > ? OR deadline_at IS NULL ) ";
			$query_data[] = $now->format('Y-m-d H:i:s');
		}
		else if ($view_deadline === "close_deadline"){
			$query .= "AND ( deadline_at > ? AND deadline_at < ? ) ";
			$query_data[] = $now->format('Y-m-d H:i:s');
			$query_data[] = $now->modify('+1 day')->format('Y-m-d H:i:s');
		}
		else{
			//全て(all)query追加なし
		}

		$query .= ";";


		try{
			$dbh = new PDO(DSN, USERNAME, PASSWORD);
			$dbh->beginTransaction();

			$stmt = $dbh->prepare($query);
			$stmt->execute($query_data);

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

		} catch(PDOException $e){
			$dbh->rollBack();

			$this->error_msgs[] = $e->getMessage();
			$result = false;
		}

		return $result['count'];
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

		if($stmt){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		else{
			$result = false;
		}

		return $result;
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
			$query = sprintf("UPDATE todos SET deleted_at=NOW(), updated_at=NOW() WHERE id=%d;", $this->id);

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
			$query = sprintf("UPDATE todos SET done_at=NOW(), updated_at=NOW() WHERE id=%d;", $this->id);

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