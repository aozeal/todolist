<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');
require_once('../../model/TodoHistory.php');

require_once('../../controller/TodoController.php');

require_once('../../service/auth/Auth.php');
require_once('../../service/error/ErrorMsgs.php');


$error_msgs = ErrorMsgs::getErrorMessages();

$user_name = Auth::getUserName();
$icon_path = Auth::getIconPath();



if (isset($_GET['action']) && $_GET['action'] === 'delete'){
	$action = new TodoController;
	$action->delete(); //$todo_listを返してもいいけど、内部でリダイレクトしたらいいのでは？
}

if (isset($_GET['action']) && $_GET['action'] === 'done'){
	$action = new TodoController;
	$action->done();
}


$keyword = filter_input(INPUT_GET, 'keyword');
$view_done = filter_input(INPUT_GET, 'view_done');
$view_deadline = filter_input(INPUT_GET, 'view_deadline');
$sort_type = filter_input(INPUT_GET, 'sort_type');
$page = filter_input(INPUT_GET, 'page');
if (!$page){
	$page = 1;
}


$action = new TodoController;
if (isset($_GET['view']) && $_GET['view'] === 'with_condition'){
	$total_row = $action->countRowWithCondition();
	$total_pages = ceil($total_row / TodoController::MAX_ROW_PER_PAGE);
	$todo_list = $action->indexWithCondition();
}
else{
	$total_row = $action->countRowByDefault();
	$total_pages = ceil($total_row / TodoController::MAX_ROW_PER_PAGE);
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
		<a href="./new.php">新規登録</a>,
		<a href="../history/index.php">履歴</a>,
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

	<div>
		<form action="./index.php" method="GET">
			<div>
				達成状況
				<select name="view_done">
					<option value="">選択してください</option>
					<option value="without_done" <?php if($view_done === "without_done"){ echo 'selected';} ?> >
						未完了のみ
					</option>
					<option value="only_done" <?php if($view_done === "only_done"){ echo 'selected';} ?> >完了のみ</option>
					<option value="with_done" <?php if($view_done === "with_done"){ echo 'selected';} ?> >両方</option>
				</select>
			</div>
			<div>
				期限
				<select name="view_deadline">
					<option value="">選択してください</option>
					<option value="all" <?php if($view_deadline === "all"){ echo 'selected';} ?> >すべて</option>
					<option value="before_deadline" <?php if($view_deadline === "before_deadline"){ echo 'selected';} ?> >期限前のみ</option>
					<option value="close_deadline" <?php if($view_deadline === "close_deadline"){ echo 'selected';} ?> >期限間近のみ</option>
					<option value="after_deadline" <?php if($view_deadline === "after_deadline"){ echo 'selected';} ?> >期限切れのみ</option>
				</select>
			</div>
			<div>
				検索キーワード
				<input type="text" name="keyword" value="<?php echo $keyword; ?>">
			</div>
			<div>
				ソート
				<select name="sort_type">
					<option value="">選択してください</option>
					<option value="created_asc" <?php if($sort_type === "created_asc"){ echo 'selected';} ?> >作成順（昇順）</option>
					<option value="created_desc" <?php if($sort_type === "created_desc"){ echo 'selected';} ?> >作成順（降順）</option>
					<option value="deadline_asc" <?php if($sort_type === "deadline_asc"){ echo 'selected';} ?> >期限日順（昇順）</option>
					<option value="deadline_desc" <?php if($sort_type === "deadline_desc"){ echo 'selected';} ?> >期限日順（降順）</option>					
				</select>
			</div>
			<input type="hidden" name="view" value="with_condition">
			<button type="submit">表示条件設定</button>
		</form>
	</div>

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
	<div>
		<?php for($i=1; $i<=$total_pages;$i++):?>
			<?php if($i == $page):?>
				<?php echo $i;?>
			<?php else: ?>
				<a href="./index.php?view=with_condition&page=<?php echo $i;?>&view_deadline=<?php echo $view_deadline;?>&view_done=<?php echo $view_done;?>&sort_type=<?php echo $sort_type;?>&keyword=<?php echo $keyword;?>"><?php echo $i;?></a> 
			<?php endif;?>
		<?php endfor; ?>
	</div>
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

