#!/usr/bin/php
<?php

include_once(__DIR__ . "/vendor/autoload.php");

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$qc = \Queuecat\QueueWriterFactory::create($dotenv);

$qc->run();