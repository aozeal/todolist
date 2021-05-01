<?php 

const DSN = 'mysql:host=7a9ebbccd663;dbname=todo;charset=utf8';
const USERNAME = 'todo_user';
const PASSWORD = 'userpw';

const TODO_LIST_STATEMENT = 'SELECT * FROM todos INNER JOIN (SELECT todo_id, title, detail FROM todo_histories ORDER BY id DESC LIMIT 1) AS history ON todos.id=history.todo_id';

$pdo = new PDO(DSN, USERNAME, PASSWORD);
#$stmh = $pdo->query('SELECT * FROM todo.todos');
$stmh = $pdo->query(TODO_LIST_STATEMENT);


$todo_list = $stmh->fetchAll(PDO::FETCH_ASSOC);
#var_dump($todo_list);


?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>TODOリスト</title>

</head>
<body>
	<ul>
		<?php if($todo_list):?>
			<?php foreach($todo_list as $todo):?>
				<li><?php echo $todo['title'];?></li>
			<?php endforeach;?>
		<?php else:?>
			<div>データなし</div>
		<?php endif;?>

	</ul>
</body>
</html>



