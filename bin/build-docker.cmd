@echo off

docker-compose -f "%~dp0docker-compose.yml" run --rm punic dos2unix /punic/bin/build.sh

docker-compose -f "%~dp0docker-compose.yml" run --rm punic build.sh %*
