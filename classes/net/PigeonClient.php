<?php
namespace net;

use Exception;

class PigeonClient implements PigeonResource
{
    private $id;
    private $so;
    private $create_time;
    private $update_time;
    private $recv_count;

    public function __construct(string $host)
    {
        $this->id = spl_object_id($this);
        $this->so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        $bool = socket_connect($this->so, $host, 9999);
        if ($bool === false)
        {
            throw new Exception('connection failed');
        }

        $this->recv_count = 0;
        $this->create_time = time();
        $this->update_time = $this->create_time;
    }

    public function id(): int 
    {
        return $this->id;
    }

    public function fd()
    {
        return $this->so;
    }

    public function onData(): void
    {
        // TODO
    }

    public function closed(): bool
    {
        // TODO
    }

    public function getRecvCount(): int
    {
        return $this->recv_count;
    }

    public function getCreateTime(): int
    {
        return $this->create_time;
    }

    public function getUpdateTime(): int
    {
        return $this->update_time;
    }
}

