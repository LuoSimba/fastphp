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
final class HttpPost extends HttpClient
{
    public function __construct()
    {
        parent::__construct();

        curl_setopt($this->handle(), CURLOPT_POST, true);

    }

    public function sendJSON(array $data)
    {
        $this->header('Content-Type', 'application/json');

        $json = json_encode($data);
        curl_setopt($this->handle(), CURLOPT_POSTFIELDS, $json);

        $this->send();
    }
}

