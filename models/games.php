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
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return (isset($row['max'])) ? $row['max'] : 1;
    }
}

