<?php

	//エラー表示
	ini_set( 'display_errors', 1 );
	ini_set( 'error_reporting', E_ALL );
	
	//データベースユーザ
	$user = 'testuser';
	$password = 'pw4testuser';

	$dbName = 'reminder';
	$host = 'localhost:8889';
	$dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";

	
?>

<?php
	session_start();

	if(isset($_POST["login"])) {
		//ユーザIDチェック
		if(empty($_POST["id"])) {
			//echo 'ユーザIDが未入力です。';
			echo "<script>alert('ユーザIDが未入力です。')</script> ";
		}else if(empty($_POST["password"])) {
			//echo 'パスワードが未入力です。';
			echo "<script>alert(' パスワードが未入力です。')</script> ";
		}
	}

	if(!empty($_POST['id']) && !empty($_POST['password'])) {
		$userid = $_POST['id'];
		
		$pdo = new PDO($dsn, $user, $password);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try {
			$sql = "SELECT * FROM user_tbl WHERE userid = ?";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(1, $userid, PDO::PARAM_STR);
			$stmt->execute();
			$password = $_POST['password'];
			

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			//echo $result['password'];
			if(password_verify($password, $result['password'])) {
				$_SESSION['USERID'] = $userid;
				header('Location: main1.php');
				exit();
			}else {
				
				echo "<script>alert('ユーザIDあるいはパスワードに謝りがあります。')</script> ";
			}
		}catch(Exception $e) {
			echo $e->getMessage();
		}
	}
?>

<!DOCTYPE html>
<html lang="ja">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<head>
	<title>ログインページ</title>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<!--jQuery etc-->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<link rel="stylesheet" type="text/css" href="css/login_style.css">
</head>
<body>
	<div class="container">
		<h1 class="header">ログインページ</h1>
		<hr>
		<form method="POST" action="login.php">
			<div class="form-group">
				<label>ID<input type="text" name="id" class="form-control"></label>
				<label>パスワード<input type="password" name="password" class="form-control"></label>
				<br>
			<button type="submit" id="login" name="login" class="btn btn-primary">ログイン</button>
		</div>
		</form>
		<a href="newAccount.php"><h2>初めてご利用の方</h2></a>
	</div>
</body>
</html>