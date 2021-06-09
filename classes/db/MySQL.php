<?php
namespace db;

class MySQL
{

    private $link;

    public function __construct()
    {
        $this->link = mysqli_init();
    }

    public function connect(string $host, string $username, string $password = null)
    {
        $b = $this->link->real_connect(
            $host, 
            $username,
            $password,
            null,
            3306);
    }

    public function select_db(string $dbname)
    {
        $b = $this->link->select_db($dbname);

        if ($b === false)
            $this->_raise_error();
    }

    public function query(string $sql)
    {
        $rs = $this->link->query($sql);

        // 查询失败时返回 false
        if ($rs === false)
            $this->_raise_error();
        else if ($rs === true)
            return true;

        $rs1 = new ResultSet($rs);

        return $rs1;
    }

    private function _raise_error()
    {
        throw new \Exception('MySQL: ' . $this->link->error . ', ' . $this->link->errno);
    }

    public function beginTransaction()
    {
        $b = $this->link->begin_transaction();
        if ($b === false)
            $this->_raise_error();
    }

    public function commit()
    {
        if (!$this->link->commit())
            $this->_raise_error();
    }

    public function rollback()
    {
        if (!$this->link->rollback())
            $this->_raise_error();
    }
}

