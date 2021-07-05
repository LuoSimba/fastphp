<?php
namespace http;

/**
 */
abstract class HttpClient
{
    private $ch;
    private $content;
    private $headers = array();

    protected function handle()
    {
        return $this->ch;
    }

    public function __construct()
    {
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true);
    }

    public function referer(string $url): self
    {
        curl_setopt($this->ch, CURLOPT_REFERER, $url);
        return $this;
    }

    public function header(string $field, string $value): self
    {
        $this->headers[] = "$field: $value";
        return $this;
    }

    public function open(string $url): self
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);

        return $this;
    }

    public function send()
    {
        // 设置 HTTP 头字段的数组
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);

        $this->content = curl_exec($this->ch);
    }

    public function hasError(): bool
    {
        // 在没有错误发生时返回 0
        return curl_errno($this->ch) !== 0;
    }

    /**
     * 没有执行的时候返回 ""
     */
    public function getErrorString(): string
    {
        return curl_error($this->ch);
    }

    public function getContent()
    {
        return $this->content;
    }
}

