#!/bin/sh

docker-compose -f "$(dirname -- "$0")/docker-compose.yml" run --rm punic build.sh $@
