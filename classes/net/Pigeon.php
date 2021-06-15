<?php
namespace net;

/**
 * 基于 TCP 的通信服务
 */
class Pigeon 
{
    private $ip;
    private $port;
    private $so;

    public function __construct()
    {
        $this->ip = '0.0.0.0';
        $this->port = 9999;

        // resource of type (Socket)
        $this->so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_bind($this->so, $this->ip, $this->port);
    }
}


