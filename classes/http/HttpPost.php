<?php
namespace http;

/**
 * usage:
 *
 * var a = new HttpGet;
 * a.referer(url2);
 * a.open(url).send();
 *
 * if (a.hasError())
 *    a.getErrorString();
 * else
 *    echo a.getContent();
 *
 */
class HttpPost extends HttpClient
{
    public function __construct()
    {
        parent::__construct();

        curl_setopt($this->handle(), CURLOPT_POST, true);

    }

    public function post(array $data): self
    {
        curl_setopt($this->handle(), CURLOPT_POSTFIELDS, $data);

        return $this;
    }

    public function getContent()
    {
        return parent::getContent();
    }
}

