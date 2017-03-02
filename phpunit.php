<?php

require_once "vendor/autoload.php";

ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
putenv('APP_ENV=testing');

(new \Library\Application())
    ->registerProvider(
        \App\Provider\Mvc::class,
        \App\Provider\Cli::class
    )
    ->loadCore()
    ->loadProviders();