<?php

namespace core {

    function has_file($path)
    {
        return file_exists(__DIR__ . $path);
    }

    function import($path)
    {
        require __DIR__ . $path;
    }
}


