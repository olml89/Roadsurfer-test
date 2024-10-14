DOCKER_COMPOSE = docker-compose -f docker/docker-compose.yml --env-file .env.local

.PHONY: build
build:
	$(DOCKER_COMPOSE) build

.PHONY: upd
upd:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml up -d --remove-orphans

.PHONY: down
down:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml down

.PHONY: ssh
ssh:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml exec php /bin/sh

.PHONY: listen
listen:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml exec php php -S 0.0.0.0:80 -t public

.PHONY: reset_test_db
reset_test_db:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml exec php ./bin/console --env=test doctrine:database:drop --if-exists --force
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml exec php ./bin/console --env=test doctrine:database:create
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml exec php ./bin/console --env=test doctrine:migrations:migrate --no-interaction

.PHONY: test
test:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml exec php ./bin/phpunit --coverage-text

.PHONY: phpstan
phpstan:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml exec php ./vendor/bin/phpstan