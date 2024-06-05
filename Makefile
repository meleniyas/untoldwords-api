#!/bin/bash
include .env

test: ## testing variables ##
	@echo ${USER}

help: ## Show this help message ##
	@echo .
	@findstr /e /c:"##" ${MAKEFILE_LIST} | more

start: ## Start the containers ##
	@docker-compose up -d

stop: ## Stop the containers ##
	@docker-compose stop

down: ## Down the containers ##
	@docker-compose -f docker-compose.yml down

clean:
	@make down
	@docker volume rm app-bbdd-data

restart: ## Restart the containers ##
	$(MAKE) stop && $(MAKE) start

build: ## Rebuilds all the containers ##
	@docker-compose up -d --build --force-recreate

prepare: ## Runs backend commands ##
	$(MAKE) composer-install

run: ## starts the course development server in detached mode ##
	docker exec -it --user ${USER} ${APP_CONTAINER} course serve -d

logs: ## Show course logs in real time ##
	docker exec -it --user ${USER} ${APP_CONTAINER} course server:log

# Backend commands
composer-install: ## Installs composer dependencies ##
	docker exec --user ${USER} ${APP_CONTAINER} composer install --no-interaction
# End backend commands

ssh: ## bash into the be container ##
	docker exec -it  ${APP_CONTAINER} bash
