###############################################################################
#     _             _____           _
#    / \   __  ____|_   _|__   ___ | |___
#   / _ \  \ \/ / _ \| |/ _ \ / _ \| / __|
#  / ___ \  >  <  __/| | (_) | (_) | \__ \
# /_/   \_\/_/\_\___||_|\___/ \___/|_|___/
#
###############################################################################

DEV=devops/axetools-dev
PROD=devops/axetools-prod

.DEFAULT_GOAL := default

#
# Bring up the dev containers
#
docker-dev-up:
	@echo "##### Bringing up Dev Containers #####"
	@test -s ${DEV}/docker-compose.override.yml || { echo "ERROR: docker-compose.override.yml is missing"; exit 1; }
	@(cd ${DEV} && docker compose up -d)

#
# Bring down the dev containers
#
docker-dev-down:
	@echo "##### Bringing down Dev Containers #####"
	@(cd ${DEV} && docker compose down)

#
# Execute a Bash terminal on the dev php container
#
docker-dev-bash: docker-dev-up
	@echo "##### Dev php Container Bash Prompt #####"
	@(cd ${DEV} && docker compose exec php bash)

#
# Execute tests against the dev php container
#
docker-dev-test: docker-dev-up
	@echo "##### Dev php Container Tests #####"
	@(cd ${DEV} && docker compose exec php composer tests)


#
# Build the production docker files
#
docker-dev-install: docker-dev-up
	@echo "##### Installing Composer Dependencies #####"
	@(cd ${DEV} && docker compose exec php composer install)

#
# Build the production docker files
#
docker-build:
	@echo "##### Building Production Containers #####"
	@docker build -f devops/images/nginx_prod.Dockerfile -t axetools_nginx:latest .
	@docker build -f devops/images/axetools_php_prod.Dockerfile -t axetools:latest .

#
# Bring up the Production docker containers
#
docker-prod:
	@echo "##### Bringing up Production Containers #####"
	@test -s ${PROD}/docker-compose.override.yml || { echo "ERROR: docker-compose.override.yml is missing"; exit 1; }
	@(cd ${PROD} && docker compose up -d)

#
# Bring down the Production docker containers
#
docker-down:
	@echo "##### Bringing up Production Containers #####"
	@test -s ${PROD}/docker-compose.override.yml || { echo "ERROR: docker-compose.override.yml is missing"; exit 1; }
	@(cd ${PROD} && docker compose down)


#
# build and bring up the production containers
#
default: docker-build docker-prod