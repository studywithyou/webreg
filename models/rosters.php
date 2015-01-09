<?hh
class Roster
{
    protected $_db;

    /**
     * Constructor for class
     *
     * @param PDO $db
     */
    public function __construct(PDO $db)
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
        $sql = "SELECT * FROM teams WHERE ibl_team = ? ORDER BY item_type DESC, tig_name";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute(array($nickname));
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results) {
            return array();
        }

        $roster = array();

        foreach ($results as $row) {
            $roster[] = $row;
        }

        return $roster;
    }

    /**
     * Get all players on a roster based on the team nicknam
     *
     * @param string $nickname
     * @param string $type
     * @return array
     */ 

    /**
     * Update the IBL team a player is on based on the player ID
     *
     * @param string $iblTeam
     * @param integer $playerId
     * @return boolean
     */
    public function updatePlayerTeam($iblTeam, $playerId)
    {
        $sql = "UPDATE teams SET ibl_team = ? WHERE id = ?";
        $stmt = $this->_db->prepare($sql);
        return $stmt->execute(array($iblTeam, $playerId));
    }

    /**
     * Delete a player based on the player ID
     *
     * @param array $player_ids
     * @return boolean
     */
    public function deletePlayerById($player_ids)
    {
        $sql = "DELETE FROM teams WHERE id = ?";
        $stmt = $this->_db->prepare($sql);

        foreach ($player_ids as $player_id) {
            $stmt->execute($player_id);
        }
    }

    /**
     * Release players from a team based on an array of ID's
     *
     * @param array $release_list
     */
    public function releasePlayerByList($release_list)
    {
        $select_sql = "SELECT status FROM teams WHERE id = ?";
        $select_stmt = $this->_db->prepare($select_sql);
        $update_sql = "UPDATE teams SET ibl_team = 'FA' WHERE id = ?";
        $update_stmt = $this->_db->prepare($update_sql);

        foreach ($release_list as $release_id) {
            // If a player is uncarded and gets released, we need to delete them
            $select_stmt->execute(array($release_id));
            $result = $select_stmt->fetch();
            if ($result['status'] == 3) { // player is uncarded, so they get deleted
                $this->deletePlayerById($release_id);
            } else {
                $update_stmt->execute(array($release_id));
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
        $uncarded_list = [];

        // quick hack for picks since we don't assign them a status
        $shadow_tig_name=$raw_post["shadow_tig_name"];
        $shadow_type=$raw_post["shadow_type"];
        $shadow_comments=$raw_post["shadow_comments"];
        $shadow_status=$raw_post["shadow_status"];

        $updated_list = [];

        foreach ($id as $modify_id) {
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

                if ($new_data['status'] == 1) {
                    $activate_list[] = $new_data['tig_name'];
                }

                if ($new_data['status'] == 2) {
                    $deactivate_list[] = $new_data['tig_name'];
                }

                if ($new_data['status'] == 3) {
                    $uncarded_list[] = $new_data['tig_name'];
                    $uc_year=date('y')+1;

                    if ($uc_year < 10) {
                        $uc_year = "0{$uc_year}";
                    }

                    // If this player was already uncarded, we have to update things
                    if (preg_match('/\[UC/', $new_data['comments']) == true) {
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
        $sql = "
            UPDATE teams
            SET tig_name = ?, item_type = ?, status = ?, comments = ?
            WHERE id = ?";
        $stmt = $this->_db->prepare($sql);
        $data = array(
            $new_data['tig_name'],
            $new_data['item_type'],
            $new_data['status'],
            $new_data['comments'],
            $new_data['id']
        );
        return $stmt->execute($data);
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
        $sql = "
            INSERT INTO teams (tig_name, ibl_team, item_type, comments, status)
            VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->_db->prepare($sql);
        $data = array(
            $player_data['tig_name'],
            $player_data['ibl_team'],
            $player_data['item_type'],
            $player_data['comments'],
            $player_data['status']
        );
        return $stmt->execute($data);
    }
}
