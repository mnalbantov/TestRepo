<?php
namespace Payment\Gateway\Igfs;

class ReadWriteException extends IOException
{
    public function __construct($url, $message)
    {
        parent::__construct("[" . $url . "] " . $message);
    }
}
