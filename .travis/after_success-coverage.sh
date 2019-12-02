#!/usr/bin/env bash

set -o errexit
set -o nounset

cd "$( dirname "${BASH_SOURCE[0]}" )/.."

echo '### SENDING COVERAGE DATA TO COVERALLS'
cd "$TRAVIS_BUILD_DIR"
./vendor/bin/php-coveralls --no-interaction --coverage_clover=coverage-clover.xml --json_path=coveralls-upload.json

echo '### SENDING COVERAGE DATA TO SCRUTINIZER'
./vendor/bin/ocular code-coverage:upload --format=php-clover coverage-clover.xml
