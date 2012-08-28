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
        $results = $this->_db->query($sql);

        if (!$results) {
            return array();
        }

        $franchises = array();

        while ($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
            $franchises[$row['id']] = $row['nickname'];
        }

        return $franchises;
    }
}

