<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');
require_once('../../model/TodoHistory.php');

require_once('../../controller/TodoController.php');

require_once('../../validation/TodoValidation.php');

$title='';
$detail='';
$deadline_at = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$action = new TodoController;
	$action->new();
}

session_start();
$error_msgs = $_SESSION['error_msgs'];
//セッション削除
unset($_SESSION['error_msgs']);

if($_SERVER['REQUEST_METHOD'] === 'GET'){
	$title = $_GET['title'];
	$detail = $_GET['detail'];
	$deadline_at = $_GET['deadline_at'];
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
<header>
		<a href="./index.php">一覧</a>, 
		<a href="./index.php?view=with_done">一覧（達成済みアリ）</a>, 
		<a href="./new.php">新規登録</a>
		<a href="../user/edit.php">ユーザー情報編集</a>
		<a href="../user/logout.php">ログアウト</a>		
	</header>

	<?php if($error_msgs): ?>
		<div>
			<ul>
			<?php foreach($error_msgs as $error_msg):?>
				<li><?php echo $error_msg; ?></li>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<form action="./new.php" method="POST">
		<div>タイトル</div>
		<div><input type="text" name="title" placeholder="タイトルを記入してください" value="<?php echo $title ?>"></div>
		<div>詳細</div>
		<div><textarea name="detail" placeholder="詳細を記入してください"><?php echo $detail ?></textarea></div>
		<div>期日</div>
		<div><input type="datetime" name="deadline_at" placeholder="20XX-XX-XX XX:XX:XX" value="<?php echo $deadline_at ?>"></div>
		<button type="submit">登録</button>
	</form>


</body>
</html>



