<?php
namespace net;

use Exception;

/**
 * 基于 TCP 的通信服务
 *
 * 容器负责维护各个连接，监视连接是否有新的数据
 * 到来，然后通知各个连接自行获取。容器本身并不
 * 亲自执行 socket 连接的读写。
 */
final class Pigeon implements PigeonContainer
{
    private $server = null;

    private $objects   = array();
    private $resources = array();

    public function __construct()
    {
        $server = new PigeonServer;
        $server->setRelatedContainer($this);

        $this->server = $server;
        $this->add($server);
    }

    /**
     * 将一个 socket 资源包装成一个具体的对象，并
     * 放入容器中
     */
    public function add(PigeonResource $conn): void
    {
        $id = $conn->id();

        $this->objects  [ $id ] = $conn;
        $this->resources[ $id ] = $conn->fd();
    }

    public function get(int $id): PigeonResource
    {
        return $this->objects[ $id ];
    }

    public function del(int $id): void
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


