<html>
<head>
<title>WebReg -- Modify Rosters</title>
</head>
<body>
<h3 align=center>WebReg -- Modify Rosters</h3>
<?php

// modify_roster.php

// Used to modify existing rosters

// Can be used to do the following:
//		1. update player names and teams at the end of the season
//		2. change status of players (Active, Inactive, UC)

require_once 'db_config.php';
include_once('transaction_log.php');

$get_team=0;

if (isset($_POST["get_team"])) $get_team=$_POST["get_team"];

// Now, if we don't have a team selected, just get a dropdown list
// of teams to work with
if ($get_team==0)
{
    $sql="SELECT DISTINCT(ibl_team) FROM teams ORDER BY ibl_team";
    $results = $db->fetchAll($sql);

    if ($results !=FALSE)
    {
        foreach ($results as $row) {
            $ibl_team[] = $row['ibl_team'];
        }
    }
?>
    <div align=center>
    <form action=<?php print $_SERVER["PHP_SELF"];?> method="post">
    <input type=hidden name=get_team value=1>
    <table>
    <tr><td><input type=submit value="Choose A Team"></td>
    <td><select name="ibl_team">
<?php
    foreach ($ibl_team as $team)
    {
?>
        <option value="<?php print $team;?>"><?php print $team;?></option>
<?php
    }
?>
    </select></td></tr>
    </table>
    </form>
    </div>
<?php
}
else
{

    // Let's collect all the data we just grabbed via _POST
    if (isset($_POST["id"])) $id=$_POST["id"];
    if (isset($_POST["tig_name"])) $tig_name=$_POST["tig_name"];
    if (isset($_POST["type"])) $type=$_POST["type"];
    if (isset($_POST["comments"])) $comments=$_POST["comments"];
    if (isset($_POST["status"])) $status=$_POST["status"];
    if (isset($_POST['ibl_team'])) $ibl_team=$_POST['ibl_team'];
    $update_list=array();


    // quick hack for picks since we don't assign them a status
    if (isset($_POST["shadow_tig_name"])) $shadow_tig_name=$_POST["shadow_tig_name"];
    if (isset($_POST["shadow_type"])) $shadow_type=$_POST["shadow_type"];
    if (isset($_POST["shadow_comments"])) $shadow_comments=$_POST["shadow_comments"];
    if (isset($_POST["shadow_status"])) $shadow_status=$_POST["shadow_status"];

    // First, let's see if we're deleting any entries
    $delete=0;

    if (isset($_POST["delete"])) $delete=1;

    if ($delete==1)
    {
        $delete_list=$_POST["delete"];

        foreach ($delete_list as $delete_id)
        {
            $sql="DELETE FROM teams WHERE id={$delete_id}";
            $db->query($sql);
        }
    }

    // Hey, are we releasing anyone?
    $release=0;

    if (isset($_POST["release"])) $release=1;

    if ($release==1)
    {
        $release_list=$_POST["release"];

        print "<div align=center>";
        $delete = $db->newDelete();
        $delete->from('teams')
            ->where('id = :id');

        $update = $db->newUpdate();
        $update->table('teams')
            ->cols(['ibl_team'])
            ->set('ibl_team', "'FA'")
            ->where('id = :id');

        foreach ($release_list as $release_id) {
            // If a player is uncarded and gets released, we need to delete them
            $sql = "SELECT status FROM teams WHERE id={$release_id}";
            $row = $db->fetchOne($sql);
            $status = $row["status"];
            $bind = ['id' => $release_id];

            if ($status == 3) { // player is uncarded, so they get deleted
                $db->query($delete, $bind);
            } else {
                $db->query($update, $bind);
            }

            print "Released {$tig_name[$release_id]}<br>";
            $released_player[]=$tig_name[$release_id];
        }

        print "</div>";

        // Build log_entry for transaction log
        $log_entry="Releases ".implode(", ",$released_player);
        transaction_log($ibl_team, $log_entry, $db);

    }

    // Now, if we're modifying a roster, let's update the records we've worked on
    $modify=0;

    if (isset($_POST["modify"])) $modify=$_POST["modify"];

    if ($modify==1)
    {
        // Only do an update if we actually altered a record
        $id=$_POST["id"];
        $tig_name=$_POST["tig_name"];
        $type=$_POST["type"];
        $comments=$_POST["comments"];
        $status=$_POST["status"];
        $ibl_team = $_POST['ibl_team'];
        $update_list=array();

        // quick hack for picks since we don't assign them a status
        $shadow_tig_name=$_POST["shadow_tig_name"];
        $shadow_type=$_POST["shadow_type"];
        $shadow_comments=$_POST["shadow_comments"];
        $shadow_status=$_POST["shadow_status"];
        $sql = "UPDATE teams SET tig_name = :tig_name, item_type = :item_type, status = :status, comments = :comments WHERE id = :id";

        foreach ($id as $modify_id)
        {
            $update_tig_name=$tig_name[$modify_id];
            $original_tig_name=$shadow_tig_name[$modify_id];
            $update_type=$type[$modify_id];
            $update_comments=$comments[$modify_id];
            $update_status=$status[$modify_id];

            if ($update_type=="") $update_type=0;
            if ($update_status=="") $update_status=0;

            // Now, let's only do an update if we have actually changed data
            if ($update_tig_name!=$shadow_tig_name[$modify_id]
                || $update_type!=$shadow_type[$modify_id]
                || $update_comments!=$shadow_comments[$modify_id]
                || $update_status!=$shadow_status[$modify_id])
            {
                // Keep track of who was activated and who was deactivated
                // active=1, inactive=2, uncarded=3

                if ($update_status==1 && $shadow_status[$modify_id]==2) $activate_list[]=$update_tig_name;
                if ($update_status==2 && $shadow_status[$modify_id]==1) $deactivate_list[]=$update_tig_name;
                if ($update_status==3 && $shadow_status[$modify_id]!=3) $uncarded_list[]=$update_tig_name;

                // Now we have to add UC to the comments
                if ($update_status == 3 && ($update_status != $shadow_status[$modify_id])) {
                    $uc_year=date('y')+1;

                    if ($uc_year < 10) { $uc_year = "0{$uc_year}"; }

                    // If this player was already uncarded, we have to update things
                    if (preg_match('/\[UC/',$update_comments) == TRUE) {
                        $replacement="[UC{$uc_year}]";

                        $update_comments = preg_replace('/(\[UC\w+])/',$replacement,$update_comments);
                    }
                    else {
                        $update_comments.= " [UC{$uc_year}]";
                    }
                }

                $update_list[]="Updating <b>$update_tig_name</b><br>";
                $update = $db->newUpdate();
                $update->table('teams')
                    ->cols(['tig_name', 'item_type', 'status', 'comments'])
                    ->where('id = :id');
                $bind = [
                    'id' => $modify_id,
                    'tig_name' => $update_tig_name,
                    'item_type' => $update_type,
                    'status' => $update_status,
                    'comments' => $update_comments
                ];
                $db->query($update, $bind);
            }
        }

        // Now, make an entry in the transaction log if neccessary
        if (isset($activate_list))
        {
            $log_entry="Activates ".implode(", ",$activate_list);
            transaction_log($ibl_team,$log_entry);
        }

        if (isset($deactivate_list))
        {
            $log_entry="Deactivates ".implode(", ",$deactivate_list);
            transaction_log($ibl_team,$log_entry);
        }

    }

    // Check to see if we're adding a new player to this list
    $add=0;

    if (isset($_POST["new_tig_name"]) && $_POST["new_tig_name"]!="") $add=1;

    if ($add==1)
    {
        $new_tig_name=$_POST["new_tig_name"];
        $new_type=$_POST["new_type"];
        $new_status=$_POST["new_status"];
        $new_comments=$_POST["new_comments"];
        $insert = $db->newInsert();
        $insert->into('teams')
            ->cols(['tig_name', 'ibl_team', 'item_type', 'comments', 'status']);

        $bind = [
            'tig_name' => $new_tig_name,
            'ibl_team' => $ibl_team,
            'item_type' => $new_type,
            'comments' => $new_comments,
            'status' => $new_status
        ];
        $db->query($insert, $bind);
    }

    // We've picked a team, let's grab all the players on their roster
    $ibl_team=$_POST["ibl_team"];
    $sql="SELECT id,tig_name,item_type,comments,status FROM teams WHERE ibl_team='$ibl_team' ORDER BY tig_name";
    $result=$db->query($sql);
?>
    <div align=center>
    Modifying Roster for <b><?php print $ibl_team;?></b><br><br>
<?php
    if (sizeof($update_list)!=0)
    {
        foreach ($update_list as $update_tig_name) print "$update_tig_name<br>";
    }
?>

    <form action=<?php print $_SERVER["PHP_SELF"];?> method=post>
    <input type=hidden name=get_team value=1>
    <input type=hidden name=ibl_team value="<?php print $ibl_team;?>">
    <input type=hidden name=modify value=1>
    <table>
    <tr>
    <td><b>TIG Name</b></td>
    <td><b>Type</b></td>
    <td><b>Comments</b></td>
    <td><b>Status</b></td>
    <td><b>Delete</b></td>
    <td><b>Release</b></td>
    </tr>
<?php
    if ($result!=FALSE)
    {
        $results = $db->fetchAll($sql);

        foreach ($results as $row) {
            $id=$row['id'];
            $tig_name=trim($row['tig_name']);
            $type=$row['item_type'];
            $comments=trim($row['comments']);
            $status=$row['status'];
            $type_selected=array();
            $status_selected=array();

            for ($x=0;$x<=2;$x++) {
                $type_selected[$x]="";
                $status_selected[$x+1]="";
            }

            $type_selected[$type]="selected";
            $status_selected[$status]="selected";
?>
            <input type=hidden name=shadow_tig_name[<?php print $id;?>] value="<?php print $tig_name;?>">
            <input type=hidden name=shadow_type[<?php print $id;?>] value=<?php print $type;?>>
            <input type=hidden name=shadow_comments[<?php print $id;?>] value="<?php print $comments;?>">
            <input type=hidden name=shadow_status[<?php print $id;?>] value=<?php print $status;?>>
            <input type=hidden name=id[] value=<?php print $id;?>>
            <tr>
            <td><input name=tig_name[<?php print $id;?>] value="<?php print $tig_name;?>" size=20></td>
            <td><select name=type[<?php print $id;?>]>
                <option value=0 <?php print $type_selected[0];?>>Pick</option>
                <option value=1 <?php print $type_selected[1];?>>Pitcher</option>
                <option value=2 <?php print $type_selected[2];?>>Batter</option>
                </select></td>
            <td><input name=comments[<?php print $id;?>] value="<?php print $comments;?>" size=40></td>
                <td><select name=status[<?php print $id;?>]>
                    <option value=1 <?php print $status_selected[1];?>>Active</option>
                    <option value=2 <?php print $status_selected[2];?>>Inactive</option>
                    <option value=3 <?php print $status_selected[3];?>>Uncarded</option>
                    </select>
                </td>
            </td>
            <td><input name=delete[] type="checkbox" value="<?php print $id;?>"></td>
            <td><input name=release[] type="checkbox" value=<?php print $id;?>></td>
            </tr>
<?php
        }
        }
?>
    <tr>
    <td><input name=new_tig_name value="" size=20></td>
    <td><select name=new_type>
        <option value=0 <?php print $type_selected[0];?>>Pick</option>
        <option value=1 <?php print $type_selected[1];?>>Pitcher</option>
        <option value=2 <?php print $type_selected[2];?>>Batter</option>
        </select></td>
    <td><input name=new_comments value="" size=40></td>
    <td><select name=new_status>
        <option value=1>Active</option>
        <option value=2>Inactive</option>
        <option value=3>Uncarded</option>
        </select>
    </td>
    </tr>
    <tr><td colspan=4><input type=submit value="Modify Roster"></td></tr>
    </table>
    </form>
    </div>
<?php
    }
?>
<hr>
<div align=center><a href=roster_management.php>Return to Roster Management</a></div>
</body>
</html>
