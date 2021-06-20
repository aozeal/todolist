<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');
require_once('../../model/TodoHistory.php');

require_once('../../controller/TodoController.php');

require_once('../../service/auth/Auth.php');
require_once('../../service/error/ErrorMsgs.php');


$action = new TodoController;
$todo_detail = $action->detail();
if (!$todo_detail){
	header('Location: ../error/404.php');
	exit();
}

$error_msgs = ErrorMsgs::getErrorMessages();

$user_name = Auth::getUserName();;
$icon_path = Auth::getIconPath();

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
		<a href="./new.php">新規登録</a>,
		<a href="../user/detail.php">
			<?php if ($icon_path): ?>
				<img style="height:30px;" src="<?php echo $icon_path; ?>">
			<?php endif; ?>
			<?php echo $user_name ?>さん
		</a>
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

	<table>
		<thead>
			<tr>
				<th>タイトル</th>
				<th>詳細</th>
				<th>期日</th>
				<th>完了日</th>
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
				<td>
					<?php echo $todo_detail['deadline_at']; ?>
				</td>
				<td>
					<?php echo $todo_detail['done_at']; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<div>
		<?php
			$now = new DateTime('Asia/Tokyo');
			$deadline = new DateTime($todo_detail['deadline_at'], new DateTimeZone('Asia/Tokyo'));
			$interval = $deadline->diff($now); ?>
		<?php if (!is_null($todo_detail['done_at'])): ?>
			達成済み
		<?php elseif (is_null($todo_detail['deadline_at'])): ?>

		<?php elseif ($now > $deadline): ?>
			期限切れ
		<?php elseif ($interval->d < 1): ?>
			期限間近！
		<?php endif; ?>
	</div>
	<div>
		<button><a href="./edit.php?id=<?php echo $todo_detail['id']; ?>">編集</a></button>
	</div>

</body>
</html>



