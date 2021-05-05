<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');

require_once('../../controller/TodoController.php');

require_once('../../validation/TodoValidation.php');

$action = new TodoController;
$todo_detail = $action->edit();

session_start();
$error_msgs = $_SESSION['error_msgs'];
unset($_SESSION['error_msgs']);

if ($error_msgs){
	$todo_detail['title'] = $_GET['title'];
	$todo_detail['detail'] = $_GET['detail'];
	$todo_detail['deadline_at'] = $_GET['deadline_at'];
}

?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>編集</title>

</head>
<body>
	<header>
		<a href="./index.php">一覧</a>, 
		<a href="./index.php?view=with_done">一覧（達成済みアリ）</a>, 
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

	<form action="./edit.php" method="POST">
		<div>ID : <?php echo $todo_detail['id']; ?><input type="hidden" name="id" value="<?php echo $todo_detail['id']; ?>"></div>
		<div>タイトル</div>
		<div><input type="text" name="title" placeholder="タイトルを記入してください" value="<?php echo $todo_detail['title']; ?>"></div>
		<div>詳細</div>
		<div><textarea name="detail" placeholder="詳細を記入してください"><?php echo $todo_detail['detail']; ?></textarea></div>
		<div>期日</div>
		<div><input type="datetime" name="deadline_at" placeholder="20XX-XX-XX XX:XX:XX" value="<?php echo $todo_detail['deadline_at']; ?>"></div>
		<button type="submit">登録</button>
	</form>


</body>
</html>



