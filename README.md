# test2

## 環境構築手順

1. リポジトリをクローン
   https://github.com/yu1021-soni/test2.git

2. Docker起動
   `docker-compose up -d --build`

   Apple Silicon (M1/M2) でビルドできない場合
   docker-compose.yml の services.mysql に以下を追加してください:

   services:
      mysql:
         platform: linux/x86_64
         image: mysql:8.0.26
         environment:

3. `docker-compose exec php bash`

4. Composerインストール
   `composer install`

5. .envの記述を以下に変更

   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel_db
   DB_USERNAME=laravel_user
   DB_PASSWORD=laravel_pass

   MAIL_MAILER=smtp
   MAIL_HOST=mailhog
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS=admin@example.com
   MAIL_FROM_NAME="${APP_NAME}"

6. アプリケーションキーの作成
   `php artisan key:generate`

7. マイグレーション & シーディング
   `php artisan migrate:fresh --seed`

8. ストレージリンク
   `php artisan storage:link`

## メール認証テスト

Mailhogを利用
ブラウザで以下にアクセス
http://localhost:8025
`php artisan queue:work`

## テスト実行
`php artisan test`

## 使用技術

・基盤
- PHP 8.1.33
- Laravel 8.83.29
- Mysql 8.0.26
- Composer 2.8.12
- Docker / Docker Compose

・主要パッケージ
- Laravel Fortify
- PHPUnit

・開発用ツール
- phpMyAdmin
- Mailhog


## URL
・アプリケーション: http://localhost
・phpMyAdmin: http://localhost:8080
・Mailhog: http://localhost:8025