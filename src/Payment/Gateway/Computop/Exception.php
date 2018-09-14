<?php
namespace Payment\Gateway\Computop;


class CmptpException extends \Exception
{
}
class CmptpMissingParException extends \Exception
{
}

class IOException extends \Exception
{
}
class ConnectionException extends IOException
{
    public function __construct($url, $message)
    {
        parent::__construct("[" . $url . "] " . $message);
    }
}
class ReadWriteException extends IOException
{
    public function __construct($url, $message)
    {
        parent::__construct("[" . $url . "] " . $message);
    }
}