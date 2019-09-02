<?php


namespace Queuecat;

use Dotenv\Dotenv;
use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;


class SyslogQueueWriter implements QueueWriterInterface
{
    use QueueWriterBaseTrait;

    protected $metaData = [];

    public static function create(Dotenv $dotenv)
    {
        return new static();
    }

    public function run()
    {
        $loop = Factory::create();
        $stdio = new Stdio($loop);

        $stdio->on('data', function ($line) use ($stdio) {
            $line = rtrim($line, "\r\n");
            $stdio->write($line . PHP_EOL);

            if ($line === 'quit') {
                $stdio->end();
                return;
            }

            syslog(LOG_INFO, $this->generateMessage($line));
        });

        $loop->run();
    }
}