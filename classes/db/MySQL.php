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

    public function query(string $sql)
    {
        $rs = $this->link->query($sql);

        $rs1 = new ResultSet($rs);

        return $rs1;
    }
}

