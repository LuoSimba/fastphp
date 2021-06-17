<?php
namespace net;

use JsonException;
use stdClass;

/**
 * 基于 TCP 的通信服务
 */
class SockData
{
    private $id;
    private $so;

    /**
     * 缓存
     *
     * 不能直接操作缓存。
     * 使用 onData() 来保存数据到缓存
     * 一旦缓存攒够一个消息包，就会通知 onMessage() 处理
     */
    private $buffer = '';

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

    /**
     * 当前缓存的内容大小
     */
    final public function contentSize()
    {
        return strlen($this->buffer);
    }

    public function onError()
    {
        // TODO
    }

    /**
     * 当新的数据到来时
     */
    final public function onData(string $buf)
    {
        // 保存接收的数据
        $this->buffer     .= $buf;
        $this->update_time = time();

        // 是否收到一个头部
        if ($this->contentSize() < 4)
            return;

        $header = unpack('N', $this->buffer);
        // 得到主体的大小
        $size = $header[1];

        // 是否收到一个完整的包
        if ($this->contentSize() < $size + 4)
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

    /**
     * 当消息到来时 (sample)
     */
    public function onMessage(object $msg)
    {
        
        // 返回一个消息
        $o = new stdClass;
        $o->msg = "hello java world.";
        $this->sendMessage($o);
    }


    /**
     * 发送一则消息
     */
    private function sendMessage(object $o)
    {
        if ($this->closed())
            return;

        $letter = json_encode($o);

        $size = strlen($letter);

        $header = pack('N', $size);

        socket_send($this->so, $header, strlen($header), 0);
        socket_send($this->so, $letter, strlen($letter), 0);
    }
}

// 如果改用 HTTP/1.0
// 将 Connection: close
// 那么在同时进行两次请求的时候会非常慢
