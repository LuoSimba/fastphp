<?php
namespace net;

use Exception;

abstract class PigeonResource 
{
    private $so;
    private $create_time;
    private $update_time;
    private $recv_count;
    private $closed;

    protected function __construct($so)
    {
        // 'so' === resource of type (Socket)
        $type = get_resource_type($so);
        if ($type !== "Socket")
            throw new Exception('need type(Socket)');

        $this->so = $so;

        $this->recv_count = 0;
        $this->create_time = time();
        $this->update_time = $this->create_time;
        $this->closed = false;
    }

    final public function id(): int
    {
        return (int)$this->so;
    }

    abstract function onData(): void;
    abstract function onError(): void;
    abstract function onClose(): void;

    /**
     * 是否关闭
     */
    final public function closed(): bool
    {
        return $this->closed;
    }

    /**
     * 关闭 socket 连接
     */
    final public function close(): void
    {
        socket_close( $this->so );
        // socket_shutdown($this->so);

        $this->closed = true;
    }

    /**
     * 返回收到的消息数
     */
    final public function getRecvCount(): int
    {
        return $this->recv_count;
    }

    final public function getCreateTime(): int
    {
        return $this->create_time;
    }

    final public function getUpdateTime(): int
    {
        return $this->update_time;
    }

    final public function fd()
    {
        return $this->so;
    }


    // --------------
    final protected function update()
    {
        $this->update_time = time();
    }

    final protected function recvUp()
    {
        $this->recv_count ++;
    }

    /**
     * 发送一则消息
     */
    final public function sendMessage(PigeonMessage $msg)
    {
        //if ($this->closed())
        //    throw new Exception('socket already closed');


        $text = json_encode($msg);
        $size = strlen($text);

        $bin = pack('N', $size) . $text;

        $writeSize = @socket_send($this->so, $bin, strlen($bin), 0);

        // 写入错误
        if ($writeSize === false)
        {
            throw new Exception('socket write error');
        }
    }

    final public function recvData()
    {
        // 尝试读取最多 2048 字节
        $buf = '';

        $readSize = @socket_recv($this->so, $buf, 2048, 0);
        $this->update();

        // 读取错误
        if ($readSize === false)
        {
            // socket_last_error($this->so);
            $this->close();

            $this->onError();
            return false;
        }
        // 发现远端关闭
        else if ($readSize === 0)
        {
            $this->close();

            $this->onClose();
            return false;
        }

        return $buf;
    }
}

