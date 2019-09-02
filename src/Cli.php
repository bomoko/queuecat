<?php

namespace Queuecat;

use Aws\Sqs\SqsClient;
use Dotenv\Dotenv;
use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;
use Aws\Common\Aws;


class Cli
{

    protected $awsClient = null;

    protected $queueUrl = null;

    protected $metaData = [];

    public static function create($key, $secret, $region, $queueUrl)
    {
        //let's try set up an AWS queue connection ...
        $client = SqsClient::factory([
          'key' => $key,
          'secret' => $secret,
          'region' => $region,
        ]);
        return new static($client, $queueUrl);
    }

    public function __construct($awsClient, $queueUrl)
    {
        $this->awsClient = $awsClient;
        $this->queueUrl = $queueUrl;
    }


    public function setMetaDataEntry($key, $value)
    {
        $this->metaData[$key] = $value;
    }

    public function getMetaDataEntry($key)
    {
        return $this->metaData[$key];
    }

    private function generateMessage($data)
    {
        //wrap this up in some kind of useful object
        $message = [
          'meta' => $this->metaData,
          'timestamp' => time(),
          'data' => $data,
        ];

        return json_encode($message);
    }

    public function run()
    {
        $loop = Factory::create();
        $stdio = new Stdio($loop);
        $client = $this->awsClient;
        $queueUrl = $this->queueUrl;

        $stdio->on('data', function ($line) use ($stdio, $client, $queueUrl) {
            $line = rtrim($line, "\r\n");
            $stdio->write($line . PHP_EOL);

            if ($line === 'quit') {
                $stdio->end();
                return;
            }

            $client->sendMessage([
              'QueueUrl' => $queueUrl,
              'MessageBody' => $this->generateMessage($line),
            ]);
        });

        $loop->run();
    }
}