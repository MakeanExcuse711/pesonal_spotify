<?php

// データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'ik');
define( 'DB_PASS', 'root');
define( 'DB_NAME', 'ik');

$current_date = null;
$message = array();
$message_data = null;
$error_message = array();
$pdo = null;
$stmt = null;
$res = null;
$option = null;

//var_dump(implode(($artist_name[0])));
define( 'FILENAME', './message.txt');
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// データベースに接続
try {  
    $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
    );
    $pdo = new PDO('mysql:charset=UTF8;dbname='.DB_NAME.';host='.DB_HOST , DB_USER, DB_PASS, $option);

} catch(PDOException $e) {
}

if( !empty($_GET['message_id']&& empty($_POST['message_id'])) ) {
    	// SQL作成
	$stmt = $pdo->prepare("SELECT * FROM spotif WHERE id = :id");
    //idを作ろう
	// 値をセット
	$stmt->bindValue( ':id', $_GET['message_id'], PDO::PARAM_INT);

	// SQLクエリの実行
	$stmt->execute();

	// 表示するデータを取得
	$message_data = $stmt->fetch();

	// 投稿データが取得できないときは管理ページに戻る
	if( empty($message_data) ) {
		header("Location: ./home.php");
		exit;
	}


}elseif(!empty($_POST['message_id'])){
    	// 空白除去
	$message = preg_replace( '/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $_POST['message']);


	if( empty($error_message) ) {

		// トランザクション開始
		$pdo->beginTransaction();

		try {

			// SQL作成
			$stmt = $pdo->prepare("UPDATE spotif SET  message= :message ,post_date = :current_date WHERE id = :id");
            $current_date = date("Y-m-d H:i:s");
			// 値をセット
            $stmt->bindParam( ':current_date', $current_date, PDO::PARAM_STR);
			$stmt->bindParam( ':message', $message, PDO::PARAM_STR);
			$stmt->bindValue( ':id', $_POST['message_id'], PDO::PARAM_INT);

			// SQLクエリの実行
			$stmt->execute();

			// コミット
			$res = $pdo->commit();

		} catch(Exception $e) {

			// エラーが発生した時はロールバック
			$pdo->rollBack();
		}

		// 更新に成功したら一覧に戻る
		if( $res ) {
			header("Location: ./home.php");
			exit;
		}

		
	}

}

$stmt = null;


?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ひと言掲示板</title>
<style>

/*------------------------------

 Reset Style
 
------------------------------*/
html, body, div, span, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
abbr, address, cite, code,
del, dfn, em, img, ins, kbd, q, samp,
small, strong, sub, sup, var,
b, i,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, figcaption, figure,
footer, header, hgroup, menu, nav, section, summary,
time, mark, audio, video {
    margin:0;
    padding:0;
    border:0;
    outline:0;
    font-size:100%;
    vertical-align:baseline;
    background:transparent;
}

body {
    line-height:1;
}

article,aside,details,figcaption,figure,
footer,header,hgroup,menu,nav,section {
    display:block;
}

a {
    margin:0;
    padding:0;
    font-size:100%;
    vertical-align:baseline;
    background:transparent;
}

input, select {
    vertical-align:middle;
}

/*------------------------------

Common Style

------------------------------*/
body {
	padding: 50px;
	font-size: 100%;
	font-family:'ヒラギノ角ゴ Pro W3','Hiragino Kaku Gothic Pro','メイリオ',Meiryo,'ＭＳ Ｐゴシック',sans-serif;
	color: #222;
	background: #f7f7f7;
}

a {
    color: #007edf;
    text-decoration: none;
}


h1 {
	margin-bottom: 30px;
    font-size: 100%;
    color: #222;
    text-align: center;
}


/*-----------------------------------
入力エリア
-----------------------------------*/

label {
    display: block;
    margin-bottom: 7px;
    font-size: 86%;
}

input[type="text"],
textarea {
	margin-bottom: 20px;
	padding: 10px;
	font-size: 86%;
    border: 1px solid #ddd;
    border-radius: 3px;
    background: #fff;
}

textarea {
	width: 50%;
	max-width: 50%;
	height: 70px;
}
input[type="submit"] {
	appearance: none;
    -webkit-appearance: none;
    padding: 10px 20px;
    color: #fff;
    font-size: 86%;
    line-height: 1.0em;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #37a1e5;
}

/*-----------------------------------
掲示板エリア
-----------------------------------*/

@media only screen and (max-width: 1000px) {

    body {
        padding: 30px 5%;
    }
    textarea {
        width: 100%;
        max-width: 100%;
        height: 70px;
    }
}
</style>
</head>
<body>
<h1>曲日記</h1>
<form method="post">
	<div>
		<label for="view_name">曲名</label>
        <p> <?php 
  echo $message_data['track_name']."/".$message_data['artist_name']; ?>  

</div>
	<div>

		<label for="message">メモ</label>
   
		<textarea id="message" name="message"><?php if( !empty($message_data['message']) ){
         echo $message_data['message']; } elseif( !empty($message) ){ echo htmlspecialchars( $message, ENT_QUOTES, 'UTF-8'); }  ?>
</textarea>
       

	</div>
    <a class="btn_cancel" href="home.php">キャンセル</a>
    <input type="submit" name="btn_submit" value="更新">
	<input type="hidden" name="message_id" value="<?php if( !empty($message_data['id']) ){ 
        echo $message_data['id']; }elseif( !empty($_POST['message_id']) ){ echo htmlspecialchars( $_POST['message_id'], ENT_QUOTES, 'UTF-8'); }?>">
</form>
<section>

</section>
</body>
</html>