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
dev-up:
	@echo "##### Bringing up Dev Containers #####"
	@test -s ${DEV}/compose.override.yaml || { echo "ERROR: compose.override.yaml is missing"; exit 1; }
	@(cd ${DEV} && docker compose up -d)

#
# Bring down the dev containers
#
dev-down:
	@echo "##### Bringing down Dev Containers #####"
	@(cd ${DEV} && docker compose down)

#
# Execute a Bash terminal on the dev php container
#
dev-bash: dev-up
	@echo "##### Dev php Container Bash Prompt #####"
	@(cd ${DEV} && docker compose exec php bash)

#
# Execute tests against the dev php container
#
dev-test: dev-up
	@echo "##### Dev php Container Tests #####"
	@(cd ${DEV} && docker compose exec php composer tests)


#
# Build the production docker files
#
dev-install: dev-up
	@echo "##### Installing Composer Dependencies #####"
	@(cd ${DEV} && docker compose exec php composer install)

#
# Build the production docker files
#
build:
	@echo "##### Building Production Containers #####"
	@docker build -f devops/images/nginx_prod.Dockerfile -t axetools_nginx:latest .
	@docker build -f devops/images/axetools_php_prod.Dockerfile -t axetools:latest .

#
# Bring up the Production docker containers
#
prod:
	@echo "##### Bringing up Production Containers #####"
	@test -s ${PROD}/compose.override.yaml || { echo "ERROR: compose.override.yaml is missing"; exit 1; }
	@(cd ${PROD} && docker compose up -d)

#
# Bring down the Production docker containers
#
down:
	@echo "##### Bringing up Production Containers #####"
	@test -s ${PROD}/compose.override.yaml || { echo "ERROR: compose.override.yaml is missing"; exit 1; }
	@(cd ${PROD} && docker compose down)


#
# build and bring up the production containers
#
default: build prod