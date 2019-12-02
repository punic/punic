#!/usr/bin/env bash

set -o errexit
set -o nounset

cd "$( dirname "${BASH_SOURCE[0]}" )/.."

echo '### DISABLING XDEBUG'
phpenv config-rm xdebug.ini || true

echo '### REMOVING PHPUNIT FROM COMPOSER DEPENDENCIES'
composer --no-interaction remove --dev --no-update --no-scripts phpunit/phpunit

echo '### INSTALLING COMPOSER DEPENDENCIES'
composer --no-interaction install --prefer-dist --optimize-autoloader --no-suggest
