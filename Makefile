start:
	docker-compose up -d
	symfony server:start -d

stop:
	docker-compose stop
	symfony server:stop