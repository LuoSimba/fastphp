<?php
namespace net;

use Exception;

/**
 * 监听服务端口，接收新的连接，并将新连接
 * 加入到容器统一维护
 */
final class PigeonServer implements PigeonResource
{
    private $id;
    private $ip;
    private $port;
    private $so;

    private $recv_count;
    private $create_time;
    private $update_time;

    private $container = null;

    public function __construct()
    {
        $this->id = spl_object_id($this);
        $this->ip = '0.0.0.0';
        $this->port = 9999;
        $this->recv_count = 0;
        $this->create_time = time();
        $this->update_time = $this->create_time;

        // resource of type (Socket)
        $this->so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        $bool = @socket_bind($this->so, $this->ip, $this->port);
        // 地址绑定失败
        if ($bool === false)
        {
            //$errcode = socket_last_error($this->so);
            throw new Exception('socket bind error');
        }
    }

    final public function id(): int {
        return $this->id;
    }

    /**
     * 接受新的连接
     */
    final public function onData(): void
    {
        // resource of type (Socket)
        $so = socket_accept($this->so);
        // statistics
        $this->update_time = time();
        $this->recv_count ++;


        $data = new SockData($so);

        // 将连接加入到容器维护
        if ($this->container)
        {
            $this->container->add($data, $so);
        }
    }

    /**
     * 返回收到的连接数
     */
    final public function getRecvCount(): int {
        return $this->recv_count;
    }

    final public function getCreateTime(): int {
        return $this->create_time;
    }

    final public function getUpdateTime(): int {
        return $this->update_time;
    }

    /**
     * 不可关闭
     *
     * XXX 应该是可以关闭的
     */
    final public function closed(): bool {
        return false;
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

        $blisten = @socket_listen($this->so, 5);

        if ($blisten === false)
        {
            //socket_last_error($this->so);
            throw new Exception('socket listen error');
        }
    }

    public function fd()
    {
        return $this->so;
    }
}

