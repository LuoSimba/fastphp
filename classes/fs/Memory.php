<?php
namespace fs;

class Memory
{
    private $fp;

    public function __construct()
    {
        // open
        // resource of type (stream)
        $this->fp = fopen("php://memory", "w+b");
    }

    /**
     * 返回文件大小
     */
    public function size()
    {
        $stat = fstat($this->fp);
        return $stat['size'];
    }

    /**
     * 相当于 $this->seek(0)
     */
    public function rewind()
    {
        rewind($this->fp);
    }

    /**
     * 截短内容到指定长度
     */
    public function truncate(int $size)
    {
        ftruncate($this->fp, $size);
    }

    public function write(string $data)
    {
        fwrite($this->fp, $data);
    }

    public function close()
    {
        fclose($this->fp);
    }

    public function seek(int $n)
    {
        fseek($this->fp, $n, SEEK_SET);
    }

    public function isEnd()
    {
        return feof($this->fp);
    }

    public function read(int $size)
    {
        return fread($this->fp, $size);
    }

    /**
     * 全部输出
     */
    public function dump()
    {
        $this->rewind();
        fpassthru($this->fp);
    }
}

