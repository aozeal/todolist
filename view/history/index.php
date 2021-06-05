<?php 

require_once('../../config/database.php');

require_once('../../model/TodoHistory.php');
require_once('../../controller/TodoHistoryController.php');
require_once('../../validation/TodoHistoryValidation.php');

require_once('../../service/auth/Auth.php');
require_once('../../service/error/ErrorMsgs.php');

$error_msgs = ErrorMsgs::getErrorMessages();

$user_name = Auth::getUserName();;

$action = new TodoHistoryController;
$todo_list = $action->index();

$target_date_str = $action->getTargetDate();
$target_date = new DateTime($target_date_str, new DateTimeZone('Asia/Tokyo'));

?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>TODOリスト履歴</title>
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

</head>
<body>
	<header>
		<a href="../todo/index.php">履歴モード終了</a>, 
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

	<ul>
	<div>日時（空の場合は現在の時刻）</div>
		<form action="./index.php" method="GET">
		<div><input type="datetime" name="target_date" placeholder="20XX-XX-XX XX:XX:XX" value="<?php echo $target_date_str; ?>"></div>
		<button type="submit">Go!</button>
		</form>

		<div><?php echo $target_date_str; ?></div>

		<?php if($todo_list):?>
			<?php foreach($todo_list as $todo):?>
				<?php if ($todo['deleted_at']){continue;}?>
				<?php if ($todo['done_at']){continue;}?>
				<li>
					<a href="./detail.php?id=
						<?php echo $todo['id'] . "&target_date=" . $target_date_str; ?>
						">
						<?php echo $todo['id']; ?> : 
						<?php echo $todo['title'];?>
					</a>
					<?php
						$deadline = new DateTime($todo['deadline_at'], new DateTimeZone('Asia/Tokyo'));
						$interval = $deadline->diff($target_date); ?>
					<?php if (is_null($todo['deadline_at'])): ?>

					<?php elseif ($target_date > $deadline): ?>
						期限切れ
					<?php elseif ($interval->d < 1): ?>
						期限間近！
					<?php endif; ?>
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
		const todo_id = $(this).data('id');
		window.location.href = "./index.php?action=delete&todo_id=" + todo_id;
	});

	$(".done_btn").on('click', function(){
		alert($(this).data('id') + 'を達成にします');
		const todo_id = $(this).data('id');
		window.location.href = "./index.php?action=done&todo_id=" + todo_id;
	});
</script>

