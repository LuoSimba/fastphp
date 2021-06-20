<?php

function foo(): string
{
    //die;
    return null;  // 执行到此句才报错，
                  // 如果上句 die 没有注释掉，那么就不会报错
}


// 在 foo() 函数定义的时候，没有报错，
// 执行的时候才报错
// return value of foo() must be of the type string, null returned.
foo();

