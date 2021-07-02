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
final class HttpGet extends HttpClient
{
    public function __construct()
    {
        parent::__construct();
    }
}

