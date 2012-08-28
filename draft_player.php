<html>
<head>
<title>WebReg -- Draft Free Agents</title>
</head>
<body>
<h3 align="Center">WebReg -- Draft Free Agents</h3>
<?php 
die('under construction, will reopen for 2013 draft');

/*
require_once 'db_config.php';
require_once 'transaction_log.php';

$task = "list";
$dbh = new PDO(DSN);
define("ROUND","12");

if (isset($_GET['task'])) $task = $_GET['task'];
if (isset($_POST['task'])) $task = $_POST['task'];

if ($task == "draft") {
    $id = 0;
    $round = Array("1st","2nd","3rd","4th","5th","6th","7th","8th","9th","10th",
        "11th","12th","13th","14th","15th","16th","17th","18th","19th","20th");
    $team_list = Array();
    $sql = "SELECT nickname FROM franchises ORDER BY nickname";

    foreach ($dbh->query($sql) as $row) {
        $team_list[] = $row[0];
    }

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }

    if ($id !=0) {
        $sql = "SELECT tig_name FROM teams WHERE id = {$id}";
        $row = $dbh->query($sql);
        $tig_name = $row['tig_name'];

        // Now, we can display the form to let them assign the player to a team
?>
<form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="task" value="do_draft">
<input type="hidden" name="id" value="<?php print $id ;?>">
<input type="hidden" name="tig_name" value="<?php print $tig_name; ?>"> 
<div align="center">
<table>
<tr>
<td><?php print $tig_name; ?></td>
<td><select name="ibl_team">
<?php foreach ($team_list as $ibl_team) : ?>
    <option value="<?php print $ibl_team; ?>"><?php print $ibl_team; ?></option>\n
<?php endforeach; ?>
                                </select>
                                </td>
                                <td><select name="round">
<?php foreach ($round as $value) : ?>
    <option value="<?php print $value; ?>"><?php print "{$value} Round ".ROUND; ?></option>\n
<?php endforeach; ?>
<?php
        }
?>
</select>
</td>
<td><input type="submit" value="Draft Player"></td>
</tr>
</table>
</div>
</form>
<?php 
    } else {
        print "Invalid player ID<br>";
        $task = "list";
    }
} else {
    print "Invalid player ID<br>";
    $task = "list";
}

if ($task == "do_draft") {
    // update rosters database by drafting a player
    $id = 0;
    $ibl_team = 'FA';
    $round = "Undrafted";
    $tig_name = "";

    if (isset($_POST["id"])) $id = (int)$_POST["id"];
    if (isset($_POST["ibl_team"])) $ibl_team = $_POST["ibl_team"];
    if (isset($_POST["round"])) $round = $_POST["round"];
    if (isset($_POST["tig_name"])) $tig_name = $_POST["tig_name"];

    // Sanity checks for the data
    if ($id == 0 || $ibl_team == 'FA' || $round == "Undrafted") {
        print "You must submit the correct data to draft someone!";
        $task == "list";
    } else {
        $comments = "{$round} Round (".ROUND . ")";
        $comments = pg_escape_string($comments);
        $sql = "UPDATE teams SET ibl_team = :ibl_team, comments = :comments WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':ibl_team', $ibl_team);
        $stmt->bindParam(':comments', $comments);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        if ($result)) {
            print "<div align='center'>Assigned {$tig_name} to <b>{$ibl_team}</b></div><br><br>";

            // Add an entry to the transaction log
            transaction_log($ibl_team, "Drafts {$tig_name} {$comments}");
            $task = "list";
        } else {
            print "<div align='center'>Couldn't update record for {$tig_name}.  Please try again later</div><br><br>";
            $task = "list";
        }
    }
}

if ($task == "list") {
    // Show a list of free agents to draft
    $tig_name = Array();
    $sql = "SELECT id, tig_name FROM teams WHERE ibl_team = 'FA' ORDER by tig_name";
    $result = $dbh->query($sql);

    foreach ($result as $row) {
        $id = $row['id'];
        $tig_name[$id] = $row['tig_name'];
    }

    // Now, let's display the list of people to draft
?>
                <div align="center">
                <table>
<?php
    foreach ($tig_name as $id=>$player) {
?>
                                <tr>
                                <td><?php print $player; ?></td>
                                <td><a href=<?php print $_SERVER['PHP_SELF']; ?>?task=draft&id=<?php print $id; ?>>Draft</a></td>
                                </tr>
<?php
    }
?>
                </table>
                </div>
<?php
}

?>
<hr>
<div align="center"><a href=free_agents.php>Return to Manage Free Agents</a></div>
 */
