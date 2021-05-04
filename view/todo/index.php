<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');

require_once('../../controller/TodoController.php');

if (isset($_GET['action']) & $_GET['action'] === 'delete'){
	$action = new TodoController;
	$action->delete(); //$todo_listを返してもいいけど、内部でリダイレクトしたらいいのでは？
}

$action = new TodoController;
$todo_list = $action->index();


session_start();
$error_msgs = $_SESSION['error_msgs'];
unset($_SESSION['error_msgs']);


?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>TODOリスト</title>
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

</head>
<body>
	<header>
		<a href="./new.php">新規登録</a>
	</header>
	<?php if($error_msgs): ?>
		<div>
			<ul>
			<?php foreach($error_msgs as $error_msg):?>
				<li><?php echo $error_msg; ?></li>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<ul>
		<?php if($todo_list):?>
			<?php foreach($todo_list as $todo):?>
				<li>
					<a href="./detail.php?id=<?php echo $todo['id']; ?>">
						<?php echo $todo['id']; ?> : 
						<?php echo $todo['title'];?>
					</a>
					<button class="delete_btn" data-id="<?php echo $todo['id'];?>">
						削除
					</button>
				</li>
			<?php endforeach;?>
		<?php else:?>
			<div>データなし</div>
		<?php endif;?>

	</ul>
</body>
</html>

<script>
	$(".delete_btn").on('click', function(){
		alert($(this).data('id') + 'を削除します');
		const todo_id = $(this).data('id')
		window.location.href = "./index.php?action=delete&todo_id=" + todo_id;
	});
</script>

