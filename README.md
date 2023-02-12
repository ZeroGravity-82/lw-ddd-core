# DDD core library for Last Wishes projects

Provides a set of classes and interfaces for creating Last Wishes projects based on the [Domain-Driven Design](https://en.wikipedia.org/wiki/Domain-driven_design) methodology.

The set includes:
- Abstract class for entity
- Abstract class for entity ID
- Abstract class for entity factory
- Abstract class for entity collection
- Abstract class for aggregate root
- Abstract class for domain events
- Event dispatcher class
- Classes for domain exceptions
- Interface for identity generator

## Installation as a project dependency

The preferred way to install this library is through [Composer](http://getcomposer.org/).

First, add the private VCS repository to your project's composer.json:
```
"repositories": [
  {
    "type": "vcs",
    "url": "git@github.com:ZeroGravity-82/lw-ddd-core.git"
  }
],
```
Then install the library with Composer:
```
composer require sibers/lw-ddd-core:^1.0
```

## Local development

To configure PhpStorm + Docker + Xdebug, please see this article: <https://blog.denisbondar.com/post/phpstorm_docker_xdebug/>

This project requires the Docker Compose plugin to be installed: <https://docs.docker.com/compose/install/linux/>

Run the following console commands before starting local development:
```bash
export HOST_USER_UID=$(id -u) && export HOST_USER_GID=$(id -g)
make init
```
**Tip**: To avoid manually creating the HOST_USER_UID and HOST_USER_GID variables each time, just add their creation to the ~/.bashrc file.

CLI tools
---------

For convenient work with the project through Docker, use Makefile. It contains examples of how to use all the CLI tools available to you.
