# test2

## 環境構築

**Dockerビルド**
1.
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. .envの記述を以下に変更
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel_db
   DB_USERNAME=laravel_user
   DB_PASSWORD=laravel_pass
4. アプリケーションキーの作成
   `php artisan key:generate`
5. マイグレーションの実行
   `php artisan migrate`

## 初期データ投入
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ItemSeeder

## 画像保存設定
php artisan storage:link

## メール認証テスト

Mailhogを利用
ブラウザで以下にアクセス
http://localhost:8025

php artisan queue:work

## テスト実行
php artisan test


## URL

・アプリケーション: http://localhost:8080
・Mailhog: http://localhost:8025