<?php 
require_once('../../service/error/ErrorMsgs.php');

$error_msgs = ErrorMsgs::getErrorMessages();

?>

</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>404</title>
</head>
<body>
<header>
<a href="../user/signup.php">ユーザー新規登録</a>
<a href="../user/login.php">ログイン</a>
</header>


	<?php if($error_msgs): ?>
		<div>
			<ul>
			<?php foreach($error_msgs as $error_msg):?>
				<li><?php echo $error_msg; ?></li>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>


</body>
</html>



