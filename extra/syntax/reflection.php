<?php
// 
// 通过反射执行一个对象的私有方法
//

class Dog
{
    private function bark(): void
    {
        echo "Wang!" . PHP_EOL;
    }
}

$dog = new Dog;

// call to private method Dog::bark() from context ''
//$dog->bark();

$o = new ReflectionObject($dog);

$m = $o->getMethod('bark');

$m->setAccessible(true);
$m->invoke($dog);

// call to private method Dog::bark() from context ''
//$dog->bark();

