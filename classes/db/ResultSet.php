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
     * 获取第一列的所有数据
     */
    public function getFirstColumn()
    {
        if ($this->rs->field_count < 1)
            return null;

        $b = $this->rs->data_seek(0);
        if ($b === false)
            return null;

        $list = array();

        while ($row = $this->rs->fetch_row())
        {
            $list[] = $row[0];
        }

        return $list;
    }


    // 无需通过 free() 来释放资源
    // 当对象没有被引用时，对象（包括资源）
    // 占用的空间会全部被释放
    //$this->rs->free();
    //
    // 这个可以很容易测试得到结果：
    //
    // repeat 10000 times do:
    //   var set = mysql.query('select * from t1 join t2 join t3 join ...')
    //   echo memory_get_usage()
    //
    // 我们从数据库中查出很多表连接的笛卡尔积，
    // 这个结果往往非常大，缓存在 set 变量中，
    // 只要我们每次都丢弃这个变量，内存占用并
    // 不会越来越高

    //$rs->fetch_assoc();
}

