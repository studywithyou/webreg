<?php
class Franchise 
{
    protected $db;
    protected $pdo;

    public function __construct($db)
    {
        $this->db = $db;
        $this->pdo = new PDO('pgsql:host=localhost;dbname=ibl_stats;user=stats;password=st@ts=Fun');
    }

    public function getAll()
    {
        $select = $this->db->newSelect();
        $select->cols(['*'])
            ->from('franchises')
            ->orderBy(['nickname']);
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $franchises[$row['id']] = $row['nickname'];
        }

        return $franchises;
    }
}

