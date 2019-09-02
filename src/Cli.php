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

    public function run()
    {
        $loop = Factory::create();
        $stdio = new Stdio($loop);
        $client = $this->awsClient;
        $queueUrl = $this->queueUrl;

        $stdio->on('data', function ($line) use ($stdio, $client, $queueUrl) {
            $line = rtrim($line, "\r\n");
            $stdio->write('Your input: ' . $line . PHP_EOL);


            $client->sendMessage([
              'QueueUrl' => $queueUrl,
              'MessageBody' => $line,
            ]);

            if ($line === 'quit') {
                $stdio->end();
            }
        });

        $loop->run();
    }
}