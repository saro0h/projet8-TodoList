# Commands
BLACKFIRE = $(EXEC) blackfire
CONSOLE	= $(EXEC) $(CONTAINER-NAME) bin/console
DOCKER = docker
DOCKER-COMPOSE = $(DOCKER)-compose
EXEC = $(DOCKER-COMPOSE) exec -T
TESTS = vendor/bin/phpunit

# Default values
CONTAINER-NAME ?= php
DEPLOY_HOST ?= localhost
DEPLOY_USER ?= $(USER)
ENV ?= dev
GIT_BRANCH ?= dev

# Blackfire
.PHONY: blackfire

blackfire:				## Run blackfire
						$(BLACKFIRE) blackfire curl http://caddy/$(URL)

# Database
.PHONY: db-create db-drop db-fixtures db-migrations
db-create:				## Create Database
						$(CONSOLE) doctrine:database:create --if-not-exists --env=$(ENV)

db-drop:				## Delete Database
						$(CONSOLE) doctrine:database:drop --if-exists --force --env=$(ENV)

db-fixtures:			## Launch fixtures
						$(CONSOLE) doctrine:fixtures:load --no-interaction --env=$(ENV)

db-migrations:			## Execute Doctrine migrations
						$(CONSOLE) doctrine:migrations:migrate --no-interaction --env=$(ENV)

# Docker Compose commands
.PHONY: dc-build dc-down dc-exec dc-start dc-stop dc-up dc-prod
dc-build:				## Build docker images
						$(DOCKER-COMPOSE) build --pull

dc-build-debug:			## Build docker images
						$(DOCKER-COMPOSE) -f docker-compose.yaml -f docker-compose.debug.yaml build --pull

dc-debug:				## Initialize the project with Docker in debug mode
						$(DOCKER-COMPOSE) -f docker-compose.yaml -f docker-compose.debug.yaml up -d

dc-down:				## Delete containers and volumes
						$(DOCKER-COMPOSE) down --remove-orphans --volumes

dc-exec:				## Interact with a container
						$(DOCKER) exec -it $(CONTAINER-NAME) sh

dc-start:				## Start docker containers
						$(DOCKER-COMPOSE) start

dc-stop:				## Stop docker containers
						$(DOCKER-COMPOSE) stop

dc-up:					## Initialize the project with Docker
						$(DOCKER-COMPOSE) up -d

dc-prod:				## Initialize the project with Docker in prod environment
						$(DOCKER-COMPOSE) -f docker-compose.yaml -f docker-compose.prod.yaml up -d

# Symfony Commands
.PHONY: sf-cc sf-cw
sf-cc:					## Clear Symfony cache
						$(CONSOLE) cache:clear --env=$(ENV)

sf-cw:					## Warmup Symfony cache
						$(CONSOLE) cache:warmup --env=$(ENV)

# Tests
.PHONY: coverage tests reset-coverage reset-tests
coverage: 				## Run the tests with the Code coverage report
						$(EXEC) --env XDEBUG_MODE=coverage $(CONTAINER-NAME) $(TESTS) --coverage-html vendor/coverage

tests: 					## Run the tests
						$(EXEC) $(CONTAINER-NAME) $(TESTS)

reset-coverage:			## Recreate database, launch migrations, load fixtures and execute tests with code coverage
						ENV=test make db-drop
						ENV=test make db-create
						ENV=test make db-migrations
						ENV=test make db-fixtures
						make coverage

reset-tests: 			## Recreate database, launch migrations, load fixtures and execute tests
						ENV=test make db-drop
						ENV=test make db-create
						ENV=test make db-migrations
						ENV=test make db-fixtures
						make tests

# Help
.PHONY: help

help:					## Display help
						grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.DEFAULT_GOAL := 	help
