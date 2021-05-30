<?php 

require_once('../../config/database.php');

require_once('../../model/User.php');

require_once('../../controller/UserController.php');
require_once('../../service/auth/Auth.php');

$action = new UserController;
$user_detail = $action->detail();


session_start();
$error_msgs = $_SESSION['error_msgs'];
unset($_SESSION['error_msgs']);
#$user_name = $_SESSION['user_name'];
$user_name = Auth::getUserName();

if($error_msgs){
	$user_detail['name'] = $_GET['name'];
	$user_detail['detail'] = $_GET['detail'];
	$user_detail['icon_path'] = $_GET['icon_path'];
}


?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ユーザー情報詳細</title>

</head>
<body>
	<header>
		<a href="../todo/index.php">一覧</a>, 
		<a href="../todo/index.php?view=with_done">一覧（達成済みアリ）</a>, 
		<a href="../todo/new.php">新規登録</a>
		<a href="./detail.php"><?php echo $user_name ?>さん</a>
		<a href="./logout.php">ログアウト</a>
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
				<th>ユーザーID</th>
				<th>ユーザー名</th>
				<th>詳細</th>
			</tr>			
		</thead>
		<tbody>
			<tr>
				<td scope="row">
					<?php echo $user_detail['id']; ?>
				</td>
				<td>
					<?php echo $user_detail['name']; ?>
				</td>
				<td>
					<?php echo $user_detail['detail']; ?>
				</td>
			</tr>
		</tbody>
	</table>

	<div>
		<button><a href="./edit.php">編集</a></button>
	</div>

</body>
</html>



