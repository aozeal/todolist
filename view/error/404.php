<?php 

session_start();
if (!isset($_SESSION['user_id'])){
	header('Location: ../user/login.php');
	exit;
}
$user_name = $_SESSION['user_name'];

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
	<a href="../todo/index.php">一覧</a>, 
	<a href="../todo/new.php">新規登録</a>,
	<a href="../user/detail.php"><?php echo $user_name ?>さん</a>
	<a href="../user/logout.php">ログアウト</a>
</header>


ページが見つかりません

</body>
</html>



