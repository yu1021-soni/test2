init:
	docker-compose up -d --build
	docker-compose exec php composer install
	test -f src/.env || cp -p src/.env.example src/.env
	docker-compose exec php php artisan key:generate
	docker-compose exec php php artisan migrate:fresh --seed
	docker-compose exec php php artisan storage:link

stripe:
	docker-compose exec php composer require stripe/stripe-php