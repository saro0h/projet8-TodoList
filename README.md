[![SymfonyInsight](https://insight.symfony.com/projects/f40d8b12-3acd-4f62-ace2-44cbd43f011c/big.svg)](https://insight.symfony.com/projects/f40d8b12-3acd-4f62-ace2-44cbd43f011c)

[![Maintainability](https://api.codeclimate.com/v1/badges/9bfd853e2d634bc3dbc9/maintainability)](https://codeclimate.com/github/Casrime/todolist/maintainability)


Todolist
========================

Requirements
------------

* PHP 8.0 or higher;
* PDO-SQLite PHP extension enabled;
* and the [usual Symfony application requirements][1].

Installation
------------
You need to install :
- [Docker Engine][2]
- [Docker Compose][3]

Install the project :
```bash
$ make install
```

Usage
------------
Boot containers :
```bash
$ make dc-up
```

To interact with the PHP container :
```bash
$ make dc-exec
```

Create database, run migrations and load fixtures :
```bash
$ make db-reset
```

Tests
------------

Execute this command to run tests:
```bash
$ make tests
```

Code coverage
------------
```bash
$ make coverage
```

[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://docs.docker.com/installation/
[3]: https://docs.docker.com/compose/
