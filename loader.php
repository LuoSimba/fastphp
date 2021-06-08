<?php

namespace myloader {

    function loader($class) 
    {
        $data = explode('\\', $class);

        $str = implode('/', $data);

        $file = '/classes/' . $str . '.php';

        if (\core\has_file($file))
            \core\import($file);
    }

    spl_autoload_register(
        __NAMESPACE__ . '\\loader', true, false);
}

