<?hh

// free_agents.php

// File that handles the signing off a player from the free agent pile or the releasing
// of a player to the free agent pile

echo '
<html>
<head>
<title>WebReg -- Manage Free Agents</title>
</head>
<body>
<h3 align="Center">WebReg -- Manage Free Agents</h3>
';

require 'bootstrap.php';
require 'db_config.php';
require 'transaction_log.php';

$task="";
//$db = DB::connect(DSN);

if (isset($_POST["task"])) $task=$_POST["task"];
if (isset($_GET["task"])) $task=$_GET["task"];

if ($task=="do_signing")
{
	// assign free agents to their new teams
	$id=$_POST["id"];
	$ibl_team=$_POST["ibl_team"];
	$tig_name=$_POST["tig_name"];
	echo '<div align="center">';
	$sth = $db->prepare("UPDATE teams SET ibl_team = ?, comments = ?, status = 2 WHERE id=?");

	foreach ($id as $key=>$sign_id)
	{
		$sign_team=$ibl_team[$key];

		if ($sign_team!='FA')
		{
			$comments = "Free Agent ".date("m/y");
			$sth->execute(array($sign_team, $comments, $sign_id));
			$log_entry="Signs ".$tig_name[$sign_id];
			transaction_log($sign_team,$log_entry, $db);
			print "<b>".$tig_name[$sign_id]."</b> signs with {$sign_team}<br>";
		}
	}
	echo "</div>";
}

if ($task=="sign")
{
	// Present a list of all free agents
	// and then allow them to be assigned to a team
	$sql="SELECT DISTINCT(ibl_team) FROM teams WHERE ibl_team!='FA' ORDER BY ibl_team";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$ibl_team_dropdown="<select name=ibl_team[]>";
	$ibl_team_dropdown.="<option value='FA' selected >Free Agent</option>";

	foreach ($result as $row) {
		$ibl_team_dropdown.="<option value='".$row['ibl_team']."'>".$row['ibl_team']."</option>";
	}

	$ibl_team_dropdown.="</select>";

	// Now, get a list of all the players who are free agents
	$sql="SELECT id,tig_name FROM teams WHERE ibl_team='FA' ORDER BY tig_name";
	$stmt = $db->prepare($sql);
	$stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "
	<div align=center>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<input type='hidden' name='task' value='do_signing'>
    <table>
    ";
	foreach ($result as $row) {
		$tig_name=trim($row['tig_name']);
		$id = $row['id'];
		echo "<tr>
		<td>{$tig_name}</td><input type='hidden' name='tig_name[{$id}]' value='{$tig_name}'>
		<td>{$ibl_team_dropdown}</td><input type='hidden' name='id[]' value='{$id}'>
		</tr>";
    }
    echo '
	<tr><td colspan=2 align=center><input type="submit" value="Sign Free Agents"></td></tr>
	</table>
    </form>
    ';
}

if ($task=="modify")
{
	// do a batch modify of free agents
	if (isset($_POST['tig_name'])) $tig_name=$_POST["tig_name"];
	if (isset($_POST['shadow_tig_name'])) $shadow_tig_name=$_POST["shadow_tig_name"];
	if (isset($_POST['shadow_type'])) $shadow_type=$_POST['shadow_type'];
	if (isset($_POST['delete'])) $delete=$_POST["delete"];
	if (isset($_POST['tig_type'])) $type=$_POST["tig_type"];

	print "<div align=center>";

	$sth1 = $db->prepare('UPDATE teams SET tig_name = ? WHERE id = ?');
	$sth2 = $db->prepare('UPDATE teams SET item_type = ? WHERE id = ?');

	foreach ($tig_name as $id=>$player)
	{
		if (isset($delete[$id]) && $delete[$id]==1)
		{
			$sql="DELETE FROM teams WHERE id={$id}";
			$stmt = $db->prepare($sql);
			$stmt->execute();
			print "Deleted <b>$player</b> from free agent pool<br>";
		}
		else
		{
			if ($shadow_tig_name[$id]!=$player)
			{
				$sth1->execute(array(stripslashes($player), $id));
				print "Changed <b>".$shadow_tig_name[$id]."</b> to $player<br>";
			}

			if ($shadow_type[$id]!=$type[$id]) {
				$sth2->execute(array($type[$id], $id));
				print "Changed player type for {$player}<br>";
			}

		}
	}

	print "</div>";
	$task = "view";
}

if ($task=="view")
{
	// Show a list of all current free agents
	$sql="SELECT id,tig_name,item_type FROM teams WHERE ibl_team='FA' ORDER BY tig_name";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "
	<div align='center'>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<input name='task' value='modify' type='hidden'>
	<table>";
	foreach ($result as $row) {
		$id=$row['id'];
		$tig_name=$row['tig_name'];
		$tig_type=$row['item_type'];

		if ($tig_type==1) {
			$type_label[1]="selected";
			$type_label[2]="";
		} else {
			$type_label[1]="";
			$type_label[2]="selected";
		}

	    echo "
		<tr><td>
		<input type='hidden' name=shadow_tig_name[{$id}] value='{$tig_name}'>
		<input type='hidden' name=shadow_type[{$id}] value={$tig_type}>
		<input name=tig_name[{$id}] size=20 value='{$tig_name}'>
		</td>
		<td>
		<select name=tig_type[{$id}]>
			<option value=1 {$type_label[1]}>Pitcher</option>\n
			<option value=2 {$type_label[2]}>Batter</option>\n
		</select>
		</td>
		<td>
		<input name=delete[{$id}] type='checkbox' value=1> Delete
		</td>
		</tr>";
	}
    echo "
	<tr><td><input type=submit value='Make Changes'></td></tr>
	</table>
	</form>
	</div>";
}

if ($task=="do_add")
{
	// process the players that have been added to the free agent pool
	if (isset($_POST['fa_tig_name'])) $fa_tig_name = $_POST['fa_tig_name'];
	if (isset($_POST['fa_type'])) $fa_type = $_POST['fa_type'];

	if (count($fa_tig_name)<1)
	{
		echo "<div align=center><font color=red>You must fill in at least one name</div>";
		$task="";
	}
	else
	{
		print "<div align=center>";
		$sth = $db->prepare("INSERT INTO teams (tig_name, ibl_team, item_type, status) VALUES (?, 'FA', ?, 1)");

		foreach ($fa_tig_name as $key=>$player)
		{
			if ($player!="")
			{
				$sth->execute(array($player, $fa_type[$key]));
				print "Added <b>$player</b> to the free agent pool<br>";
			}
		}
	}
}

if ($task=="add")
{
	// Present a form where you can add up to 20 players at a time to the free agent pool
    echo "
	<div align=center>
	<p>Add Players to Free Agent Pool</p>
	<form action='{$_SERVER["PHP_SELF"]}' method='post'>
	<input name=task type=hidden value='do_add'>
	<table>
	<tr><td><b>TIG Name</b></td></tr>";
	for ($x=1;$x<=20;$x++)
	{
	    echo '
		<tr>
			<td><input name=fa_tig_name[] size=20></td>
			<td><select name=fa_type[]>
					<option value=1>Pitcher</option>\n
					<option value=2>Batter</option>\n
				 </select></td>
		</tr>';
    }
    echo '
	<tr><td><input type="submit" value="Add Players"></td></tr>
	</form>
	</table>';
}

if ($task=="")
{
    echo "
	<p>
	<div align='center'>
	<a href='{$_SERVER['PHP_SELF']}?task=sign'>Sign Free Agents</a><br>
	<a href='draft_player.php'>Draft A Player</a><br>
	<a href='{$_SERVER['PHP_SELF']}?task=add'>Add Players to Free Agent Pool</a><br>
	<a href='{$_SERVER['PHP_SELF']}?task=view'>View / Edit Free Agents</a><br>
    ";
}
echo "
<hr>
<div align=center>Return To <a href=roster_management.php>Roster Management</a></div>
</body>
</html>";
