#!/usr/bin/env bash

set -o errexit
set -o nounset

cd "$( dirname "${BASH_SOURCE[0]}" )/.."

CODE_COVERAGE=0
ASSUME_PHP=

while test $# -gt 0; do
    case "$1" in
        coverage)
            CODE_COVERAGE=1
            ;;
        assume-php)
            if test $# -lt 2; then
                printf 'Missing PHP version after "%s"\n' "$1" >&2
                exit 1
            fi
            ASSUME_PHP=$2
            shift 1
            ;;
        *)
            printf 'Invalid option: "%s"\n' "$1" >&2
            exit 1
            ;;
    esac
    shift 1
done

if test $CODE_COVERAGE -eq 0; then
    echo '### DISABLING XDEBUG'
    phpenv config-rm xdebug.ini || true
else
    export COMPOSER_ALLOW_XDEBUG=1
fi

if test -n "$ASSUME_PHP"; then
    printf '### INSTUCTING COMPOSER TO ASSUME PHP VERSION %s\n' "$ASSUME_PHP"
    composer --no-interaction config platform.php "$ASSUME_PHP"
fi

echo '### REMOVING PHP-CS-FIXER FROM COMPOSER DEPENDENCIES'
composer --no-interaction remove --dev --no-update --no-scripts friendsofphp/php-cs-fixer

if test $CODE_COVERAGE -ne 0; then
    echo '### ADDING COVERALLS TO COMPOSER DEPENDENCIES'
    composer --no-interaction require --dev --no-suggest --no-update 'php-coveralls/php-coveralls:^2.0'

    echo '### ADDING SCRUTINIZER TO COMPOSER DEPENDENCIES'
    composer --no-interaction require --dev --no-suggest --no-update 'scrutinizer/ocular:^1.0'
fi

echo '### INSTALLING COMPOSER DEPENDENCIES'
composer --no-interaction install --prefer-dist --optimize-autoloader --no-suggest
