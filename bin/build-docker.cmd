@echo off

docker-compose -f "%~dp0docker-compose.yml" run punic build.sh %*
