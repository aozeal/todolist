<?php 

require_once('../../config/database.php');

require_once('../../model/User.php');

require_once('../../service/auth/MailRegister.php');
require_once('../../service/error/ErrorMsgs.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){

	$action = new MailRegister;
	$action->register();
}

$error_msgs = ErrorMsgs::getErrorMessages();

?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>メールアドレス登録</title>

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
		<div>メールアドレス（ユーザーID） : <input type="text" name="user_id"></div>
		<button type="submit">メールアドレス登録</button>
	</form>


</body>
</html>



