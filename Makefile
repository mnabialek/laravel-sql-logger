.PHONY: help setup up down shell composer test cs-check cs-fix clean build

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

setup: ## Initial setup - build container and install dependencies
	@chmod +x docker/scripts/docker-setup.sh
	@./docker/scripts/docker-setup.sh

build: ## Build the container
	@docker compose build
	@echo "✅ Container built"

up: ## Start the container
	@docker compose up -d
	@echo "✅ Container started"

down: ## Stop the container
	@docker compose down
	@echo "✅ Container stopped"

shell: ## Access container shell
	@docker compose run --rm php bash

composer: ## Run composer commands (usage: make composer require package)
	@docker compose run --rm php composer $(filter-out $@,$(MAKECMDGOALS))

test: ## Run PHPUnit tests
	@docker compose run --rm php vendor/bin/phpunit

test-coverage: ## Run tests with HTML coverage report
	@docker compose run --rm php vendor/bin/phpunit --coverage-html build/coverage
	@echo "✅ Coverage report generated at build/coverage/index.html"

test-coverage-text: ## Run tests with text coverage output
	@docker compose run --rm php vendor/bin/phpunit --coverage-text

cs-check: ## Check code style with PHP CS Fixer
	@docker compose run --rm php vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Fix code style issues automatically
	@docker compose run --rm php vendor/bin/php-cs-fixer fix

phpstan: ## Run PHPStan static analysis
	@docker compose run --rm php vendor/bin/phpstan analyse

clean: ## Remove container and volumes
	@docker compose down -v
	@echo "✅ Containers and volumes removed"

# Catch-all target for passing arguments to composer
%:
	@:
