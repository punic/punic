#!/bin/sh

docker-compose -f "$(dirname -- "$0")/docker-compose.yml" run punic build.sh $@
