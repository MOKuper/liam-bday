.PHONY: up down build install migrate fresh seed shell composer artisan npm

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

install:
	docker-compose run --rm composer create-project laravel/laravel . --prefer-dist
	docker-compose run --rm php cp .env.example .env
	docker-compose run --rm php php artisan key:generate

migrate:
	docker-compose run --rm php php artisan migrate

fresh:
	docker-compose run --rm php php artisan migrate:fresh --seed

seed:
	docker-compose run --rm php php artisan db:seed

shell:
	docker-compose exec php bash

composer:
	docker-compose run --rm composer $(filter-out $@,$(MAKECMDGOALS))

artisan:
	docker-compose run --rm php php artisan $(filter-out $@,$(MAKECMDGOALS))

npm:
	docker-compose run --rm --service-ports node npm $(filter-out $@,$(MAKECMDGOALS))

# Prevent make from trying to interpret additional arguments as targets
%:
	@: