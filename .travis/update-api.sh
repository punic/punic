#!/usr/bin/env bash

set -o errexit
set -o nounset

cd "$( dirname "${BASH_SOURCE[0]}" )/../docs"

echo '### GENERATING NEW API DOCS'

composer --no-interaction run-script update-docs

cd ./themes/punic/static/api

echo '### CHECKING CHANGES'
if test -z "$(git status --porcelain .)"; then
    echo '- no changes detected'
    exit 0
fi
echo '- changes detected'

echo '### STAGING CHANGES'
git add --all .

echo '# COMMITTING CHANGES'
git config user.name 'TravisCI Bot'
git config user.email 'michele@locati.it'
git commit -m '[skip ci] Update APIs'

echo '# PUSHING CHANGES'
git remote add deploy "https://${GITHUB_ACCESS_TOKEN}@github.com/$TRAVIS_REPO_SLUG.git"
git push deploy "$(git rev-parse --abbrev-ref HEAD)"
