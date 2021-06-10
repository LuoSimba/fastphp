<?php

namespace debug {

    function dump($var)
    {
        var_dump($var);
        die;
    }

    /**
     * 16 进制显示字符
     */
    function hexdump(string $str)
    {
        $list = str_split($str, 8);

        $groups = array();

        foreach ($list as $line)
        {
            $content = array();

            for ($i = 0; $i < strlen($line); $i ++)
            {
                $content[] = sprintf('%02X', ord($line[$i]));
            }

            $groups[] = implode(' ', $content);
        }

        while ($groups)
        {
            $a = array_shift($groups);
            $b = array_shift($groups);

            echo $a . ($b ? ' - ' . $b : '') . PHP_EOL;
        }
    }
}


