<?php
namespace net;

use Exception;
use JsonException;

/**
 * 基于 TCP 的通信服务
 */
class SockData extends PigeonResource
{
    /**
     * 缓存
     *
     * 不能直接操作缓存。
     * 使用 onData() 来保存数据到缓存
     * 一旦缓存攒够一个消息包，就会通知 onMessage() 处理
     */
    private $buffer = '';

    /**
     * 当前缓存的内容大小
     */
    final public function contentSize()
    {
        return strlen($this->buffer);
    }

    /**
     * TODO
     */
    public function onError(): void
    {
    }

    /**
     * TODO
     */
    public function onClose(): void
    {
    }

    /**
     * 当新的数据到来时
     */
    final public function onData(): void
    {
        $buf = $this->recvData();

        if ($buf === false)
            return;

        // else 新数据 ...
        // 保存接收的数据
        $this->buffer .= $buf;

        $string = $this->pickNewPackage();
        if ($string === null)
            return;


        try {

            $msg = json_decode($string, false, 512, JSON_THROW_ON_ERROR);

            $this->onMessage($msg);

        } catch (JsonException $e) {

            // 如果发现客户端传来的数据无法 JSON 解码，则认为
            // 客户端存在严重的问题，应当立即终止通讯
            $this->close();

            $this->onError();

        } catch (Exception $e) {

            $this->close();

            $this->onError();
        }
    }

    final private function pickNewPackage()
    {
        // 是否已经收到一个完整头部
        if ($this->contentSize() < 4)
            return null;

        $header = unpack('N', $this->buffer);

        // 得到主体的大小
        $size = $header[1];

        // 是否收到一个完整的包
        if ($this->contentSize() < $size + 4)
            return null;

        $string = substr($this->buffer, 4, $size);
        // 将整个包移出缓存
        $this->buffer = substr($this->buffer, 4 + $size);
        $this->recvUp();

        // 这个包没有主体，无需处理
        if ($size === 0)
            return null;

        return $string;
    }

    /**
     * 当消息到来时 (sample)
     */
    public function onMessage(object $msg)
    {
        // 返回一个消息
        $o = new PigeonMessage;
        $o->msg = "hello java world.";

        $this->sendMessage($o);
    }
}

// 如果改用 HTTP/1.0
// 将 Connection: close
// 那么在同时进行两次请求的时候会非常慢
