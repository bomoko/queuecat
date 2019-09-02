<?php

namespace Queuecat;


trait QueueWriterBaseTrait
{

    public function setMetaDataEntry($key, $value)
    {
        $this->metaData[$key] = $value;
    }

    public function getMetaDataEntry($key)
    {
        return $this->metaData[$key];
    }

    protected function generateMessage($data)
    {
        //wrap this up in some kind of useful object
        $message = [
          'meta' => $this->metaData,
          'timestamp' => time(),
          'data' => $data,
        ];

        return json_encode($message);
    }

}