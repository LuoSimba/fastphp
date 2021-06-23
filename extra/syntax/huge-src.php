<?php
// 
// 一个非常大的源代码(110M)可以直接让 PHP 用尽 512M 内存而报错
//


class Dog
{
    public function foo()
    {
        // repeat 6 x 1,000,000 times
        echo 1 . PHP_EOL;
        echo 1 . PHP_EOL;
        echo 1 . PHP_EOL;
        // ...
    }
}


