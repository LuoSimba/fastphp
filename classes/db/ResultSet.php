<?php
namespace db;

class ResultSet 
{

    private $rs;

    public function __construct(\mysqli_result $rs)
    {
        $this->rs = $rs;

    }

    /**
     * 获取结果集记录条数
     */
    public function size()
    {
        return $this->rs->num_rows;
    }

    /**
     * 结果是否为空
     */
    public function isEmpty()
    {
        return $this->rs->num_rows === 0;
    }

    /**
     * 作为标量
     */
    public function toScalar()
    {
        $b = $this->rs->data_seek(0);

        if ($b === false)
            return null;

        if ($this->rs->field_count < 1)
            return null;

        $row = $this->rs->fetch_row();

        return $row[0];
    }

    /**
     * 释放资源
     */
    public function free()
    {
        $this->rs->free();
    }

    //$rs->fetch_assoc();
}

