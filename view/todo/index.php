<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');

require_once('../../controller/TodoController.php');

$action = new TodoController;
$todo_list = $action->index();

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
	<header>
		<a href="./new.php">新規登録</a>
	</header>
	<ul>
		<?php if($todo_list):?>
			<?php foreach($todo_list as $todo):?>
				<li>
					<a href="./detail.php?todo_id=<?php echo $todo['id']; ?>">
						<?php echo $todo['title'];?>
					</a>
				</li>
			<?php endforeach;?>
		<?php else:?>
			<div>データなし</div>
		<?php endif;?>

	</ul>
</body>
</html>


