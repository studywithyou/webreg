<?hh
class Franchise
{
    protected $_db;

    public function __construct(PDO $db)
    {
        $this->_db = $db;
    }

    public function getAll() : ImmMap
    {
        $sql = "
            SELECT *
            FROM franchises
            ORDER BY nickname
            ";
        $franchises = new Map(null);
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $p = Pair {$row['id'], $row['nickname']};
            $franchises->add($p);
        }

        return new ImmMap($franchises);
    }
}
