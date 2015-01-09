<?php
class Franchise
{
    protected $_db;

    public function __construct(PDO $db)
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
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $franchises[$row['id']] = $row['nickname'];
        }

        return $franchises;
    }
}

