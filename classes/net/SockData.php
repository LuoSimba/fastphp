<?php
namespace net;

class SockData
{
    private $id;
    private $so;

    private $data = '';

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

    public function id()
    {
        return $this->id;
    }

    public function recv()
    {
        $buf = '';

        // 尝试读取最多 2048 字节
        $ret = socket_recv($this->so, $buf, 2048, 0);

        if ($ret === false)
        {
            $this->close();
            return;
        }

        // 发现远端关闭
        if ($ret === 0)
        {
            $this->close();
            return;
        }
        
        // 保存接收的数据
        $this->data .= $buf;
    }

    /**
     * 是否关闭
     */
    public function closed()
    {
        return $this->closed;
    }

    /**
     * 关闭 socket 连接
     */
    private function close()
    {
        socket_close( $this->so );
        //socket_shutdown($msgso);

        $this->closed = true;
    }

    /**
     * 返回一个默认的响应
     */
    public function resp()
    {
        $content = "hello java world\n";
        $ContentLength = strlen($content);

        // 如果改用 HTTP/1.0
        // 将 Connection: close
        // 那么在同时进行两次请求的时候
        // 会非常慢
        $header = "HTTP/1.1 200 OK\r\n"
            . "Content-Length: $ContentLength\r\n"
            . "\r\n";

        // send header
        socket_send($this->so, $header, strlen($header), 0);

        // send content
        socket_send($this->so, $content, $ContentLength, 0);
    }
}

