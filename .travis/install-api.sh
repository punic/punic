#!/usr/bin/env bash

set -o errexit
set -o nounset

cd "$( dirname "${BASH_SOURCE[0]}" )/../docs"

echo '### DISABLING XDEBUG'
phpenv config-rm xdebug.ini || true

echo '### INSTALLING COMPOSER DEPENDENCIES'
composer --no-interaction install --prefer-dist --optimize-autoloader --no-suggest
