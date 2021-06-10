<?php

namespace http;

class Response
{
    private $content_type = null;

    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }

    public function outputHeaders()
    {
        if ($this->content_type)
            header('Content-Type: ' . $this->content_type);
    }
}

