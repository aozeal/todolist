<?php 

require_once('../../config/database.php');

require_once('../../model/Todo.php');
require_once('../../model/TodoHistory.php');

require_once('../../controller/TodoController.php');

require_once('../../service/auth/Auth.php');
require_once('../../service/error/ErrorMsgs.php');


$action = new TodoController;

if (isset($_GET['action']) && $_GET['action'] === 'delete'){
	$action->delete();
}

if (isset($_GET['action']) && $_GET['action'] === 'done'){
	$action->done();
}

$data = $action->index();


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
			<?php if ($data['user']['icon_path']): ?>
				<img style="height:30px;" src="<?php echo $data['user']['icon_path']; ?>">
			<?php endif; ?>
			<?php echo $data['user']['name']; ?>さん
		</a>
		<a href="../user/logout.php">ログアウト</a>
	</header>
	<?php if($data['error_msgs']): ?>
		<div>
			<ul>
			<?php foreach($data['error_msgs'] as $error_msg):?>
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
					<option value="<?php echo Todo::VIEW_DONE_WITHOUT_DONE;?>" <?php if($data['view_done'] === Todo::VIEW_DONE_WITHOUT_DONE){ echo 'selected';} ?> >
						未完了のみ
					</option>
					<option value="<?php echo Todo::VIEW_DONE_ONLY_DONE;?>" <?php if($data['view_done'] === Todo::VIEW_DONE_ONLY_DONE){ echo 'selected';} ?> >完了のみ</option>
					<option value="<?php echo Todo::VIEW_DONE_WITH_DONE;?>" <?php if($data['view_done'] === Todo::VIEW_DONE_WITH_DONE){ echo 'selected';} ?> >両方</option>
				</select>
			</div>
			<div>
				期限
				<select name="view_deadline">
					<option value="">選択してください</option>
					<option value="<?php echo Todo::VIEW_DEADLINE_ALL;?>" <?php if($data['view_deadline'] === Todo::VIEW_DEADLINE_ALL){ echo 'selected';} ?> >すべて</option>
					<option value="<?php echo Todo::VIEW_DEADLINE_BEFORE_DEADLINE;?>" <?php if($data['view_deadline'] === Todo::VIEW_DEADLINE_BEFORE_DEADLINE){ echo 'selected';} ?> >期限前のみ</option>
					<option value="<?php echo Todo::VIEW_DEADLINE_CLOSE_DEADLINE ?>" <?php if($data['view_deadline'] === Todo::VIEW_DEADLINE_CLOSE_DEADLINE){ echo 'selected';} ?> >期限間近のみ</option>
					<option value="<?php echo Todo::VIEW_DEADLINE_AFTER_DEADLINE;?>" <?php if($data['view_deadline'] === Todo::VIEW_DEADLINE_AFTER_DEADLINE){ echo 'selected';} ?> >期限切れのみ</option>
				</select>
			</div>
			<div>
				検索キーワード
				<input type="text" name="keyword" value="<?php echo $data['keyword']; ?>">
			</div>
			<div>
				ソート
				<select name="sort_type">
					<option value="">選択してください</option>
					<option value="<?php echo Todo::SORT_TYPE_CREATED_ASC;?>" <?php if($data['sort_type'] === Todo::SORT_TYPE_CREATED_ASC){ echo 'selected';} ?> >作成順（昇順）</option>
					<option value="<?php echo Todo::SORT_TYPE_CREATED_DESC;?>" <?php if($data['sort_type'] === Todo::SORT_TYPE_CREATED_DESC){ echo 'selected';} ?> >作成順（降順）</option>
					<option value="<?php echo Todo::SORT_TYPE_DEADLINE_ASC;?>" <?php if($data['sort_type'] === Todo::SORT_TYPE_DEADLINE_ASC){ echo 'selected';} ?> >期限日順（昇順）</option>
					<option value="<?php echo Todo::SORT_TYPE_DEADLINE_DESC;?>" <?php if($data['sort_type'] === Todo::SORT_TYPE_DEADLINE_DESC){ echo 'selected';} ?> >期限日順（降順）</option>					
				</select>
			</div>
			<button type="submit">表示条件設定</button>
		</form>
	</div>

	<ul>
		<?php if($data['todo_list']):?>
			<?php foreach($data['todo_list'] as $todo):?>
				<li>
					<a href="./detail.php?id=<?php echo $todo['id']; ?>">
						<?php echo $todo['id']; ?> : 
						<?php echo $todo['title'];?>
					</a>
					<?php
						$deadline = new DateTime($todo['deadline_at'], new DateTimeZone('Asia/Tokyo'));
						$interval = $deadline->diff($data['now']); ?>
					<?php if (is_null($todo['deadline_at'])): ?>

					<?php elseif ($data['now'] > $deadline): ?>
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
		<?php for($i=1; $i<=$data['total_pages'];$i++):?>
			<?php if($i == $data['page']):?>
				<?php echo $i;?>
			<?php else: ?>
				<a href="./index.php?view=with_condition&page=<?php echo $i;?>&view_deadline=<?php echo $data['view_deadline'];?>&view_done=<?php echo $data['view_done'];?>&sort_type=<?php echo $data['sort_type'];?>&keyword=<?php echo $data['keyword'];?>"><?php echo $i;?></a> 
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

