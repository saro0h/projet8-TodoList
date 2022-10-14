.?: tests
tests:
	php bin/console d:d:d --force --if-exists --env=test
	php bin/console d:d:c --env=test
	php bin/console d:m:m --no-interaction --env=test
	php bin/console h:f:l --no-interaction --env=test
	php bin/phpunit --testdox tests/Entity/
	php bin/phpunit --testdox tests/Controller/
	php bin/phpunit --testdox tests/Security/
	      
