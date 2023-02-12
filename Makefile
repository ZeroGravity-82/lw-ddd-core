DOCKER_COMPOSE = docker compose
PHP_CLI        = $(DOCKER_COMPOSE) run --rm ddd-core-php-cli

getargs    = $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
escapeagrs = $(subst :,\:,$(1))

.PHONY: dummy tests

##
## Project maintenance ("make init", "make composer-install")
## ----------------------------------------------------------
all:
	@echo 'Please provide a command, for example, "make composer-install"'
init: docker-down-clear docker-pull docker-build composer-install
docker-down-clear:
	$(DOCKER_COMPOSE) down -v --remove-orphans
docker-pull:
	$(DOCKER_COMPOSE) pull
docker-build:
	$(DOCKER_COMPOSE) build
composer-install:
	$(PHP_CLI) composer install
composer-update:
	$(PHP_CLI) composer update
composer-dumpautoload:
	$(PHP_CLI) composer dumpautoload -o

##
## Code quality tests ("make tests")
## ---------------------------------
tests: phpunit

##
## Run unit tests ("make -- phpunit --filter testOne UnitTest.php" or "make -- phpunit --exclude-group database")
## --------------------------------------------------------------------------------------------------------------
ifeq (phpunit,$(firstword $(MAKECMDGOALS)))
    PHPUNIT_ARGS         := $(call getargs)
    PHPUNIT_ARGS_ESCAPED := $(call escapeagrs, $(PHPUNIT_ARGS))
    $(eval $(PHPUNIT_ARGS_ESCAPED):dummy;@:)
endif
phpunit:
	$(PHP_CLI) vendor/bin/phpunit $(PHPUNIT_ARGS) $(-*-command-variables-*-)

##
## Run PHP CLI command ("make -- php-cli ls -la /app")
## ---------------------------------------------------
ifeq (php-cli,$(firstword $(MAKECMDGOALS)))
    PHP_CLI_ARGS         := $(call getargs)
    PHP_CLI_ARGS_ESCAPED := $(call escapeagrs, $(PHP_CLI_ARGS))
    $(eval $(PHP_CLI_ARGS_ESCAPED):dummy;@:)
endif
php-cli:
	$(PHP_CLI) $(PHP_CLI_ARGS) $(-*-command-variables-*-)
