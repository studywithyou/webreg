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

    public function deletePlayerById($player_id)
    {
        $bind = ['player_id' => $player_id];
        $delete = $this->_db->newDelete();
        $delete->from('teams')
            ->where('id IN (:player_id)');

        return $this->_db->query($delete, $bind);
    }

    public function releasePlayerByList($release_list)
    {
        $released_player = [];
        $delete = $this->_db->newDelete();
        $delete->from('teams')
            ->where('id = :id');

        $update = $this->_db->newUpdate();
        $update->table('teams')
            ->cols(['ibl_team'])
            ->set('ibl_team', "'FA'")
            ->where('id = :id');

        foreach ($release_list as $release_id) {
            // If a player is uncarded and gets released, we need to delete them
            $sql = "SELECT status FROM teams WHERE id={$release_id}";
            $row = $this->_db->fetchOne($sql);
            $status = $row["status"];
            $bind = ['id' => $release_id];

            if ($status == 3) { // player is uncarded, so they get deleted
                $this->_db->query($delete, $bind);
            } else {
                $this->_db->query($update, $bind);
            }
        }
    }

    public function update($raw_post)
    {
        $activate_list = [];
        $deactivate_list = [];
        $id=$raw_post["id"];
        $tig_name=$raw_post["tig_name"];
        $type=$raw_post["type"];
        $comments=$raw_post["comments"];
        $status=$raw_post["status"];
        $ibl_team = $raw_post['ibl_team'];
        $update_list=array();

        // quick hack for picks since we don't assign them a status
        $shadow_tig_name=$raw_post["shadow_tig_name"];
        $shadow_type=$raw_post["shadow_type"];
        $shadow_comments=$raw_post["shadow_comments"];
        $shadow_status=$raw_post["shadow_status"];

        $updated_list = [];

        foreach ($id as $modify_id)
        {
            $new_data = [
                'id' => $modify_id,
                'tig_name' => $tig_name[$modify_id],
                'type' => $type[$modify_id] ?: 0,
                'comments' => $comments[$modify_id],
                'status' => $status[$modify_id]
            ];
            $old_data = [
                'id' => $modify_id,
                'tig_name' => $shadow_tig_name[$modify_id],
                'type' => $shadow_type[$modify_id] ?: 0,
                'comments' => $shadow_comments[$modify_id],
                'status' => $shadow_status[$modify_id]
            ];

            // Now, let's only do an update if we have actually changed data
            $update_check_count = count(array_intersect_assoc($new_data, $old_data));

            if ($update_check_count != 5) {
                $updated_list[] = "Updated <b>{$new_data['tig_name']}</b>";
                $this->updatePlayer($new_data, $old_data);

                if ($new_data['status'] == 1) $activate_list[] = $new_data['tig_name'];

                if ($new_data['status'] == 2) $deactivate_list[] = $new_data['tig_name'];

                if ($new_data['status'] == 3) {
                    $uncarded_list[] = $new_data['tig_name'];
                    $uc_year=date('y')+1;

                    if ($uc_year < 10) { $uc_year = "0{$uc_year}"; }

                    // If this player was already uncarded, we have to update things
                    if (preg_match('/\[UC/',$new_data['comments']) == TRUE) {
                        $replacement="[UC{$uc_year}]";

                        $new_data['comments'] = preg_replace('/(\[UC\w+])/', $replacement, $new_data['comments']);
                    } else {
                        $new_data['comments'] .= " [UC{$uc_year}]";
                    }
                }
            }
        }

        // Return an array of lists to display and log
        return [
            'updated_list' => $updated_list,
            'activate_list' => $activate_list,
            'deactivate_list' => $deactivate_list
        ];
    }

    public function updatePlayer($new_data, $old_data)
    {
        $update_list[]="Updating <b>{$new_data['tig_name']}</b><br>";
        $bind = [
            'id' => $new_data['id'],
            'tig_name' => $new_data['tig_name'],
            'item_type' => $new_data['type'],
            'status' => $new_data['status'],
            'comments' => $new_data['comments']
        ];
        $update = $this->_db->newUpdate();
        $update->table('teams')
            ->cols(['tig_name', 'item_type', 'status', 'comments'])
            ->where('id = :id');

        return $this->_db->query($update, $bind);
    }

    public function addPlayer($player_data)
    {
        $insert = $this->_db->newInsert();
        $insert->into('teams')
            ->cols(['tig_name', 'ibl_team', 'item_type', 'comments', 'status']);
        return $this->_db->query($insert, $player_data);
    }
}

