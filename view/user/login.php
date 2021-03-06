<?php 

require_once('../../config/database.php');

require_once('../../controller/UserController.php');
require_once('../../validation/UserValidation.php');

require_once('../../model/User.php');
require_once('../../service/auth/Auth.php');
require_once('../../service/error/ErrorMsgs.php');


if($_SERVER['REQUEST_METHOD'] === 'POST'){

	$action = new UserController;
	$action->login();
}

$error_msgs = ErrorMsgs::getErrorMessages();

?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ログイン</title>

</head>
<body>
	<header>
		<a href="./signup.php">ユーザー登録</a>
	</header>

	<?php if($error_msgs): ?>
		<div>
			<ul>
			<?php foreach($error_msgs as $error_msg):?>
				<li><?php echo $error_msg; ?></li>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<form action="./login.php" method="POST">
		<div>ユーザーID : <input type="text" name="user_id"></div>
		<div>パスワード：<input type="password" name="password"></div>
		<button type="submit">ログイン</button>
	</form>
	<div>
		<a href="./forget_pw.php">パスワードを忘れた場合</a>
	</div>


</body>
</html>



