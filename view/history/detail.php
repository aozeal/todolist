<?php 

require_once('../../config/database.php');

require_once('../../model/TodoHistory.php');
require_once('../../controller/TodoHistoryController.php');
require_once('../../validation/TodoHistoryValidation.php');

require_once('../../service/auth/Auth.php');


session_start();
$error_msgs = $_SESSION['error_msgs'];
unset($_SESSION['error_msgs']);

Auth::checkLoginSession();
$user_name = $_SESSION['user_name'];

$action = new TodoHistoryController;
$todo_detail = $action->detail();
if (!$todo_detail){
	header('Location: ../error/404.php');
	exit();
}

$target_date_str = $action->getTargetDate();
$target_date = new DateTime($target_date_str);


?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>TODO項目詳細（履歴）</title>

</head>
<body>
	<header>
		<a href="../todo/index.php">履歴モード終了</a>, 
		<a href="./index.php?target_date='<?php echo $target_date_str;?>'">履歴一覧</a>, 
		<a href="../user/detail.php"><?php echo $user_name ?>さん</a>
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
	<div>日時（空の場合は現在の時刻）</div>
	<div><?php echo $target_date_str; ?></div>

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

</body>
</html>



