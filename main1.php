<?php
	session_start();
	if(!isset($_SESSION['USERID'])) {
		header("Location: login.php");
		exit();
	}
?>
<!--データベース接続-->
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


<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<title>reminder</title>


	<!--jQuery etc-->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<!--<script src="js/script.js"></script>-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="js/modaal.min.js"></script>


	<!--BootStrap CDN-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<!--stylesheet-->
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="css/modaal.min.css">

	<link href="https://fonts.googleapis.com/css?family=Noto+Serif+JP" rel="stylesheet">

	
</head>
<body>
	
	
	<div class="header">
		<a href="main1.php"><h1>リマインダー</h1></a>
	</div>


	<div class="hello container">
		<h2>ようこそ!<?php echo $_SESSION['USERID'].'さん!';?></h2>
		<a href="logout.php"><h4>ログアウトする</h4></a>
	</div>
	

	<!--入力フォーム-->
		<div class="new">
			<form method="POST" action="main1.php"> 
				<div class="container">
					<div class="form-group row">
						
							<div class="col-6">
								<input type="text" name="content" placeholder="予定を入力してください。" class="form-control">
							</div>
							<div class="col">
								<label><input type="date" name="date" class="form-control"></label>
							
								<label><input type="time" name="time" class="form-control"></label>
							
								<label><input type="submit" name="submit" value="作成" class="btn btn-primary form-control"></label>
							</div>
					</div>
				</div>		
			</form>
		</div>
	
	
	


	
	<?php
		try{
			/*formからデータベースに追加する*/

		$pdo = new PDO($dsn, $user, $password);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


		$content = NULL;
		$date = NULL;
		$time = NULL;



		$type = NULL;
		$id   = NULL;

		if (isset($_POST['type'])) $type = $_POST['type'];
		if (isset($_POST['id']))   $id   = $_POST['id'];

		//削除ボタンが押されたら削除する
		if( $type == 'delete') {
			$sql = "DELETE FROM plan1_tbl WHERE id=$id";
			$stm = $pdo->prepare($sql);
			$stm->execute();
		}
		

		if(isset($_POST["content"])) $content = $_POST["content"];
		if(isset($_POST["date"])) $date = $_POST["date"];
		if(isset($_POST["time"])) $time = $_POST["time"];

		//userの名前を格納
		$userid = $_SESSION['USERID'];

		//作成ボタンが押されたら
		if (isset($_POST["submit"])) {
			if($content == null) {
				//予定が何も入力されずにボタンを押したらアラートを出す。
				echo "<script>alert('予定を入力してください')</script> ";
			}else if($_POST["submit"] == '作成'){ //作成ボタンなら
				$sql_id = "SELECT * FROM user_tbl WHERE userid='$userid'";
				$stm_id = $pdo->prepare($sql_id);
				$stm_id->execute();
				$result_id = $stm_id->fetchAll(PDO::FETCH_ASSOC);
				foreach ($result_id as $key => $value) {			
					$sql_in = "INSERT INTO plan1_tbl (content, date, time, user) VALUES (:content, :date, :time, :id)";
					$stm_in = $pdo->prepare($sql_in);
					$stm_in->bindValue(':content', $content, PDO::PARAM_STR);
					$stm_in->bindValue(':date', $date, PDO::PARAM_STR);
					$stm_in->bindValue(':time', $time, PDO::PARAM_STR);
					$stm_in->bindValue(':id', $value['id'], PDO::PARAM_INT);
					$stm_in->execute();
					//echo $value['id'];
				}
			}else if($_POST["submit"] == '変更') { //変更ボタンなら
				$sql_c = "UPDATE plan1_tbl SET date = :date, time = :time, content = :content WHERE id = $id";
				$stm_c = $pdo->prepare($sql_c);
				$stm_c->bindValue(':date', $date);
				$stm_c->bindValue(':time', $time);
				$stm_c->bindValue(':content', $content);
				$stm_c->execute();
			}
					
		}


	
		//予定をユーザごとに表示
		$sql_id = "SELECT * FROM user_tbl WHERE userid='$userid'";
		$stm_id = $pdo->prepare($sql_id);
		$stm_id->execute();
		$result_id = $stm_id->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result_id as $key => $value) {
			$user = $value['id'];
			$sql = "SELECT * FROM plan1_tbl WHERE user=$user";
			$stm = $pdo->prepare($sql);
			$stm->execute();
			$result = $stm->fetchAll(PDO::FETCH_ASSOC);
		
			foreach ($result as $key1 => $value1) {
				echo '<div class="container">';
				echo '<a href="#modal_'.$value1['id'].'" class="modal-open"><div class="content" id="'.$value1['id'].'">'.'予定日:'.$value1['date'].' 時間'.$value1['time'].'<br>'.$value1['content'].'</a>';
				echo '<form action="main1.php" method="POST">
						
					  	<!--<input type="submit" name="delete" id="delete"  value="削除">-->
					  	<button type="submit" name="delete" value="削除" id="delete" class="btn btn-outline-info">削除</button>

				  		<input type="hidden" id="type" name="type" value="delete">
					  	<input type="hidden" id="id" name="id" value="'.$value1['id'].'">
					  </form>';
				echo "</div>";
				echo "</div>";

				//モーダルコンテンツ
				echo '<div id="modal_'.$value1['id'].'" style="display:none">
						日時:'.$value1['date'].'<br>
						時間:'.$value1['time'].'<br>
						用件:'.$value1['content'].'<br>';


				echo '<div class="change'.$value1['id'].'" style="display:none">';
				echo '<div class="new">
					<form method="POST" action="main1.php" class="form-group">
						<label><input type="text" name="content" placeholder="予定を入力してください。" style="width: 400px" class="form-control"></label>
						<label><input type="date" name="date" class="form-control"></label>
						<label><input type="time" name="time" class="form-control"></label>
						<label><input type="submit" name="submit" value="変更" class="btn btn-outline-primary"></label>
						<input type="hidden" id="id" name="id" value="'.$value1['id'].'">
					</form>
					</div>';
			echo '</div>';
				echo '<button id="change_button" class="button'.$value1['id'].' btn btn-outline-primary" value= "'.$value1['id'].'">変更</button>';
				echo '</div>';
			}
		}

		
		}catch (Exception $e) {
			echo '<span class="error">エラーがありました。</span>';
			echo $e->getMessage();
		}
	?>

<script>
	$(document).on('click', '#delete', function() {
		$('#type').val('delete');
	});

	$(document).on('click', '#detail', function() {
		$('#type').val('detail');
	});

	$(document).on('click', '#change_button', function() {
		var $buttonId = $(this).val();
		console.log($buttonId);
		$('.button' + $buttonId).hide();
		$('.change' + $buttonId).show();
	});

	$('.modal-open').modaal();
	
</script>




	



</body>
</html>
