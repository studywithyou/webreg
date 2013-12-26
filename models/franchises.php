<?php
class Franchise 
{
    protected $_db;

    public function __construct($db)
    {
        $this->_db = $db;
    }

    public function getAll()
    {
        $sql = "
            SELECT *
            FROM franchises
            ORDER BY nickname
        ";
        $franchises = [];
        $rows = $this->_db->fetchAssoc($sql);

        foreach ($rows as $row) {
            $franchises[$row['id']] = $row['nickname'];
        }

        return $franchises;
    }
}

