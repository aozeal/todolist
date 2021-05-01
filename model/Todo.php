<?php


class Todo{
	const TODO_LIST_STATEMENT = 'SELECT * FROM todos INNER JOIN (SELECT todo_id, title, detail FROM todo_histories ORDER BY id DESC LIMIT 1) AS history ON todos.id=history.todo_id';

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
		return self::findByQuery(self::TODO_LIST_STATEMENT);
	}

}


?>