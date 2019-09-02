<?php


namespace Queuecat;

use Dotenv\Dotenv;
use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;


class SyslogQueueWriter implements QueueWriterInterface
{

    protected $metaData = [];

    public static function create(Dotenv $dotenv)
    {
        return new static();
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