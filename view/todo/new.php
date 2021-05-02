<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');

require_once('../../controller/TodoController.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$action = new TodoController;
	$action->new();
}


?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>新規登録</title>

</head>
<body>
	<form action="./new.php" method="POST">
		<div>タイトル</div>
		<div><input type="text" name="title"></div>
		<div>詳細</div>
		<div><textarea name="detail" placeholder="詳細を記入してください"></textarea></div>
		<div>期日</div>
		<div><input type="datetime" name="deadline_at" placeholder="20XX-XX-XX XX:XX:XX"></div>
		<button type="submit">登録</button>
	</form>


</body>
</html>



