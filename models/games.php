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
        $result = $this->_db->query($sql);
        $row = $result->fetchRow();
        $week = 1;

        if ($row[0] !== null) {
            $week = $row[0];
        }

        return $week;
    }
}

