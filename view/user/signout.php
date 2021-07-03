<?php
require_once('../../config/database.php');

require_once('../../controller/TodoController.php');
require_once('../../model/Todo.php');

require_once('../../controller/UserController.php');
require_once('../../model/User.php');

require_once('../../service/auth/Auth.php');
require_once('../../service/error/ErrorMsgs.php');

$action1 = new TodoController;
$action1->deleteAll();

$action2 = new UserController;
$action2->signout();

Auth::logout();

?>


</!DOCTYPE html>
<html lang-"ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>退会しました</title>
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>
<body>
	<header>
		<a href="./signup.php">新規登録</a>,
	</header>

	<div>
		退会処理完了しました。ご利用ありがとうございました。
	</div>

</body>
</html>


