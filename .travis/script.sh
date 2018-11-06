#!/usr/bin/env bash

set -o errexit
set -o nounset

if test $DO_CODINGSTYLE -ge 1; then
    echo '### CHECKING CODING STYLE ###'
    cd "$TRAVIS_BUILD_DIR"
    composer --no-interaction run-script cs -- fix --no-interaction --dry-run --diff --using-cache=no -v --config=.php_cs.dist
fi

if test $DO_TESTS -ge 2; then
    echo '### RUNNING TESTS (WITH CODE COVERAGE) ###'
    cd "$TRAVIS_BUILD_DIR"
    COMPOSER_ALLOW_XDEBUG=1 composer --no-interaction run-script test -- --coverage-clover coverage-clover.xml
elif test $DO_TESTS -ge 1; then
    echo '### Running tests (without code coverage)'
    composer --no-interaction run-script test
fi

if test $DO_UPDATEAPI -ge 1; then
    echo '### UPDATING API DOCS ###'
    API_REPOSITORY_OWNER='punic'
    API_REPOSITORY_NAME='punic'
    API_PROCESS_BRANCH='master'
    API_COMMIT_MESSAGE='[skip ci] Update APIs'
    API_COMMIT_AUTHOR_NAME='concrete5 TravisCI Bot'
    API_COMMIT_AUTHOR_EMAIL='concrete5-bot@concrete5.org'
    if test "${TRAVIS_PULL_REQUEST:-}" != 'false'; then
        printf "# skipping because it's a pull request\n"
    elif test "${TRAVIS_BRANCH:-}" != "$API_PROCESS_BRANCH"; then
        printf '# skipping because pushing to "%s" instead of "%s"\n' "${TRAVIS_BRANCH:-}" "$API_PROCESS_BRANCH"
    elif test "${TRAVIS_REPO_SLUG:-}" != "$API_REPOSITORY_OWNER/$API_REPOSITORY_NAME"; then
        printf '# skipping because the repository is "%s" instead of "%s/%s"\n' "${TRAVIS_REPO_SLUG:-}" "$API_REPOSITORY_OWNER" "$API_REPOSITORY_NAME"
    elif test -z "${GITHUB_ACCESS_TOKEN:-}"; then
        printf '# skipping because GITHUB_ACCESS_TOKEN is not available
To create it:
 - go to https://github.com/settings/tokens/new
 - create a new token
 - sudo apt update
 - sudo apt install -y build-essential ruby ruby-dev
 - sudo gem install travis
 - travis encrypt --repo %s/%s GITHUB_ACCESS_TOKEN=<TOKEN>
 - Add to the env setting of:
   secure: "encrypted string"
' "$API_REPOSITORY_OWNER" "$API_REPOSITORY_NAME"
    else
        printf '# checking out branch "%s"\n' "$API_PROCESS_BRANCH"
        cd "$TRAVIS_BUILD_DIR"
        git checkout --quiet --force "$API_PROCESS_BRANCH"
        printf '# installing docs composer dependencies\n'
        cd "$TRAVIS_BUILD_DIR/docs"
        composer --no-interaction install --prefer-dist --optimize-autoloader --no-suggest
        printf '# removing old API docs\n'
        rm -rf "$TRAVIS_BUILD_DIR/themes/punic/static/api"
        printf '# generating new API docs\n'
        composer --no-interaction run-script update-docs
        printf '# checking changes\n'
        cd "$TRAVIS_BUILD_DIR/themes/punic/static/api"
        if test -z "$(git status --porcelain .)"; then
            printf '# no changes detected\n'
        else
            printf '# staging changes\n'
            git add --all .
            printf '# committing changes\n'
            git config user.name "$API_COMMIT_AUTHOR_NAME"
            git config user.email "$API_COMMIT_AUTHOR_EMAIL"
            git commit -m "$API_COMMIT_MESSAGE"
            printf '# pushing changes\n'
            git remote add deploy "https://${GITHUB_ACCESS_TOKEN}@github.com/$API_REPOSITORY_OWNER/$API_REPOSITORY_NAME.git"
            git push deploy "$API_PROCESS_BRANCH"
        fi
    fi
fi
