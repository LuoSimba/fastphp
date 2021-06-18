<?php
namespace net;

use Exception;

/**
 * 基于 TCP 的通信服务
 */
class Pigeon implements PigeonResource
{
    private $id;
    private $ip;
    private $port;
    private $so;

    // 自身同时也是一个容器
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
    }

    /**
     * 接受新的连接
     */
    public function onData()
    {
        // resource of type (Socket)
        $so = socket_accept($this->so);

        $data = new SockData($so);

        $this->add($data);
    }

    /**
     * container
     */
    private function add(SockData $data)
    {
        $id = $data->id();

        $this->objects  [ $id ] = $data;
        $this->resources[ $id ] = $data->fd();
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


    public function run()
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
            $readSet[ $this->id ] = $this->so;
            $writeSet  = null;
            $exceptSet = null;

            $n = socket_select($readSet, $writeSet, $exceptSet, null);

            $this->noticeAll($readSet);
        }
    }

    private function noticeAll(array $list)
    {
        foreach ($list as $id => $so)
        {
            if ($id === $this->id)
                $this->onData();
            else
                $this->notice($id);
        }
    }

    /**
     * 通知有新的数据到达
     *
     * 由服务统一读取数据，并通知各个连接
     */
    private function notice(int $id)
    {
        $data = $this->get($id);

        $fd = $data->fd();
        $buf = '';

        // 尝试读取最多 2048 字节
        $ret = @socket_recv($fd, $buf, 2048, 0);

        // 读取错误
        if ($ret === false)
        {
            //socket_last_error($fd);

            $this->del($id);

            $data->close();
            $data->onError();
        }
        // 发现远端关闭
        else if ($ret === 0)
        {
            $this->del($id);

            $data->close();
            $data->onClose();
        }
        // 新数据
        else 
        {
            $data->onData($buf);
        }
    }
}


