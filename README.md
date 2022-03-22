# music_diary

spotifyの履歴を表示し、コメントを付けたり検索したりcsvファイルを作ったりする一連のシステム

# DEMO

![名称未設定 drawio (1)](https://user-images.githubusercontent.com/95104894/159476987-4291191b-dfab-4334-a9cc-19592c034ca3.png)
# Features
* spotify再生履歴からartist情報と曲情報を取り出して、sqlに保存する。  
* それぞれの曲にコメントを打つことができその情報を保存する。  
* 保存した曲情報を検索できる。曲名とアーティスト名から検索でき、部分一致で検索する。  
* それぞれの曲情報をサイトから削除することができる。  
* sqlの情報をcsｖファイルにして出力することができる。出力時に最大出力数を１０件と３０件に設定することができる。  
  
ホーム画面：
![スクリーンショット 2022-03-21 23 11 00](https://user-images.githubusercontent.com/95104894/159479998-bc4d68b4-5e73-4b7c-a224-bf3cb520891a.png)


# 使ったもの


* PHP 7.3 or later.
* PHP cURL extension (Usually included with PHP).


# インストールしたもの

Install it using Composer:

```bash
composer require jwilsson/spotify-web-api-php
```

# Usage

index.phpを起動すれば自動的にhome.phpの画面に繋がる。

# Note

注意点などがあれば書く


