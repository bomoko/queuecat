<?php

namespace Queuecat;

use Aws\Sqs\SqsClient;
use Dotenv\Dotenv;
use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;
use Aws\Common\Aws;


class AwsQueueWriter implements QueueWriterInterface
{
    use QueueWriterBaseTrait;

    protected $awsClient = null;

    protected $queueUrl = null;

    protected $metaData = [];


    public static function create(Dotenv $dotenv)
    {
        try {
            $dotenv->required([
              'ALGM_AWS_ACCESS_KEY_ID',
              'ALGM_AWS_SECRET_ACCESS_KEY',
              'ALGM_AWS_REGION',
              'ALGM_AWS_QUEUE_URL',
            ]);

            //let's try set up an AWS queue connection ...
            $client = SqsClient::factory([
              'key' => getenv('ALGM_AWS_ACCESS_KEY_ID'),
              'secret' => getenv('ALGM_AWS_SECRET_ACCESS_KEY'),
              'region' => getenv('ALGM_AWS_REGION'),
            ]);

            return new static($client, getenv('ALGM_AWS_QUEUE_URL'));
        } catch (\Exception $exception) {
            return false;
        }
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