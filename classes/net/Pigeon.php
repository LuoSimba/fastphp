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

    // 自身同时也是一个容器
    private $objects   = array();
    private $resources = array();

    public function __construct()
    {
        $this->ip = '0.0.0.0';
        $this->port = 9999;

        // resource of type (Socket)
        $this->so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_bind($this->so, $this->ip, $this->port);
    }

    /**
     * 接受新的连接
     */
    private function accept()
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
        socket_listen($this->so, 5);

        while (true)
        {
            $a = $this->resources;
            $a['server'] = $this->so;
            $b = null;
            $c = null;

            $n = socket_select($a, $b, $c, null);

            $this->noticeAll($a);
        }
    }

    private function noticeAll(array $list)
    {
        foreach ($list as $id => $so)
        {
            if ($id === 'server')
                $this->accept();
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
        //
        // 10054 - 远程主机强迫关闭了一个现有的连接
        $ret = socket_recv($fd, $buf, 2048, 0);

        if ($ret === false)
        {
            $data->close();

            $this->del($id);
        }
        // 发现远端关闭
        else if ($ret === 0)
        {
            $data->close();

            $this->del($id);
        }
        // 新数据
        else 
        {
            $data->onData($buf);
        }
    }
}


