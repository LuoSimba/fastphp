<?php
namespace net;

use Exception;
use JsonException;

/**
 * 基于 TCP 的通信服务
 */
class SockData implements PigeonResource
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
    private $recv_count;

    private $closed = false;




    public function __construct($so)
    {
        // get_resource_id require PHP8
        $this->id = spl_object_id($this);

        $this->so = $so;

        $this->create_time = time();
        $this->update_time = $this->create_time;
        $this->recv_count = 0;
    }

    final public function id(): int
    {
        return $this->id;
    }

    /**
     * 是否关闭
     */
    final public function closed(): bool
    {
        return $this->closed;
    }

    /**
     * 返回收到的消息数
     */
    final public function getRecvCount(): int { return $this->recv_count; }

    final public function getCreateTime(): int { return $this->create_time; }
    final public function getUpdateTime(): int { return $this->update_time; }

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

    /**
     * TODO
     */
    public function onError()
    {
    }

    /**
     * TODO
     */
    public function onClose()
    {
    }

    /**
     * 当新的数据到来时
     */
    final public function onData(): void
    {
        // 自身主动读取
        $this->update_time = time();


        // 尝试读取最多 2048 字节
        $buf = '';

        $readSize = @socket_recv($this->so, $buf, 2048, 0);

        // 读取错误
        if ($readSize === false)
        {
            // socket_last_error($this->so);
            $this->close();

            $this->onError();
            return;
        }
        // 发现远端关闭
        else if ($readSize === 0)
        {
            $this->close();

            $this->onClose();
            return;
        }
        

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

    final function pickNewPackage(): string
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
        $this->recv_count ++;

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


    /**
     * 发送一则消息
     */
    final private function sendMessage(PigeonMessage $o)
    {
        if ($this->closed())
            throw new Exception('socket already closed');

        $msg = json_encode($o);
        $size = strlen($msg);

        $text = pack('N', $size) . $msg;
        $writeSize = @socket_send($this->so, $text, strlen($text), 0);

        // 写入错误
        if ($writeSize === false)
        {
            throw new Exception('socket write error');
        }
    }
}

// 如果改用 HTTP/1.0
// 将 Connection: close
// 那么在同时进行两次请求的时候会非常慢
