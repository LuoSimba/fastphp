<?php
namespace net;

/**
 * 基于 TCP 的通信服务
 */
class SockData
{
    private $id;
    private $so;

    protected $buffer = '';

    private $create_time;
    private $update_time;

    private $closed = false;

    public function __construct($so)
    {
        $this->id = spl_object_id($this);

        $this->so = $so;

        $this->create_time = time();
        $this->update_time = $this->create_time;
    }

    final public function id()
    {
        return $this->id;
    }

    final public function fd()
    {
        return $this->so;
    }

    /**
     * 是否关闭
     */
    final public function closed()
    {
        return $this->closed;
    }

    /**
     * 关闭 socket 连接
     */
    final public function close()
    {
        socket_close( $this->so );
        //socket_shutdown($msgso);

        $this->closed = true;
    }

    final public function onData(string $buf)
    {
        // 保存接收的数据
        $this->buffer .= $buf;

        //$this->onMessage();
    }

    public function onMessage()
    {
        if ($this->closed())
            return;

        $content = $this->buffer;
        $ContentLength = strlen($content);

        // 如果改用 HTTP/1.0
        // 将 Connection: close
        // 那么在同时进行两次请求的时候
        // 会非常慢
        $header = "HTTP/1.1 200 OK\r\n"
            . "Content-Type: text/plain\r\n"
            . "Content-Length: $ContentLength\r\n"
            . "\r\n";

        $this->send($header);
        $this->send($content);
    }

    /**
     * 发送数据
     */
    protected function send($str)
    {
        socket_send($this->so, $str, strlen($str), 0);
    }
}

