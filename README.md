# music_d

spotifyの履歴を表示し、コメントを付けたり検索したりcsvファイルを作ったりする一連のシステム

# ダイアグラム

![名称未設定 drawio (1)](https://user-images.githubusercontent.com/95104894/159476987-4291191b-dfab-4334-a9cc-19592c034ca3.png)
# Features
* spotify再生履歴からartist情報と曲情報を取り出して、sqlに保存する。  
* それぞれの曲にコメントを打つことができその情報を保存する。  
* 保存した曲情報を検索できる。曲名とアーティスト名から検索でき、部分一致で検索する。  
* それぞれの曲情報をサイトから削除することができる。  
* sqlの情報をcsｖファイルにして出力することができる。出力時に最大出力数を１０件と３０件に設定することができる。  
  
ホーム画面：
![スクリーンショット 2022-03-22 21 23 06](https://user-images.githubusercontent.com/95104894/159481233-465ee74b-9889-42d4-b3f7-c4935d22f16d.png)




# 使ったもの


* PHP 7.3 or later.
* PHP cURL extension (Usually included with PHP).


# インストールしたもの

Install it using Composer:

```bash
composer require jwilsson/spotify-web-api-php
```

# 使い方
spotify.apiに接続するためには使用者のspotifyidとseceret_codeが必要。
index.phpに入力する。  
index.phpを起動すれば自動的にhome.phpの画面に繋がる。

# Note
このシステムは主に私用および、インターンのポートフォリオとして作ったものである。  
今後の拡張可能性としては、preview_Urlを取得できることから、編集画面で曲の一部を流せるようにすることが考えられる。sqlにurlをそのまま貼るとバグが発生してしまうため、綺麗に処理する必要がある。
このシステムは主に私用および、インターンのポートフォリオとして作ったものである。  



