<?php 

require_once('../../config/database.php');

require_once('../../controller/UserController.php');
require_once('../../validation/UserValidation.php');

require_once('../../model/User.php');
require_once('../../service/auth/Auth.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){

	$action = new UserController;
	$action->signup();
}

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
	<title>ユーザー登録</title>

</head>
<body>
	<header>
		<a href="./login.php">ログイン</a>
	</header>

	<?php if($error_msgs): ?>
		<div>
			<ul>
			<?php foreach($error_msgs as $error_msg):?>
				<li><?php echo $error_msg; ?></li>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<form action="./signup.php" method="POST">
		<div>ユーザーID : <input type="text" name="user_id"></div>
		<div>パスワード：<input type="password" name="password1"></div>		
		<div>パスワード（確認用）：<input type="password" name="password2"></div>
		<div>ユーザー名 : <input type="text" name="name"></div>
		<div>詳細 : <textarea name="detail"></textarea></div>
		<button type="submit">ユーザー登録</button>
	</form>


</body>
</html>



