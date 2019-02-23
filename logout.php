<?php
session_start();
// セッションクリア
session_destroy();
$error = "ログアウトしました。";
echo $error;
?>

<!DOCTYPE html>
<html lang="ja">
<meta charset="utf-8">
<meta name="viewport" width="device-width">
<head>
	<title>ログアウトページ</title>
</head>
<body>
	<a href="login.php"><h2>ログインページへ</h2></a>
</body>
</html>