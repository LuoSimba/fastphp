<?php
// 
// PHP 与 JAVA 的不同点：PHP 垃圾回收是立即的
//

class Dog 
{
    public function __construct()
    {
        echo 'CREATE' . PHP_EOL;
    }

    public function __destruct()
    {
        echo 'DESTROY' . PHP_EOL;
    }
}

$dog = new Dog;

//unset($dog);
// or:
//$dog = 1;

echo 'bye' . PHP_EOL;

