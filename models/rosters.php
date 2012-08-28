<?php
class Roster
{
    protected $_db;

    public function __construct($db)
    {
        $this->_db = $db;
    }

    public function getByNickname($nickname)
    {
    	$sql = "
    	SELECT *
    	FROM teams
    	WHERE ibl_team = '{$nickname}'
        ORDER BY item_type DESC, tig_name
    	";

    	$results = $this->_db->query($sql);

    	if (!$results) {
    		return array();
    	}

    	$roster = array();

    	while ($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
            $roster[] = $row;
        }

        return $roster;
    }

    public function updatePlayerTeam($iblTeam, $playerId)
    {
        $sql = "
        UPDATE teams
        SET ibl_team = '{$iblTeam}'
        WHERE id = {$playerId}
        ";

        return $this->_db->query($sql);
    }
}

