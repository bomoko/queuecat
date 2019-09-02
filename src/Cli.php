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

    public static function create($key, $secret, $region)
    {
        //let's try set up an AWS queue connection ...
        $client = SqsClient::factory([
          'key' => $key,
          'secret' => $secret,
          'region' => $region,
        ]);

        return new static($client);
    }

    protected function __construct($awsClient)
    {
        $this->awsClient = $awsClient;
    }

    public function run()
    {
        $loop = Factory::create();
        $stdio = new Stdio($loop);


        $stdio->on('data', function ($line) use ($stdio, $client) {
            $line = rtrim($line, "\r\n");
            $stdio->write('Your input: ' . $line . PHP_EOL);


            $client->sendMessage([
              'QueueUrl' => 'https://sqs.us-east-1.amazonaws.com/152002996554/queuecat_test',
              'MessageBody' => $line,
            ]);

            if ($line === 'quit') {
                $stdio->end();
            }
        });

        $loop->run();
    }
}