# **Vue Symfony Starter** Documentation site

**Vue Symfony Starter**: A 'simple' starter project using Vue.js and Symfony.

The objective of the project is to provide a project template with full build / tests / deploy automation while providing as much "_standard_" features as usually found in recent web applications.

## Technologies

This project uses the following technologies:

-   [Symfony 4.4](https://symfony.com/releases/4.4) with:
    -   [Twig](https://twig.symfony.com/) templates with full [translation support](https://symfony.com/doc/4.4/translation/templates.html)
    -   [Messenger component](https://symfony.com/doc/4.4/components/messenger.html) to send messages to background workers
    -   Custom [Console Commands](https://symfony.com/doc/current/console.html) for support and cron jobs automation
    -   Code quality tools: [PHPUnit](https://phpunit.de/), [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer), [Psalm](https://psalm.dev/)

-   [Vue.js](https://vuejs.org/) frontend with:
    -   full [TypeScript](https://www.typescriptlang.org/) support
    -   [Vuex](https://vuex.vuejs.org/) state management and [axios](https://github.com/axios/axios) HTTP client
    -   full [Sass](https://sass-lang.com/) support
    -   [Bulma](https://bulma.io/) with [Buefy](https://buefy.org/) integration
    -   [WebPack](https://webpack.js.org/) to build efficiently assets
    -   [StoryBook](https://storybook.js.org/) to help development of the UI components
    -   Code quality tools: [ESLint](https://eslint.org/), [stylelint](https://stylelint.io/) with [stylelint-config-sass-guidelines](https://github.com/bjankord/stylelint-config-sass-guidelines)

-   [Docker](https://docs.docker.com/engine/) and [Docker-Compose](https://docs.docker.com/compose/) for building development and production environment with:
    -   [RabbitMQ](https://www.rabbitmq.com/) for delegating long tasks to background workers
    -   Mail sending with [MailCatcher](https://mailcatcher.me/) for simple mail debug with GUI
    -   LDAP Authentication with [rroemhild/docker-test-openldap](https://github.com/rroemhild/docker-test-openldap) for simple LDAP test server

-   CI tools:
    -   DockerHub [Advanced Automated Build](https://docs.docker.com/docker-hub/builds/advanced/) hooks
    -   [GitHub Actions](https://docs.github.com/en/actions) using DockerHub Advanced Automated Build hooks
    -   [Travis-CI](https://travis-ci.com/) using DockerHub Advanced Automated Build hooks
    -   [Jenkins](https://www.jenkins.io/) (experimental) support with sample [Jenkinsfile](https://www.jenkins.io/doc/book/pipeline/jenkinsfile/)
    -   [Codacy](https://www.codacy.com/) code quality and code coverage review
    -   [Snyk](https://snyk.io/) security review

-   Monitoring, Reporting and SEO:
    -   Integration with Google Analytics and Matomo
    -   Integration with Prometheus / Grafana and custom metrics possible (POC)
    -   Integration with ELK Stack possible (POC)

-   Source Code Management templates:
    -   GitHub [Issue and PR templates](https://docs.github.com/en/github/building-a-strong-community/configuring-issue-templates-for-your-repository)
    -   GitLab [Issue and MR templates](https://docs.gitlab.com/ee/user/project/description_templates.html)

-   Ready to Code:
    -   [![Open in Gitpod](https://gitpod.io/button/open-in-gitpod.svg)](https://gitpod.io/#https://github.com/Monogramm/vue-symfony-starter)

## Architecture diagram

![Architecture Production Diagram](architecture.svg)

Directory structure:

-   `app`: The main application directory
    -   `assets`: everything regarding the Frontend VUE app
        -   `i18n`: Frontend app translations
        -   `styles`: Frontend app global SCSS
        -   `vue`: Frontend Vue.js
    -   `src`: everything regarding the Symfony Backend
        -   `Command`: Symfony [Console Commands](https://symfony.com/doc/current/console.html). Mostly used for CRON and automation.
        -   `Controller`: Symfony REST API [Controllers](https://symfony.com/doc/current/controller.html) for APP.
        -   `DataFixtures`: [Dummy Data fixtures](https://symfony.com/doc/current/testing/database.html#dummy-data-fixtures). Currently only used for tests purposes.
        -   `DTO`: Data Transfer Object. Define _custom_ objects to interact with the API when it's not appropriate to use the Entities.
        -   `Entity`: Symfony (Doctrine) [Entities](https://symfony.com/doc/current/doctrine.html) that are saved in the persistence storage.
        -   `Event`: Symfony [Events and Event Subscribers](https://symfony.com/doc/current/event_dispatcher.html). Mostly used to trigger asynchronous tasks on the Messenger.
        -   `EventListner`: Symfony [Event Listeners](https://symfony.com/doc/current/event_dispatcher.html).
        -   `Message`: Long tasks messages for the backend Symfony [Messenger](https://symfony.com/doc/current/messenger.html).
            -   `Handler`: Long tasks handlers for the backend MESSENGER.
        -   `Migrations`: Persistence storage [Migrations](https://symfony.com/doc/current/doctrine.html#migrations-creating-the-database-tables-schema). Used by backend to check database status and by APP to install/update database.
        -   `Repository`: Symfony (Doctrine) [Entity Repositories](https://symfony.com/doc/current/doctrine.html) that manage the persistence storage.
        -   `Service`: Symfony [services](https://symfony.com/doc/current/service_container.html).
    -   `templates`: [Twig](https://symfony.com/doc/current/templates.html) templates. Used to generate HTML index page and emails.
    -   `tests`: Backend Symofny [testing](https://symfony.com/doc/current/testing.html). Used to test APP controllers, CRON commands, ... Provides Unit and Integration tests.
    -   `translations`: Backend Symfony [translations](https://symfony.com/doc/current/translation.html). Mostly used for emails and error messages.
-   `cron`: The main CRON jobs directory. Each subdirectory contains bash scripts to will be executed periodically.
-   `docker`: Test/Dev/Prod docker related configuration
-   `hooks`: CI build/test/publish hooks for DockerHub (shared with GitHub Actions and Travis-CI)

## How to use

Check repository on GitHub for details: <https://github.com/Monogramm/vue-symfony-starter>

## Contributing

For information about contributing, see the [Contributing page](https://github.com/Monogramm/vue-symfony-starter/blob/master/CONTRIBUTING.md).

## License

For information about license, see the [license page](https://github.com/Monogramm/vue-symfony-starter/blob/main/LICENSE).
