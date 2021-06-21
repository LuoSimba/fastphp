<?php
namespace net;

use Exception;

/**
 * 监听服务端口，接收新的连接，并将新连接
 * 加入到容器统一维护
 */
final class PigeonServer extends PigeonResource
{
    private $ip;
    private $port;
    private $container = null;

    public function __construct()
    {
        $this->ip = '0.0.0.0';
        $this->port = 9999;

        $so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        $bool = @socket_bind($so, $this->ip, $this->port);
        // 地址绑定失败
        if ($bool === false || $bool === null)
        {
            //$errcode = socket_last_error($so);
            throw new Exception('socket bind error');
        }

        parent::__construct($so);
    }

    /**
     * 接受新的连接
     */
    final public function onData(): void
    {
        // resource of type (Socket)
        $so = socket_accept($this->fd());
        // statistics
        $this->update();
        $this->recvUp();

        // 将连接加入到容器维护
        if ($this->container)
        {
            $conn = new SockData($so);

            $this->container->add($conn);
        }
    }

    // TODO
    public function onError(): void
    {
    }

    // TODO
    public function onClose(): void
    {
    }

    public function setRelatedContainer(Pigeon $container)
    {
        if ($this->container !== null)
            throw new Exception('container can only be set once');

        $this->container = $container;
    }

    public function listen(): void
    {
        // 必须先设置一个关联的容器
        // 如果没有设置容器，那么新的连接到来的时候，
        // 由于没有其他的处理逻辑，新的连接就会被丢弃，
        // 这使得监听动作变得毫无意义
        if ($this->container === null)
            throw new Exception('set container first');

        $blisten = @socket_listen($this->fd(), 5);
        if ($blisten === false || $blisten === null)
        {
            //socket_last_error($this->fd());
            throw new Exception('socket listen error');
        }
    }
}

