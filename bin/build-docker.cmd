@echo off

docker-compose -f "%~dp0docker-compose.yml" run punic sed -i -e 's/\r//g' /punic/bin/build.sh

docker-compose -f "%~dp0docker-compose.yml" run punic build.sh %*
