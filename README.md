# test2 プロジェクト概要
このプロジェクトは「coachtechフリマ」の開発計画に基づいたフリマアプリケーションです。

ユーザー登録・ログイン、商品出品・購入、コメント、Stripe 決済、メール認証などの機能を備えています。

開発の目的は「アイテムの出品と購入を行えるフリマアプリを実装し、初年度でのユーザー数1000人達成」を目標としています。

## 環境構築手順

1. リポジトリをクローン

   `git clone https://github.com/yu1021-soni/test2.git`

2. Docker起動

   `cd test2/docker`

   `docker-compose up -d --build`

   Apple Silicon (M1/M2) でビルドできない場合

   docker-compose.yml の services.mysql に以下を追加してください

   ```
   services:
      mysql:
         platform: linux/x86_64(この文追加)
         image: mysql:8.0.26
         environment:
   ```
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成。

   `cd ../src`

   `cp -p .env.example .env`

4. .envの記述を以下に変更

   ```
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

   # Stripe のキーは各自でダッシュボードから発行してください
   STRIPE_KEY=pk_test_xxxxx
   STRIPE_SECRET=sk_test_xxxxx
   STRIPE_WEBHOOK_SECRET=whsec_xxxxx
   STRIPE_CURRENCY=JPY
   STRIPE_TEST_FORCE_COMPLETE=1
   ```

5. Composerインストール

   `cd ../docker`

   `docker-compose exec php bash`

   `composer install`


6. Stripe の PHP SDK を追加インストール

   `docker-compose exec php bash`

   `composer require stripe/stripe-php`

7. アプリケーションキーの作成

   `php artisan key:generate`

8. マイグレーション & シーディング

   `php artisan migrate:fresh --seed`

9. ストレージリンク

   `php artisan storage:link`

## メール認証テスト

   Mailhogを利用。

   ブラウザで以下にアクセス。

   http://localhost:8025

   `php artisan queue:work`

## テスト実行
1. テスト専用の環境設定ファイル .env.testing を用意
2. .env.testingに以下を記述

   テスト用アプリケーションキーの作成

   `php artisan key:generate --env=testing`
   ```
   APP_ENV=testing
   APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

   APP_DEBUG=true

   DB_CONNECTION=sqlite
   DB_DATABASE=:memory:

   CACHE_DRIVER=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   ```

3. テスト実行

`php artisan test`

## 使用技術

・基盤
- PHP 8.1.33
- Laravel 8.83.29
- Mysql 8.0.26
- nginx 1.21.1
- Composer 2.8.12
- Docker / Docker Compose

・主要パッケージ
- Laravel Fortify
- PHPUnit

・開発用ツール
- phpMyAdmin
- Mailhog

## ER図
![ER Diagram](docs/images/test2.png)

## URL
- アプリケーション: http://localhost
- phpMyAdmin: http://localhost:8080
- Mailhog: http://localhost:8025
