<?php
namespace net;

use Exception;

/**
 * 基于 TCP 的通信服务
 */
final class Pigeon implements PigeonResource
{
    // as a resource:
    private $id;
    private $ip;
    private $port;
    private $so;

    // as a container:
    private $objects   = array();
    private $resources = array();

    public function __construct()
    {
        $this->id = spl_object_id($this);
        $this->ip = '0.0.0.0';
        $this->port = 9999;

        // resource of type (Socket)
        $this->so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        $bool = @socket_bind($this->so, $this->ip, $this->port);
        // 地址绑定失败
        if ($bool === false)
        {
            $errcode = socket_last_error($this->so);
            throw new Exception('socket bind error');
        }

        $this->add($this, $this->so);
    }

    final public function id(): int
    {
        return $this->id;
    }

    /**
     * 不可关闭
     */
    final public function closed(): bool
    {
        return false;
    }

    /**
     * 接受新的连接
     */
    final public function onData(): void
    {
        // resource of type (Socket)
        $so = socket_accept($this->so);

        $data = new SockData($so);

        $this->add($data, $so);
    }

    /**
     * container
     */
    private function add(PigeonResource $data,  $so)
    {
        $id = $data->id();

        $this->objects  [ $id ] = $data;
        $this->resources[ $id ] = $so;
    }

    /**
     * container
     */
    private function get(int $id)
    {
        return $this->objects[ $id ];
    }

    /**
     * container
     */
    private function del(int $id)
    {
        unset($this->objects  [ $id ]);
        unset($this->resources[ $id ]);
    }


    final public function run()
    {
        $blisten = @socket_listen($this->so, 5);
        if ($blisten === false)
        {
            //socket_last_error($this->so);
            throw new Exception('socket listen error');
        }

        while (true)
        {
            $readSet   = $this->resources;
            $writeSet  = null;
            $exceptSet = null;

            $n = socket_select($readSet, $writeSet, $exceptSet, null);

            $this->notice($readSet);
        }
    }

    /**
     * 通知有新的数据到达
     */
    private function notice(array $list): void
    {
        foreach ($list as $id => $so)
        {
            $data = $this->get($id);

            $data->onData();

            if ($data->closed())
                $this->del($id);
        }
    }
}


