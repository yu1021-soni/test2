init:
	docker-compose up -d --build
	docker-compose exec php composer install
	docker-compose exec php cp .env.example .env
	docker-compose exec php php artisan key:generate
	docker-compose exec php php artisan migrate:fresh --seed
	docker-compose exec php php artisan storage:link
	docker-compose restart nginx

stripe:
	docker-compose exec php composer require stripe/stripe-php