<?php
namespace net;

use Exception;

/**
 * 基于 TCP 的通信服务
 *
 * 容器类
 *
 * 容器负责维护各个连接，监视连接是否有新的数据
 * 到来，然后通知各个连接自行获取。容器本身并不
 * 亲自执行 socket 连接的读写。
 */
final class Pigeon
{
    private $server = null;

    private $objects   = array();
    private $resources = array();

    public function __construct()
    {
        $server = new PigeonServer;
        $server->setRelatedContainer($this);

        $this->server = $server;
        $this->add($server, $server->fd());
    }

    /**
     * 将一个 socket 资源包装成一个具体的对象，并
     * 放入容器中
     */
    private function add(PigeonResource $data, $so): void
    {
        $id = $data->id();

        $this->objects  [ $id ] = $data;
        $this->resources[ $id ] = $so;
    }

    public function addSocket($so): void
    {
        $type = get_resource_type($so);
        if ($type !== "Socket")
            throw new Exception('need type(Socket)');

        $data = new SockData($so);

        $this->add($data, $so);
    }

    private function get(int $id)
    {
        return $this->objects[ $id ];
    }

    private function del(int $id)
    {
        unset($this->objects  [ $id ]);
        unset($this->resources[ $id ]);
    }

    final public function run()
    {
        $this->server->listen();

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

    /**
     * 打印信息
     */
    final public function debug()
    {
        foreach ($this->objects as $id => $obj) 
        {
            echo '-------' . PHP_EOL;
            echo 'id=' . $id . PHP_EOL;
            echo 'create time=' . date('Y-m-d H:i:s', $obj->getCreateTime()) . PHP_EOL;
            echo 'update time=' . date('Y-m-d H:i:s', $obj->getUpdateTime()) . PHP_EOL;
            echo 'recv count=' . $obj->getRecvCount() . PHP_EOL;
        }
    }
}


