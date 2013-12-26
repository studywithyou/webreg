<?php
class Game 
{
    protected $_db;

    public function __construct($db)
    {
        $this->_db = $db;
    }

    public function getMaxWeek()
    {
        $sql = "SELECT MAX(week) FROM games";
        $row = $this->_db->fetchOne($sql);

        return $row['max'] ?: 0;
    }
}

