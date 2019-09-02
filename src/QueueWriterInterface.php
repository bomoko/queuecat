<?php

namespace Queuecat;

use Dotenv\Dotenv;

interface QueueWriterInterface
{

    public static function create(Dotenv $dotenv);

    public function setMetaDataEntry($key, $value);

    public function getMetaDataEntry($key);

    public function run();
}