#!/usr/bin/php
<?php

include_once(__DIR__ . "/vendor/autoload.php");

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$dotenv->required([
  'ALGM_AWS_ACCESS_KEY_ID',
  'ALGM_AWS_SECRET_ACCESS_KEY',
  'ALGM_AWS_REGION',
  'ALGM_AWS_QUEUE_URL',
]);



$qc = \Queuecat\Cli::create(getenv('ALGM_AWS_ACCESS_KEY_ID'),
  getenv('ALGM_AWS_SECRET_ACCESS_KEY'),
  getenv('ALGM_AWS_REGION'),
  getenv('ALGM_AWS_QUEUE_URL'));

$qc->run();