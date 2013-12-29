<?php
class Roster
{
    protected $_db;

    /**
     * Constructor for class
     *
     * @param Aura\Sql\Connection\Pgsql $db
     */
    public function __construct($db)
    {
        $this->_db = $db;
    }

    /**
     * Get all players on a roster based on the team nicknam
     *
     * @param string $nickname
     * @return array
     */
    public function getByNickname($nickname)
    {
        $select = $this->_db->newSelect();
        $select->cols('[*]')
            ->from('teams')
            ->where('ibl_team = :ibl_team')
            ->orderBy('item_type DESC, tig_name');
        $values = ['ibl_team' => $nickname];
    	$results = $this->_db->fetchAll($select, $values);

    	if (!$results) {
    		return [];
    	}

    	$roster = [];

        foreach ($results as $row) {
            $roster[] = $row;
        }

        return $roster;
    }

    /**
     * Update the IBL team a player is on based on the player ID
     *
     * @param string $iblTeam
     * @param integer $playerId
     * @return boolean
     */
    public function updatePlayerTeam($iblTeam, $playerId)
    {
        $update = $this->_db->newUpdate();
        $update->table('teams')
            ->cols(['ibl_team'])
            ->where('id = :id');
        $values = [
            'ibl_team' => $iblTeam,
            'id' => $playerId
        ];

        return $this->_db->query($update, $values);
    }

    /**
     * Delete a player based on the player ID
     *
     * @param integer $player_id
     * @return boolean
     */
    public function deletePlayerById($player_id)
    {
        $values = ['player_id' => $player_id];
        $delete = $this->_db->newDelete();
        $delete->from('teams')
            ->where('id IN (:player_id)');

        return $this->_db->query($delete, $values);
    }

    /**
     * Release players from a team based on an array of ID's
     *
     * @param array $release_list
     */
    public function releasePlayerByList($release_list)
    {
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
            $select = $this->_db->newSelect();
            $select->cols(['status'])
                ->from('teams')
                ->where('id = :id');
            $row = $this->_db->fetchOne($select, ['id' => $release_id]);
            $status = $row["status"];
            $values = ['id' => $release_id];

            if ($status == 3) { // player is uncarded, so they get deleted
                $this->_db->query($delete, $values);
            } else {
                $this->_db->query($update, $values);
            }
        }
    }

    /**
     * Update an entire roster based on POST data
     *
     * @param array $raw_post
     * @return array
     */
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

    /**
     * Update an individual player on the roster
     *
     * @param array $new_data
     * @param array $old_data
     * @return boolean
     */
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

    /**
     * Add a player not currently on anyone's roster or not a
     * free agent to someone's roster
     *
     * @param array $player_data
     * @return boolean
     */
    public function addPlayer($player_data)
    {
        $insert = $this->_db->newInsert();
        $insert->into('teams')
            ->cols(['tig_name', 'ibl_team', 'item_type', 'comments', 'status']);
        return $this->_db->query($insert, $player_data);
    }
}

