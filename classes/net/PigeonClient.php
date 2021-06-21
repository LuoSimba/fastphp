<?php
namespace net;

use Exception;

class PigeonClient extends PigeonResource
{
    public function __construct(string $host)
    {
        $so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $bool = @socket_connect($so, $host, 9999);
        if ($bool === false)
        {
            throw new Exception('connection failed');
        }

        parent::__construct($so);
    }

    public function onData(): void
    {
        // TODO
    }

    public function onError(): void
    {
        // TODO
    }

    public function onClose(): void
    {
        // TODO
    }
}

