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
    -   full [Sass](https://sass-lang.com/) support
    -   [Bulma](https://bulma.io/) with [Buefy](https://buefy.org/) integration
    -   [WebPack](https://webpack.js.org/) to build efficiently assets
    -   [StoryBook](https://storybook.js.org/) to help development of the UI components
    -   [ESLint](https://eslint.org/) for code quality
-   [Docker](https://docs.docker.com/engine/) and [Docker-Compose](https://docs.docker.com/compose/) for building development and production environment with:
    -   [RabbitMQ](https://www.rabbitmq.com/) for delegating long tasks to background workers
    -   Mail sending with [MailCatcher](https://mailcatcher.me/) for simple mail debug with GUI
    -   LDAP Authentication with [rroemhild/docker-test-openldap](https://github.com/rroemhild/docker-test-openldap) for simple LDAP test server
-   CI tools:
    -   DockerHub [Advanced Automated Build](https://docs.docker.com/docker-hub/builds/advanced/)
    -   [GitHub Actions](https://docs.github.com/en/actions) using DockerHub Advanced Automated Build hooks
    -   [Jenkins](https://www.jenkins.io/) support with sample [Jenkinsfile](https://www.jenkins.io/doc/book/pipeline/jenkinsfile/)
    -   [Codacy](https://www.codacy.com/) code quality review
    -   [Snyk](https://snyk.io/) security review
    -   [Coveralls](https://coveralls.io/) code coverage review
-   Source Code Management templates:
    -   GitHub [Issue and PR templates](https://docs.github.com/en/github/building-a-strong-community/configuring-issue-templates-for-your-repository)
    -   GitLab [Issue and MR templates](https://docs.gitlab.com/ee/user/project/description_templates.html)

## Architecture diagram

![Architecture Production Diagram](architecture.svg)

## How to use

Check repository on GitHub for details: <https://github.com/Monogramm/vue-symfony-starter>

## Contributing

For information about contributing, see the [Contributing page](https://github.com/Monogramm/vue-symfony-starter/blob/master/CONTRIBUTING.md).
