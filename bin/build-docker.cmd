@echo off

docker-compose -f "%~dp0docker-compose.yml" run punic dos2unix /punic/bin/build.sh

docker-compose -f "%~dp0docker-compose.yml" run punic build.sh %*
