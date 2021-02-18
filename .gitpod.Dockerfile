FROM gitpod/workspace-full

# Install custom tools, runtimes, etc.
# For example "bastet", a command-line tetris clone:
# RUN brew install bastet
#
# More information: https://www.gitpod.io/docs/config-docker/

RUN set -ex; \
    wget 'https://get.symfony.com/cli/installer' -O - | bash; \
    export PATH="$HOME/.symfony/bin:$PATH"; \
    symfony -V; \
    brew install mailhog openldap postgresql rabbitmq; \
    sudo apt-get install -y --no-install-recommends \
        php-ldap \
    ;
