CONSOLE	= $(PHP) bin/console
PHP = $(EXEC) php
TESTS = $(PHP) bin/phpunit

# Tests
.PHONY: coverage tests

coverage:				## Run the tests with the Code coverage report
						$(CONSOLE) doctrine:database:drop --env=test --if-exists --force
						$(CONSOLE) doctrine:database:create --env=test
						$(CONSOLE) doctrine:schema:update --env=test --no-interaction --force
						$(CONSOLE) doctrine:fixtures:load --env=test --no-interaction
						$(TESTS) --coverage-html var/data

tests:					## Run the tests
						$(CONSOLE) doctrine:database:drop --env=test --if-exists --force
						$(CONSOLE) doctrine:database:create --env=test
						$(CONSOLE) doctrine:schema:update --env=test --no-interaction --force
						$(CONSOLE) doctrine:fixtures:load --env=test --no-interaction
						$(TESTS)

# Help
.PHONY: help

help:					## Display help
						@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.DEFAULT_GOAL := 	help