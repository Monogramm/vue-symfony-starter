#!/bin/bash
set -e

###########################################################
# Functions

log() {
    echo "[$0] [$(date +%Y-%m-%dT%H:%M:%S)] $*"
}

function ask_field() {
    local FIELD=$1
    local MESSAGE=$2
    local DEFAULT_VALUE=$3

    local TEMP=
    echo "$MESSAGE (or leave empty for default value '$DEFAULT_VALUE'):"
    read -r -e TEMP
    echo ' '
    export "$FIELD"="${TEMP:-$DEFAULT_VALUE}"
}

lc-check() {
    symfony check:requirements --dir=app
    symfony check:security --dir=app
}

lc-build() {
    # Backend install
    log "Backend install..."
    composer install --working-dir=app
    symfony server:ca:install

    if [ ! -f 'app/.env.local' ]; then
        log "Init local environment..."
        cat <<EOF > 'app/.env.local'
###> app/service/encryptor ###
# Custom encryptor configuration
# Generate one with </dev/urandom tr -dc 'A-Za-z0-9+\-*_' | head -c 32 ; echo
ENCRYPTOR_KEY=$(</dev/urandom tr -dc 'A-Za-z0-9+\-*_' | head -c 32 ; echo)
###< app/service/encryptor ###

###> lexik/jwt-authentication-bundle ###
JWT_PASSPHRASE=P@ssw0rd
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
###< lexik/jwt-authentication-bundle ###

# Paypal configuration
PAYPAL_CLIENT_ID=client_id
PAYPAL_CLIENT_SECRET=client_secret
EOF
    fi

    if [ ! -e "app/config/jwt/public.pem" ]; then
        log "Generating keys for JWT authentication..."

        mkdir -p app/config/jwt
        rm -f app/config/jwt/*.pem

        export JWT_PASSPHRASE=P@ssw0rd
        openssl genpkey -out app/config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass "pass:${JWT_PASSPHRASE}"
        openssl pkey -in app/config/jwt/private.pem -passin "pass:${JWT_PASSPHRASE}" -out app/config/jwt/public.pem -pubout

        export JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
        export JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem

        chmod 644 \
            app/config/jwt/private.pem \
            app/config/jwt/public.pem

        log "Keys for JWT authentication generated"
    fi

    log "Checking application's database status..."
    php app/bin/console doctrine:migrations:status

    if ! php app/bin/console doctrine:migrations:up-to-date; then
        log "Executing application's database migration..."
        php app/bin/console doctrine:migrations:migrate --no-interaction
        log "Application's database migrations applied."
    fi

    # Frontend install
    log "Frontend install..."
    yarn install --cwd=app
    cd app && npm install && cd ..

}

lc-start-front() {
    yarn run --cwd=app encore dev --watch
}

lc-start-back() {
    symfony server:start --dir=app --port=8000
}

lc-start-story() {
    cd app
    npm run storybook
}

lc-stop-back() {
    symfony server:stop --dir=app "$@"
}

lc-test-back() {
    cd app
    php ./bin/phpunit --coverage-xml ./var/tests/ "$@"
    #log "PHPStan..."
    #vendor/bin/phpstan analyse src tests
    log "PHP Copy/Paste detector..."
    vendor/bin/phpcpd src
    log "PHP_CodeSniffer bug fixer..."
    vendor/bin/phpcbf src
    log "PHP_CodeSniffer..."
    vendor/bin/phpcs src
    #log "Psalm..."
    #vendor/bin/psalm --alter --issues=MissingReturnType,InvalidReturnType,InvalidNullableReturnType --dry-run
    log "PHPMD..."
    vendor/bin/phpmd src text cleancode,controversial,codesize,naming,design,unusedcode
    #vendor/bin/phpmd src xml phpmd.xml
}

lc-log-back() {
    symfony server:log --dir=app "$@"
}

lc-log-ps() {
    symfony server:list "$@"
}

lc-console() {
    #symfony console --dir=app "$@"
    php app/bin/console "$@"
}

lc-prepare-release() {
    NEW_VERSION=${1}
    if [ -z "${NEW_VERSION}" ] ; then
        log 'Missing release version!'
        return 1;
    fi
    #NEW_VERSION=$(grep '"version"' .gitmoji-changelogrc | cut -d'"' -f4)

    log 'Updating gitmoji-changelog version...'
    sed -i \
        -e "s|\"version\": \".*\"|\"version\": \"${NEW_VERSION}\"|g" \
        app/package.json app/composer.json .gitmoji-changelogrc
    sed -i \
        -e "s| VERSION=.*| VERSION=${NEW_VERSION}|g" \
        .travis.yml Dockerfile.alpine Dockerfile.debian

    # Generate changelog for current version
    log "Installing dev dependencies for Changelog..."
    yarn --cwd ./app install

    log "Generating Changelog for version '${NEW_VERSION}'..."
    yarn --cwd ./app run gitmoji-changelog
    sed '/\*   Merge branch/d;s/ \\\[.*//' app/CHANGELOG.md > CHANGELOG.md
}

lc-after-release() {
    CURRENT_VERSION=$(grep '"version"' .gitmoji-changelogrc | cut -d'"' -f4)
    NEXT_VERSION=${1}

    # Update next version in existing files
    if [ -z "${NEXT_VERSION}" ]; then
        BASE_VERSION=$(echo "$CURRENT_VERSION" | cut -d. -f1-2)
        FIX_VERSION=$(echo "$CURRENT_VERSION" | cut -d. -f3)
        NEXT_VERSION="${BASE_VERSION}.$(( FIX_VERSION + 1 ))"
    fi
    sed -i \
        -e "s|\"version\": \".*\"|\"version\": \"${NEXT_VERSION}\"|g" \
        .gitmoji-changelogrc
}

lc-release() {
    VERSION=${1}

    git branch -a | grep -q " develop$" || {
      git checkout -b develop origin/develop
    }
    git checkout develop && git pull || exit 2
    lc-prepare-release "${VERSION}"
    git add CHANGELOG.md .gitmoji-changelogrc .travis.yml Dockerfile* app/*json
    git commit -m":bookmark: Release ${VERSION}"
    echo "Version ${VERSION} is now HEAD of develop."
    git push
    git checkout master && git pull || exit 2
    git merge develop && git tag "${VERSION}"
    echo "Version ${VERSION} is now HEAD of master and tagged if all went well."
    echo "Please double check and amend last commit if needed."
    echo "Finally, push the release to remote master branch:"
    echo "  $ git push"
    echo "  $ git push origin ${VERSION}"
}

lc-prepare-docker() {
    CURRENT_VERSION=$(grep '"version"' package.json | cut -d'"' -f4)

    # Update Dockerfile build args default values
    local VCS_REF
    VCS_REF=$(git rev-parse --short HEAD)
    local BUILD_DATE
    BUILD_DATE=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

    sed -i \
        -e "s|ARG VERSION=.*|ARG VERSION=${CURRENT_VERSION}|g" \
        -e "s|ARG VCS_REF=.*|ARG VCS_REF=${VCS_REF}|g" \
        -e "s|ARG BUILD_DATE=.*|ARG BUILD_DATE=${BUILD_DATE}|g" \
        Dockerfile.alpine Dockerfile.debian
}

init_compose() {
    if [ ! -f '.env' ]; then
        log 'Init docker compose environment variables...'
        cp .env_template .env.tmp

        mv .env.tmp .env
    fi
    export VARIANT=alpine
    export BASE=fpm

    export DOCKER_REPO=monogramm/vue-symfony-starter
    export DOCKERFILE_PATH=Dockerfile.${VARIANT}
    export DOCKER_TAG=${VARIANT}
    export IMAGE_NAME=${DOCKER_REPO}:${DOCKER_TAG}
}

dc() {
    init_compose

    docker-compose -f "$@"
}

build() {
    CURRENT_VERSION=$(grep '"version"' app/package.json | cut -d'"' -f4)

    log 'Building container(s)...'
    dc "${1}" build \
        --build-arg STORIES=true \
        --build-arg VERSION="${CURRENT_VERSION}" \
        --build-arg VCS_REF=$(git rev-parse --short HEAD) \
        --build-arg BUILD_DATE=$(date -u +"%Y-%m-%dT%H:%M:%SZ") \
        "${@:2}"
}

start() {
    log 'Starting container(s)...'
    dc "${1}" up -d "${@:2}"
}

stop() {
    log 'Stopping container(s)...'
    dc "${1}" stop "${@:2}"
}

restart() {
    log 'Restarting container(s)...'
    dc "${1}" restart "${@:2}"
}

logs() {
    log 'Following container(s) logs (Ctrl + C to stop)...'
    dc "${1}" logs -f "${@:2}"
}

dc-ps() {
    log 'Checking container(s)...'
    dc "${1}" ps "${@:2}"
}

down() {
    log 'Stopping and removing container(s)...'
    dc "${1}" down "${@:2}"
}

dc-console() {
    dc "${1}" exec "${2}" php bin/console "${@:3}"
}

usage() {
    echo "usage: ./manage.sh COMMAND [ARGUMENTS]

    Commands:
      local
        local:check, check-local                Check Local env requirements and security
        local:build, build-local                Build Local env
        local:start-front, start-local-front    Start Local env frontend
        local:start-back, start-local-back      Start Local env backend
        local:start-story, start-local-story    Start Local Storybook
        local:restart, restart-local            Retart Local env
        local:stop-back, stop-local-back        Stop Local env
        local:test-back, test-back-local        Execute test of Local env
        local:logs, logs-local                  Follow logs of Local env
        local:ps, ps-local                      List Local env servers
        local:console, console                  Send command to Local env bin/console
        local:prepare-release, prepare-release  Prepare release
        local:after-release, after-release      Update version after release
        local:prepare-docker, prepare-docker    Prepare docker build args

      dev
        dev:build, build-dev                    Build Docker Dev env
        dev:start, start-dev                    Start Docker Dev env
        dev:restart, restart-dev                Retart Docker Dev env
        dev:stop, stop-dev                      Stop Docker Dev env
        dev:logs, logs-dev                      Follow logs of Docker Dev env
        dev:down, down-dev                      Stop and remove Docker Dev env
        dev:reset, reset-dev                    Stop and remove Docker Dev env, and remove all data
        dev:ps, ps-dev                          List Docker Prod env containers
        dev:console, console-dev                Send command to Docker Dev env bin/console

      prod
        prod:build, build-prod, build           Build Docker Prod env
        prod:start, start-prod, start           Start Docker Prod env
        prod:restart, restart-prod, restart     Retart Docker Prod env
        prod:stop, stop-prod, stop              Stop Docker Prod env
        prod:logs, logs-prod, logs              Follow logs of Docker Prod env
        prod:down, down-prod, down              Stop and remove Docker Prod env
        prod:reset, reset-prod, reset           Stop and remove Docker Prod env, and remove all data
        prod:ps, ps-prod, ps                    List Docker Prod env containers
        prod:console, console-prod, console     Send command to Docker Prod env bin/console

    "
}

###########################################################
# Runtime

case "${1}" in
    # Local env
    local:check|check-local) lc-check;;
    local:build|build-local) lc-build;;
    local:start-front|start-local-front) lc-start-front;;
    local:start-back|start-local-back) lc-start-back;;
    local:start-story|start-local-story) lc-start-story;;
    local:stop-back|stop-local-back) lc-stop-back "${@:2}";;
    local:test-back|test-back-local) lc-test-back "${@:2}";;
    local:logs|logs-local) lc-log-back "${@:2}";;
    local:ps|ps-local) lc-log-ps "${@:2}";;
    local:console|console-local) lc-console "${@:2}";;
    local:prepare-release|prepare-release) lc-prepare-release "${@:2}";;
    local:after-release|after-release) lc-after-release "${@:2}";;
    local:release|release) lc-release "${@:2}";;
    local:prepare-docker|prepare-docker) lc-prepare-docker "${@:2}";;

    # DEV env
    dev:build|build-dev) build docker-compose.yml "${@:2}";;
    dev:start|start-dev) start docker-compose.yml "${@:2}";;
    dev:restart|restart-dev) restart docker-compose.yml "${@:2}";;
    dev:stop|stop-dev) stop docker-compose.yml "${@:2}";;
    dev:logs|logs-dev) logs docker-compose.yml "${@:2}";;
    dev:down|down-dev) down docker-compose.yml "${@:2}";;
    dev:reset|reset-dev) down docker-compose.yml "${@:2}";
    . .env;
    sudo rm -rf "${APP_HOME:-/srv/app}_dev"
    ;;
    dev:ps|ps-dev) dc-ps docker-compose.yml "${@:2}";;
    dev:console|console-dev)
    dc-console docker-compose.yml app_dev_symfony "${@:2}";;

    # PROD env
    prod:build|build-prod|build) build "docker-compose.${BASE:-fpm}.test.yml" "${@:2}";;
    prod:start|start-prod|start) start "docker-compose.${BASE:-fpm}.test.yml" "${@:2}";;
    prod:restart|restart-prod|restart) restart "docker-compose.${BASE:-fpm}.test.yml" "${@:2}";;
    prod:stop|stop-prod|stop) stop "docker-compose.${BASE:-fpm}.test.yml" "${@:2}";;
    prod:logs|logs-prod|logs) logs "docker-compose.${BASE:-fpm}.test.yml" "${@:2}";;
    prod:down|down-prod|down) down "docker-compose.${BASE:-fpm}.test.yml" "${@:2}";;
    prod:reset|reset-prod|reset) down "docker-compose.${BASE:-fpm}.test.yml" "${@:2}";
    . .env;
    sudo rm -rf "${APP_HOME:-/srv/app}"
    ;;
    prod:ps|ps-prod|ps) dc-ps "docker-compose.${BASE:-fpm}.test.yml" "${@:2}";;
    prod:console|console-prod|console)
    dc-console "docker-compose.${BASE:-fpm}.test.yml" app_backend "${@:2}";;

    # Help
    *) usage;;
esac

exit 0
