# Riddlestone Brokkr-Users

A [Laminas](https://github.com/laminas) module to provide [Doctrine](https://github.com/doctrine/DoctrineORMModule)
stored users

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
composer require riddlestone/brokkr-users
```

## Usage

This module adds a Doctrine entity for persisting users, and controllers for managing them, and allowing them to
login/logout.

For information on setting up Doctrine in Laminas, see the
[DoctrineORMModule](https://github.com/doctrine/DoctrineORMModule) documentation.

This module does not provide the routing or view-scripts for the included controllers - this is to allow Laminas-based
projects as much flexibility as possible in their use of this module.

For a list of controllers and actions, including their respective view-script paths and variables, see the
[Controllers and Actions](docs/ControllerActions.md) documentation.

## Get Involved

File issues at https://github.com/riddlestone/brokkr-users/issues
