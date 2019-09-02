<?php

namespace Queuecat;


class QueueWriterFactory
{

    const REGISTERED_WRITERS = [
      AwsQueueWriter::class,
      SyslogQueueWriter::class,
    ];

    public static function create($dotenv)
    {
        foreach (static::REGISTERED_WRITERS as $WRITER) {
            $queue = ($WRITER)::create($dotenv);
            if ($queue) {
                return $queue;
            }
        }
        throw new \Exception("COULD NOT LOAD ANY QUEUEWRITER");
    }

}