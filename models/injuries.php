<?php
class Injury
{
    protected $_db;

    public function __construct($db)
    {
        $this->_db = $db;
    }

    public function delete($id)
    {
        if (!$id) {
            return false;
        }

        $sql = "DELETE FROM injuries WHERE id = '{$id}'";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();

        return true;
    }

    public function find($id)
    {
        $sql = "
            SElECT *

            WHERE id = '{$id}'
        ";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $sql = "
            SELECT i.id, i.week_starting, i.week_ending, i.franchise_id, i.description, f.nickname
            FROM injuries i
            JOIN franchises f ON i.franchise_id = f.id
            ORDER BY i.week_starting, f.nickname, i.week_ending
            ";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByWeek($week)
    {
        $sql = "
            SELECT *
            FROM injuries
            WHERE week_starting >= {$week}
            ORDER BY week_starting
            ";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results = $this->_db->query($sql);

        if (!$results) {
            return array();
        }

        $injuries = array();

        foreach ($results as $row) {
            $injuries[] = $row;
        }

        return $injuries;
    }

    public function save($data)
    {
        if (!isset($data['id'])) {
            return $this->_create($data);
        }

        $this->_update($data);

    }

    protected function _create($data)
    {
        $sql = "
            INSERT INTO injuries
            VALUES (?, ?, ?, ?, ?)
            ";
        $sth = $this->_db->prepare($sql);
        $this->_db->execute($sth, array(
            $this->_generateUuid(),
            $data['franchise_id'],
            $data['week_starting'],
            $data['week_ending'],
            $data['description']
        ));
    }

    protected function _generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    protected function _update($data)
    {
        $sql = "
            UPDATE injuries
            SET franchise_id = ?,
            week_starting = ?,
            week_ending = ?,
            description = ?
            WHERE id = ?
            ";
        $sth = $this->_db->prepare($sql);
        $this->_db->execute($sth, array(
            $data['franchise_id'],
            $data['week_starting'],
            $data['week_ending'],
            $data['description'],
            $data['id']
        ));
    }
}