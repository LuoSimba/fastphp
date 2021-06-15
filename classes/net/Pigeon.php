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

        echo 'add client ' . $id . PHP_EOL;
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

        echo 'remote closed ' . $id . PHP_EOL;
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
     */
    private function notice(int $id)
    {
        $data = $this->get($id);

        $data->recv(); // XXX

        if ($data->closed())
        {
            $this->del($id);
        }

        $data->onData();
    }
}


