# Makefile pre Laravel 12 s Laravel Sail

# Premenné
SHELL := /bin/bash
COMPOSE := docker compose
SAIL := ./vendor/bin/sail

# Inštalácia projektu
install: ## Inštaluje projekt
	composer install --ignore-platform-reqs
	cp .env.example .env
	$(SAIL) up -d
	$(SAIL) artisan key:generate
	$(SAIL) npm install
	$(SAIL) npm run build
	$(SAIL) artisan migrate --seed

# Spustenie kontajnerov
start: ## Spustí Laravel Sail kontajnery
	$(SAIL) up -d
	$(SAIL) npm run dev

# Zastavenie kontajnerov
stop: ## Zastaví Laravel Sail kontajnery
	$(SAIL) down

# Vyčistenie kontajnerov a vymazanie dát
reset: ## Resetne kontajnery a databázu
	$(SAIL) down -v
	$(SAIL) up -d
	$(SAIL) artisan migrate:fresh --seed

packages: ## Vytvorí symlink na lokálne package
	ln -s ../../packages packages

help: ## Zobrazí zoznam dostupných príkazov
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'
