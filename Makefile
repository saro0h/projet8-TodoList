.PHONY: test init-db test-fresh

COMPOSE_TEST_FILES = -f compose.yaml

# Reset the database
init-db:
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:database:drop --force --if-exists
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:database:create
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:schema:update --force
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:fixtures:load --no-interaction

# Reset the test database
init-db-test:
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:database:drop --force --env=test --if-exists
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:database:create --env=test
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:schema:update --force --env=test
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:fixtures:load --no-interaction --env=test

# Run tests
test:
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:fixtures:load --no-interaction --env=test
	docker compose $(COMPOSE_TEST_FILES) exec php ./vendor/bin/phpunit

# Reset test db and run tests
test-fresh: init-db-test test
