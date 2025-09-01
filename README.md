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