#!/usr/bin/env bash
cd /app && composer update && php console migration:migrate && php console generator:permission

php-fpm
