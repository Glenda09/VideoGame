.PHONY: up down restart seed migrate migrate-fresh test pest coverage cs-fix horizon logs docs

up:
	@docker compose up -d --build

down:
	@docker compose down

restart:
	@docker compose down
	@docker compose up -d --build

seed:
	@docker compose exec app php artisan migrate --seed

migrate:
	@docker compose exec app php artisan migrate

migrate-fresh:
	@docker compose exec app php artisan migrate:fresh --seed

test:
	@docker compose exec app php artisan test --parallel

pest:
	@docker compose exec app ./vendor/bin/pest

coverage:
	@docker compose exec app ./vendor/bin/pest --coverage

cs-fix:
	@docker compose exec app ./vendor/bin/php-cs-fixer fix

horizon:
	@docker compose --profile queue up -d horizon

logs:
	@docker compose logs -f

docs:
	@docker compose exec app php artisan openapi:generate
