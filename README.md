[![License: AGPL v3][uri_license_image]][uri_license]
[![Docs](https://img.shields.io/badge/Docs-Github%20Pages-blue)](https://monogramm.github.io/vue-symfony-starter/)
[![gitmoji-changelog](https://img.shields.io/badge/Changelog-gitmoji-blue.svg)](https://github.com/frinyvonnick/gitmoji-changelog)
[![Managed with Taiga.io](https://img.shields.io/badge/Managed%20with-TAIGA.io-709f14.svg)](https://tree.taiga.io/project/monogrammbot-monogrammvue-symfony-starter/ "Managed with Taiga.io")
[![GitHub stars](https://img.shields.io/github/stars/Monogramm/vue-symfony-starter?style=social)](https://github.com/Monogramm/vue-symfony-starter)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Monogramm/vue-symfony-starter/Docker%20Image%20CI)](https://github.com/Monogramm/vue-symfony-starter/actions)

<!--
[TODO] If project uses Coveralls for code coverage:

[![Coverage Status](https://coveralls.io/repos/github/Monogramm/vue-symfony-starter/badge.svg?branch=master)](https://coveralls.io/github/Monogramm/vue-symfony-starter?branch=master)
-->

<!--
[TODO] If project is deployed to DockerHub:

[![Docker Automated buid](https://img.shields.io/docker/cloud/build/monogramm/vue-symfony-starter.svg)](https://hub.docker.com/r/monogramm/vue-symfony-starter/)
[![Docker Pulls](https://img.shields.io/docker/pulls/monogramm/vue-symfony-starter.svg)](https://hub.docker.com/r/monogramm/vue-symfony-starter/)
[![Docker Version](https://images.microbadger.com/badges/version/monogramm/vue-symfony-starter.svg)](https://microbadger.com/images/monogramm/vue-symfony-starter)
[![Docker Size](https://images.microbadger.com/badges/image/monogramm/vue-symfony-starter.svg)](https://microbadger.com/images/monogramm/vue-symfony-starter)
-->

# **Vue Symfony Starter**

> :alembic: A 'simple' starter project using Vue.js and Symfony.

:construction: **This project is still in development!**

## :blue_book: Docs

See GitHub Pages at [monogramm.github.io/vue-symfony-starter](https://monogramm.github.io/vue-symfony-starter/).

## :chart_with_upwards_trend: Changes

All notable changes to this project will be documented in [CHANGELOG](./CHANGELOG.md) file.

This CHANGELOG is generated with :heart: by [gitmoji-changelog](https://github.com/frinyvonnick/gitmoji-changelog).
<!--
To generate new changelog:
* update `.gitmoji-changelogrc`
* execute `gitmoji-changelog --preset generic`

-->

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## :bookmark: Roadmap

See [Taiga.io](https://tree.taiga.io/project/monogrammbot-monogrammvue-symfony-starter/ "Taiga.io monogrammbot-monogrammvue-symfony-starter")

## Docker Development environment

You can build and run a development environment using Docker (recommended).

### :construction: Install

```bash
./manage.sh dev:build
```

### :rocket: Usage

```bash
./manage.sh dev:start
```

Now go to <http://localhost:8000> to access development environment using docker.
Go to <http://localhost:6006> to access the storybook.

## Docker Production environment

You can build and run a "_production-like_" environment using Docker.

### :construction: Install

```bash
./manage.sh prod:build
```

### :rocket: Usage

```bash
./manage.sh prod:start
```

Now go to <http://localhost:8080> to access development environment using docker.

## Local Development environment

You can run the development environment locally (not recommened, prefer the Docker development environment).

### :construction: Install

Requires PHP 7.4 or higher, [Composer 2](https://getcomposer.org/), [Symfony 4.4](https://symfony.com/) and [yarn](https://yarnpkg.com/) installed.
You can check your local requirements with the following command:

```bash
./manage.sh local:check
```

To install locally:

```bash
./manage.sh local:build
```

### :rocket: Usage

To start frontend:

```sh
./manage.sh local:start-front
```

To start backend:

```sh
./manage.sh local:start-back
```

Now go to <http://localhost:8000> to access development environment using your local host.

To start local storybook:

```sh
./manage.sh local:start-back
```

Now go to <http://localhost:6006> to access locally the storybook.

## :white_check_mark: Run tests

### Unit tests

```bash
cd app
php bin/phpunit
```

### Unit tests with code coverage report in HTML format

```bash
cd app
php bin/phpunit --coverage-html=./
```

### Code style check

```bash
cd app
php vendor/bin/phpcs
```

### Static analysis tool

```bash
cd app
php vendor/bin/psalm
```

<!--
[TODO] If project is deployed to DockerHub:

## :whale: Supported Docker tags

[Dockerhub monogramm/vue-symfony-starter](https://hub.docker.com/r/monogramm/vue-symfony-starter/)

* `latest`

-->

## :bust_in_silhouette: Authors

**Monogramm**

-   Website: <https://www.monogramm.io>
-   Github: [@Monogramm](https://github.com/Monogramm)

**Mathieu BRUNOT**

-   GitHub: [@madmath03](https://github.com/madmath03)

**Artur Khachaturyan**

-   Github: [@Arky9782](https://github.com/orgs/Monogramm/people/Arky9782)

## :handshake: Contributing

Contributions, issues and feature requests are welcome!<br />Feel free to check [issues page](https://github.com/Monogramm/vue-symfony-starter/issues).
[Check the contributing guide](./CONTRIBUTING.md).<br />

## :thumbsup: Show your support

Give a :star: if this project helped you!

## :page_facing_up: License

Copyright © 2021 [Monogramm](https://github.com/Monogramm).<br />
This project is [AGPL v3](uri_license) licensed.

* * *

_This README was generated with :heart: by [readme-md-generator](https://github.com/kefranabg/readme-md-generator)_

[uri_license]: http://www.gnu.org/licenses/agpl.html

[uri_license_image]: https://img.shields.io/badge/License-AGPL%20v3-blue.svg
