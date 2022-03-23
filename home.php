<?php

$date = null;
$file_handle;
$split_data = null;
$message = array();
$message_array = array();
$pdo = null;
$stmt = null;
$res = null;
$option = null;
//var_dump(implode(($artist_name[0])));
define( 'FILENAME', './message.txt');
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

//db接続関数
function db_sql():PDO{
try{
    $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    );
    $pdo = new PDO('mysql:charset=UTF8;dbname=ik;host=localhost', 'ik', 'root',$option);
    }catch(PDOException $e){
    echo "error";

}
 return $pdo;
}

//検索ボタン
if(!empty($_POST['btn_serch'])){
        $pdo = db_sql();
    
        // 書き込み日時を取得
        $current_date = date("Y-m-d H:i:s");
        // トランザクション開始
        $pdo->beginTransaction();
    
        try {
       
        // SQL作成 //本来はここに件数やコメントの有無まで検索させようとしたがlikeが４重になり将来的に重くなりそうでやめた
        $stmt = $pdo->prepare("SELECT * FROM spotif WHERE artist_name LIKE :artist_name AND track_name LIKE :track_name ORDER BY post_date DESC");
    
        // 値をセット
        $stmt->bindValue( ':artist_name', '%'.$_POST['artist_name'].'%', PDO::PARAM_STR);
        $stmt->bindValue( ':track_name', '%'.$_POST['track_name'].'%', PDO::PARAM_STR);
        // SQLクエリの実行
        $stmt->execute();
        $message_array = $stmt->fetchALL();
        $stmt = null;
         
       } catch(Exception $e) {
            echo 1;
            // エラーが発生した時はロールバック
            $pdo->rollBack();
        }

        $pdo = null;
}elseif( $file_handle = fopen( FILENAME,'r')){
    //実行された時
    while( $date = fgets($file_handle) ){
        $split_data = preg_split( '/\'/',$date);
        if(!empty($split_data[1])){
        $message = array(
            'track_name' => $split_data[1],
            'artist_name' => $split_data[3],
            'message' => $split_data[5],
            'post_date' => $split_data[7],
            'url' => $split_data[9]
        );
        array_unshift($message_array, $message);
 
    }}
    $message_array = array_reverse($message_array);
    //print_r($message_array);
    // ファイルを閉じる
    fclose($file_handle);
    
    // データベースに接続
    $pdo = db_sql();
    // 書き込み日時を取得
    $current_date = date("Y-m-d H:i:s");
    foreach($message_array as $value){
    // トランザクション開始
    $pdo->beginTransaction();
    try {    
    // SQL作成
    $stmt = $pdo->prepare("INSERT INTO spotif (artist_name, track_name,post_date ) VALUES ( :artist_name, :track_name,:current_date)");
    // 値をセット
    $stmt->bindParam( ':artist_name', $value['artist_name'], PDO::PARAM_STR);
    $stmt->bindParam( ':track_name', $value['track_name'], PDO::PARAM_STR);
    $stmt->bindParam( ':current_date', $current_date, PDO::PARAM_STR);
   
    // SQLクエリの実行
    $res = $stmt->execute();      
    $res = $pdo->commit();
    }catch(Exception $e) {
        // エラーが発生した時はロールバック
        $pdo->rollBack();
    }    
    $stmt = null;
    
}
file_put_contents(FILENAME,'');
if( !empty($pdo) ) {
	// メッセージのデータを取得する
	$sql = "SELECT artist_name,track_name,message,post_date,id FROM spotif ORDER BY post_date DESC";
	$message_array = $pdo->query($sql);
    $pdo = null;

}
}
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



hr {
    display:block;
    height:1px;
    border:0;
    border-top:1px solid #cccccc;
    margin:1em 0;
    padding:0;
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


a:hover {
    text-decoration: underline;
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

input[type="text"] {
	width: 200px;
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

hr {
	margin: 20px 0;
	padding: 0;
}

/*-----------------------------------
掲示板エリア
-----------------------------------*/

article {
	margin-top: 20px;
	padding: 20px;
	border-radius: 10px;
	background: #fff;
}


	.info {
		margin-bottom: 10px;
	}
	.info h2 {
		display: inline-block;
		margin-right: 10px;
		color: #222;
		line-height: 1.6em;
		font-size: 86%;
	}
	.info time {
		color: #999;
		line-height: 1.6em;
		font-size: 72%;
	}
    article p {
        color: #555;
        font-size: 86%;
        line-height: 1.6em;
    }

@media only screen and (max-width: 1000px) {

    body {
        padding: 30px 5%;
    }

    input[type="text"] {
        width: 100%;
    }
  
}
</style>
</head>
<body>


<h1>music_days</h1>
<form method="get" action="./download.php">
<select name="limit">
        <option value="">全て</option>
        <option value="10">10件</option>
        <option value="30">30件</option>
    </select>
    <input type="submit" name="btn_download" value="ダウンロード">
</form>
<hr>
<form method="post">  
<div>
        <label for="track_name">曲名</label>
        <input id="track_name" type="text" name="track_name" value="<?php if( !empty($_POST['track_name']) ){ echo htmlspecialchars($_POST['track_name'], ENT_QUOTES, 'UTF-8'); } ?>">
</div>
<div>     
        <label for="artist_name">アーティスト名</label>
        <input id="artist_name" type="text" name="artist_name" value="<?php if( !empty($_POST['artist_name']) ){ echo htmlspecialchars($_POST['artist_name'], ENT_QUOTES, 'UTF-8'); } ?>">
</div>

<input type="submit" name="btn_serch" value="検索">
</form>
        <hr>		
<section>
    <?php if( !empty($message_array)):?>
    <?php 
        
        foreach($message_array as $value): ?>
        <article>
            <div class= "info">
                <h2><?php echo $value['track_name']; 
                echo " / ";
                echo $value['artist_name'];?></h2>
                <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?>
                </time>
    
                <p><a href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>  
                <a href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a></p>
                <p><?php echo $value['message'];?>
               
</p>
            </div>
        </article>
        <?php endforeach; ?>
<?php endif; ?>
</section>
</body>
</html>