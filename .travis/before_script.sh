#!/usr/bin/env bash

set -o errexit
set -o nounset

if test $DO_TESTS -ge 1 || test $DO_CODINGSTYLE -ge 1 || test $DO_UPDATEAPI -ge 1; then

    if test "$TRAVIS_PHP_VERSION" != 'hhvm' && test $DO_TESTS -lt 2; then
        echo '### DISABLING XDEBUG PHP EXTENSION'
        phpenv config-rm xdebug.ini || true
    else
        export COMPOSER_DISABLE_XDEBUG_WARN=1
    fi
    
    if test $DO_TESTS -ge 1 || test $DO_CODINGSTYLE -ge 1; then
        cd "$TRAVIS_BUILD_DIR"

        if test $DO_CODINGSTYLE -lt 1; then
            echo '### REMOVING PHP-CS-FIXER FROM COMPOSER DEPENDENCIES'
            composer --no-interaction remove --dev --no-update --no-scripts friendsofphp/php-cs-fixer
        fi
        
        if test $DO_TESTS -lt 1; then
            echo '### REMOVING PHPUNIT FROM COMPOSER DEPENDENCIES'
            composer --no-interaction remove --dev --no-update --no-scripts phpunit/phpunit
        fi

        if test $DO_TESTS -ge 2; then
            echo '### ADDING COVERALLS TO COMPOSER DEPENDENCIES'
            composer --no-interaction require --dev --no-suggest --no-update 'php-coveralls/php-coveralls:^2.0'
        fi

        echo '### INSTALLING MAIN COMPOSER DEPENDENCIES'
        composer --no-interaction install --prefer-dist --optimize-autoloader --no-suggest
    fi
fi
