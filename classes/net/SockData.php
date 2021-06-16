<?php
namespace net;

use JsonException;

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
        // get_resource_id require PHP8
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

    final public function bufferSize()
    {
        return strlen($this->buffer);
    }

    final public function onData(string $buf)
    {
        // 保存接收的数据
        $this->buffer .= $buf;

        // 是否收到一个头部
        if ($this->bufferSize() < 4)
            return;

        $header = unpack('N', $this->buffer);
        // 得到主体的大小
        $size = $header[1];

        // 是否收到一个完整的包
        if ($this->bufferSize() < $size + 4)
            return;
        $string = substr($this->buffer, 4, $size);

        // 将整个包移出缓存
        $this->buffer = substr($this->buffer, $size + 4);

        // 这个包没有主体, 无需处理
        if ($size === 0)
            return;

        try {

            $msg = json_decode($string, false, 512, JSON_THROW_ON_ERROR);

            $this->onMessage($msg);

        } catch (JsonException $e) {

            // XXX 暂时忽略 JSON 解析的错误
            echo 'json decode error ...' . $this->id() . PHP_EOL;
            //throw $e;
        }
    }

    public function onMessage(object $msg)
    {
        if ($this->closed())
            return;

        $o = new \stdClass;

        $o->msg = "hello java world.\r\n";

        $this->sendPackage($o);
    }

    /**
     * 发送数据
     */
    private function send($str)
    {
        socket_send($this->so, $str, strlen($str), 0);
    }

    private function sendPackage(object $o)
    {
        $letter = json_encode($o);

        $size = strlen($letter);

        $header = pack('N', $size);

        $this->send($header);
        $this->send($letter);
    }
}

// 如果改用 HTTP/1.0
// 将 Connection: close
// 那么在同时进行两次请求的时候会非常慢
