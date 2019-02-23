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

	if(isset($_POST["make"])) {
		//ユーザIDチェック
		if(empty($_POST["userid"])) {
			//echo 'ユーザIDが未入力です。';
			echo "<script>alert('ユーザIDが未入力です。')</script> ";
		}else if(empty($_POST["password"])) {
			//echo 'パスワードが未入力です。';
			echo "<script>alert(' パスワードが未入力です。')</script> ";
		}else if(empty($_POST["name"])) {
			echo "<script>alert(' 氏名が未入力です。')</script> ";

		}
	}

	if(!empty($_POST['userid']) && !empty($_POST["password"]) && !empty($_POST["name"])) {
		$pdo = new PDO($dsn, $user, $password);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$userid = $_POST['userid'];
		$password = $_POST['password'];
		$name = $_POST['name'];

		

		try {
			
			$sqlid = "SELECT COUNT(*) FROM user_tbl WHERE userid = '$userid'";
			$ss = $pdo->query($sqlid);
			$count = $ss->fetchColumn();

			if($count > 0) {
				throw new Exception("そのユーザIDはすでに使用されています。", 1);
			}

			$stmt = $pdo->prepare('INSERT INTO user_tbl (userid, name, password) VALUES (:userid, :name, :password)');
			$pass = password_hash($password, PASSWORD_DEFAULT);
			$stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':password', $pass, PDO::PARAM_STR);
			$stmt->execute();
			
			$_SESSION['USERID'] = $name;
			echo '<script>alert("登録が完了しました。");location.href="main1.php";</script>';
		} catch(Exception $e){
			//$error = $e->getMessage();
			echo "<script>alert(' そのユーザIDはすでに使用されています。')</script> ";
			//echo $error;
		}
	}
	
?>

<!DOCTYPE html>
<html lang="ja">
<meta charset="utf-8">
<meta name="viewport" width="device-width">
<head>
	<title>アカウント作成</title>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<link rel="stylesheet" type="text/css" href="css/newAccount_style.css">


</head>
<body>
	<div class="container">
		<h1 class="header">アカウント作成フォーム</h1>
		<hr>
		<form method="POST">
			<div class="form-group">
			<label>ユーザID<input type="text" name="userid" class="form-control"></label><br>
			<label>パスワード<input type="password" name="password" class="form-control"></label><br>
			<label>氏名<input type="text" name="name" class="form-control"></label>
			<br>
			<button type="submit" name="make" class="btn btn-primary">作成</button> 
		</div>
		</form>

		<a href="login.php"><h2>ログインページへ</h2></a>
	</div>

</body>
</html>