<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');

require_once('../../controller/TodoController.php');

$action = new TodoController;
$todo_detail = $action->detail();

?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>TODO項目詳細</title>

</head>
<body>
	<header>
		<a href="./index.php">一覧</a>, 
		<a href="./new.php">新規登録</a>
	</header>
	<table>
		<thead>
			<tr>
				<th>タイトル</th>
				<th>詳細</th>
			</tr>			
		</thead>
		<tbody>
			<tr>
				<td scope="row">
					<?php echo $todo_detail['title']; ?>
				</td>
				<td>
					<?php echo $todo_detail['detail']; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<div>
		<button><a href="./edit.php?id=<?php echo $todo_detail['id']; ?>">編集</a></button>
	</div>

</body>
</html>



