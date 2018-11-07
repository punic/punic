#!/usr/bin/env bash

set -o errexit
set -o nounset

if test $DO_TESTS -ge 2; then

    echo '### DISABLING XDEBUG PHP EXTENSION'
    phpenv config-rm xdebug.ini || true

    echo '### SENDING COVERAGE DATA TO COVERALLS'
    cd "$TRAVIS_BUILD_DIR"
    ./vendor/bin/php-coveralls --no-interaction --coverage_clover=coverage-clover.xml --json_path=coveralls-upload.json

    echo '### DOWNLOADING SCRITINIZER CLIENT'
    cd "$TRAVIS_BUILD_DIR"
    curl --location --output ocular.phar --retry 3 --silent --show-error https://scrutinizer-ci.com/ocular.phar
    echo '### SENDING COVERAGE DATA TO SCRUTINIZER'
    php ocular.phar code-coverage:upload --format=php-clover coverage-clover.xml
fi
