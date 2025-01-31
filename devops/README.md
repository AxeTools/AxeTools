# devops
Contains the files that are needed to build and run the axetools project within a docker environment

## Directory Structure

### `images/`

The `images/` directory contain the Dockerfiles that are used to build both the development and production images 
that are used by the axetools along with any configuration files needed by the images.

### `axetools-dev/`

The `axetools-dev/` directory contains the `docker-compose` files that are needed to run the development containers 
to develop the axetools project and add new features, correct bugs, run tests and modify existing behavior.

### `axetools-prod/`

The `axetools-prod/` directory contains the `docker-compose` files that are needed to run the production containers.
