#!/bin/sh

set -e

log() {
    echo "[$0] [$(date +%Y-%m-%dT%H:%M:%S)] $*"
}

################################################################################
# Testing docker containers

log "Waiting to ensure everything is fully ready for the tests..."
sleep 60

log "Checking main containers are reachable..."
if ! ping -c 10 -q "${DOCKER_TEST_CONTAINER}" ; then
    log 'Main container is not responding!'
    # TODO Display logs to help bug fixing
    #log 'Check the following logs for details:'
    #tail -n 100 logs/*.log
    exit 2
fi


################################################################################
# Success
log 'Docker tests successful'


################################################################################
# Automated Service Unit tests
# https://docs.docker.com/docker-hub/builds/automated-testing/
################################################################################

if [ -n "${DOCKER_WEB_CONTAINER}" ]; then

    if ! ping -c 10 -q "${DOCKER_WEB_CONTAINER}" ; then
        log 'Web container is not responding!'
        # TODO Display logs to help bug fixing
        #log 'Check the following logs for details:'
        #tail -n 100 logs/*.log
        exit 2
    fi

    log 'Checking Health API is responding...'
    curl --fail "http://${DOCKER_WEB_CONTAINER}:80/health" | grep -q -e 'UP' || exit 1
fi

if [ -n "${COVERALLS_REPO_TOKEN}" ]; then
    log 'Installing PHP-Coveralls locally...'

    composer require --dev php-coveralls/php-coveralls
    php vendor/bin/php-coveralls --help

    if [ -f '/var/www/html/.docker/tests-coverage-clover.xml' ]; then
        log 'TODO Send tests coverage to Coveralls...'
        # FIXME fatal: Failed to execute command git branch: not a git repository (or any of the parent directories): .git
        php vendor/bin/php-coveralls \
            --json_path=/tmp/tests-coveralls-upload.json \
            --root_dir=/var/www/html/ \
            --coverage_clover=/var/www/html/.docker/tests-coverage-clover.xml \
            -v
    else
        log 'No tests coverage to send to Coveralls.'
    fi
fi

################################################################################
# Success
echo "Docker app '${DOCKER_TEST_CONTAINER}' tests finished"
echo 'Check the CI reports and logs for details.'
exit 0
