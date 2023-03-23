# Commands
BLACKFIRE = $(EXEC) blackfire
CONSOLE	= $(EXEC) $(CONTAINER_NAME) bin/console
DOCKER = docker
DOCKER_COMPOSE = $(DOCKER)-compose
EXEC = $(DOCKER_COMPOSE) exec
TESTS = vendor/bin/phpunit

# Default values
CONTAINER_NAME ?= php
APP_ENV ?= dev

# Install
.PHONY: install

install:				## Install the project for dev environment
install:				.env.local dc-up db-reset

.env.local:
						@test ! -f .env.local && cp .env .env.local && echo .env.local file created

# Blackfire
.PHONY: blackfire

blackfire:				## Run blackfire
						$(BLACKFIRE) blackfire curl http://caddy/$(URL)

# Database
.PHONY: db-create db-drop db-fixtures db-migrations db-reset
db-create:				## Create Database
						$(CONSOLE) doctrine:database:create --if-not-exists --env=$(APP_ENV)

db-drop:				## Delete Database
						$(CONSOLE) doctrine:database:drop --if-exists --force --env=$(APP_ENV)

db-fixtures:			## Launch fixtures
						$(if $(filter $(APP_ENV), dev or test),$(CONSOLE) doctrine:fixtures:load --no-interaction --env=$(APP_ENV))

db-migrations:			## Execute Doctrine migrations
						$(CONSOLE) doctrine:migrations:migrate --no-interaction --env=$(APP_ENV)

db-reset:				## Reset Database
db-reset:				db-drop db-create db-migrations db-fixtures

# Docker Compose commands
.PHONY: dc-build dc-down dc-exec dc-start dc-stop dc-up dc-prod
dc-build:				## Build docker images
						$(DOCKER_COMPOSE) build --pull

dc-build-debug:			## Build docker images
						$(DOCKER_COMPOSE) -f docker-compose.yaml -f docker-compose.debug.yaml build --pull

dc-debug:				## Initialize the project with Docker in debug mode
						$(DOCKER_COMPOSE) -f docker-compose.yaml -f docker-compose.debug.yaml up -d

dc-down:				## Delete containers and volumes
						$(DOCKER_COMPOSE) down --remove-orphans --volumes

dc-exec:				## Interact with a container
						$(DOCKER_COMPOSE) exec $(CONTAINER_NAME) sh

dc-start:				## Start docker containers
						$(DOCKER_COMPOSE) start

dc-stop:				## Stop docker containers
						$(DOCKER_COMPOSE) stop

dc-up:					## Initialize the project with Docker
						$(DOCKER_COMPOSE) up -d

dc-prod:				## Initialize the project with Docker in prod APP_ENVironment
						$(DOCKER_COMPOSE) -f docker-compose.yaml -f docker-compose.prod.yaml up -d

dc-logs:				CONTAINER_NAME := $(word 2, $(MAKECMDGOALS))
dc-logs:				## Interact with a container
						@$(DOCKER_COMPOSE) logs -f $(CONTAINER_NAME)

dc-ps:					## Show running containers
						@$(DOCKER_COMPOSE) ps

dc-trust-certificate:	## Trust SSL certificate for Caddy Server
						@$(DOCKER) cp $$($(DOCKER_COMPOSE) ps -q caddy):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt

# Symfony Commands
.PHONY: sf-cc sf-cw
sf-cc:					## Clear Symfony cache
						$(CONSOLE) cache:clear --env=$(APP_ENV)

sf-cw:					## Warmup Symfony cache
						$(CONSOLE) cache:warmup --env=$(APP_ENV)

# Checks
.PHONY: php-cs-fixer phpstan twigcs yaml-lint rector

checks: 				## Run checks
checks: 				php-cs-fixer twigcs yaml-lint phpstan rector

php-cs-fixer:			## Run php-cs-fixer
						PHP_CS_FIXER_IGNORE_ENV=true vendor/bin/php-cs-fixer fix --diff --dry-run --verbose

phpstan:				## Run phpstan
						vendor/bin/phpstan analyse --memory-limit=1G

twigcs:					## Run twig-cs
						vendor/bin/twigcs templates

yaml-lint:	            ## Run yaml-lint
						bin/console lint:yaml config translations

rector:					## Run rector
						vendor/bin/rector --dry-run

# Tests
.PHONY: tests tests-reset coverage coverage-reset

tests:		   			export APP_ENV=test
tests: 					## Run the tests
						$(EXEC) $(CONTAINER_NAME) $(TESTS)

tests-reset:			export APP_ENV=test
tests-reset: 			## Recreate database, launch migrations, load fixtures and execute tests
tests-reset: 			db-reset tests

coverage:   			export APP_ENV=test
coverage: 				## Run the tests with the Code coverage report
						$(EXEC) --env XDEBUG_MODE=coverage $(CONTAINER_NAME) $(TESTS) --coverage-html vendor/coverage

coverage-reset:			export APP_ENV=test
coverage-reset:			## Recreate database, launch migrations, load fixtures and execute tests with code coverage
coverage-reset: 		db-reset coverage

# Help
.PHONY: help

help:					## Display help
						@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.DEFAULT_GOAL := 	help
