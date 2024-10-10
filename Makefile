.PHONY: build
build:
	docker-compose -f docker/docker-compose.yml build

.PHONY: upd
upd:
	docker-compose -f docker/docker-compose.yml up -d --remove-orphans

.PHONY: down
down:
	docker-compose -f docker/docker-compose.yml down

.PHONY: ssh
ssh:
	docker-compose -f docker/docker-compose.yml exec php /bin/sh

.PHONY: test
test:
	docker-compose -f docker/docker-compose.yml exec php ./bin/phpunit