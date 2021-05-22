<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');
require_once('../../model/TodoHistory.php');

require_once('../../controller/TodoController.php');


session_start();
$error_msgs = $_SESSION['error_msgs'];
unset($_SESSION['error_msgs']);

if (!isset($_SESSION['user_id'])){
	header('Location: ../user/login.php');
	exit;
}
$user_name = $_SESSION['user_name'];


if (isset($_GET['action']) & $_GET['action'] === 'delete'){
	$action = new TodoController;
	$action->delete(); //$todo_listを返してもいいけど、内部でリダイレクトしたらいいのでは？
}

if (isset($_GET['action']) & $_GET['action'] === 'done'){
	$action = new TodoController;
	$action->done();
}

$action = new TodoController;
if (isset($_GET['view']) & $_GET['view'] === 'with_done'){
	$todo_list = $action->indexWithDone();
}
else{
	$todo_list = $action->index();
}

$now = new DateTime();


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
		<a href="./index.php">一覧</a>, 
		<a href="./index.php?view=with_done">一覧（達成済みアリ）</a>, 
		<a href="./new.php">新規登録</a>
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
		<?php if($todo_list):?>
			<?php foreach($todo_list as $todo):?>
				<li>
					<a href="./detail.php?id=<?php echo $todo['id']; ?>">
						<?php echo $todo['id']; ?> : 
						<?php echo $todo['title'];?>
					</a>
					<?php
						$deadline = new DateTime($todo['deadline_at'], new DateTimeZone('Asia/Tokyo'));
						$interval = $deadline->diff($now); ?>
					<?php if (is_null($todo['deadline_at'])): ?>

					<?php elseif ($now > $deadline): ?>
						期限切れ
					<?php elseif ($interval->d < 1): ?>
						期限間近！
					<?php endif; ?>
					<?php if (is_null($todo['done_at'])): ?>
						<button class="done_btn" data-id="<?php echo $todo['id'];?>">
							完了
						</button>
					<?php endif; ?>
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
		const todo_id = $(this).data('id');
		window.location.href = "./index.php?action=delete&todo_id=" + todo_id;
	});

	$(".done_btn").on('click', function(){
		alert($(this).data('id') + 'を達成にします');
		const todo_id = $(this).data('id');
		window.location.href = "./index.php?action=done&todo_id=" + todo_id;
	});
</script>

