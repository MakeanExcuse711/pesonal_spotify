<?php
    require 'vendor/autoload.php';
    $session = new SpotifyWebAPI\Session(
        '"your_id"',
        '"secret_id"',
        'http://localhost/spotify/index.php'
    ); 
    $api = new SpotifyWebAPI\SpotifyWebAPI();

    if (isset($_GET['code'])) {
        $session->requestAccessToken($_GET['code']);
        $api->setAccessToken($session->getAccessToken());
        $top = $api->getMyRecentTracks('tracks', ['limit' => 10]);
    
        echo '<pre>';
        //print_r($top);
        $items = $top->items;
        $album = $top->items[0]->track->album;
        $artists[] = $album->artists;
        define('FILENAME','./message.txt');
            // タイムゾーン設定
        date_default_timezone_set('Asia/Tokyo');
        function write($track_name,$artist_name,$url){
        if( $file_handle = fopen( FILENAME, "a") ) {
                $message = null;
                // 書き込み日時を取得
            $current_date = date("Y-m-d H:i:s");
                // 書き込むデータを作る  
            $data = "'".$track_name."','".$artist_name."','".$message."','".$current_date."','".$url."'\n";
    
                // 書き込み
            fwrite($file_handle, $data);
    
                // ファイルを閉じる
            fclose($file_handle);
    
    }};
        $n = 0;
        foreach($items as $items){
        if(($items->track->album->album_type)!=="album"){
        $n = $n +1;
        echo $n;
        echo " ";
        print_r($items->track->album->name);
        $track_name[$n] = $items->track->album->name;
        echo "/";
        print_r($items->track->album->artists[0]->name);
        $artist_name[$n] = $items->track->album->artists[0]->name;
        echo "\n";
        //print_r($items->track->preview_url);
        $url[$n] =  $items->track->preview_url;
        write($track_name[$n],$artist_name[$n],$url[$n]);
  
        }else{
        $n = $n +1;
        echo $n;
        echo " ";
        print_r($items->track->name);
        $track_name[$n] = $items->track->name;
        echo "/";
        print_r($items->track->album->artists[0]->name);
        $artist_name[$n] = $items->track->album->artists[0]->name;
        echo "\n";
        //print_r($items->track->preview_url);
        $url[$n] =  $items->track->preview_url;
        write($track_name[$n],$artist_name[$n],$url[$n]);

        }	
        }
    echo '</pre>';

    } else {
        $scope = [
            'scope' => [
              'user-read-recently-played'
            ]
          ];
        header('Location: ' . $session->getAuthorizeUrl($scope));
        die();
    }
    header("Location:./home.php");
