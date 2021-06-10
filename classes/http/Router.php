<?php
namespace http;

class Router
{
    private $map = array();

    public function set($k, $v)
    {
        $this->map[ $k ] = $v;
    }

    public function get($k)
    {
        // 寻找处理者
        if (!array_key_exists($k, $this->map))
            return null;

        $c = $this->map[ $k ];

        return $c;
    }
}

