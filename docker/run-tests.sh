#!/usr/bin/env bash

docker exec -d humphries bash cd app && vendor/bin/phpunit
