<?hh
require 'bootstrap.php';

// view_rosters.php
// Generates a team-by-team roster report
require 'db_config.php';
require 'templates/rosters/header.php';

function display_rosters($team_list, $db)
{
	// goes through array displaying the rosters for the teams on the list
	foreach ($team_list as $team)
	{
		print "$team<br>===<br><br>";
        $counter = 0;
		// Display the pitchers first
		print "PITCHERS<br>";
        $sql="SELECT tig_name,comments,status FROM teams WHERE ibl_team=? AND item_type=1 ORDER BY tig_name";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($team));
        $results = $stmt->fetchAll();

        foreach ($results as $row) {
			$counter++;
			$player_name=$row[0];
			$comments=$row[1];
			$status=$row[2];
			$status_flag="&nbsp;";
			$carded_status="&nbsp";

			if ($status==1) $status_flag="*";

			echo " {$counter}. {$status_flag} {$player_name} -- {$comments} {$carded_status}<br>";
		}

		// Now, show the hitters
		print "<br>BATTERS<br>";
        $sql="SELECT tig_name,comments,status FROM teams WHERE ibl_team='$team' AND item_type=2 ORDER BY tig_name";
        $stmt = $db->prepare($sql);
        $stmt->execute($team);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
			$counter++;
			$player_name=$row['tig_name'];
			$comments=$row['comments'];
			$status=$row['status'];
			$status_flag="&nbsp;";

			if ($status==1) $status_flag="*";

			print " $counter. $status_flag $player_name -- $comments<br>";
		}

		// Print out blank spots if there are less than 35 spots on the roster
		if ($counter<35)
		{
			while ($counter<35)
			{
				$counter++;
				print " $counter.<br>";
			}
		}

		// Finally we need to print out their draft picks
		print "<br />DRAFT PICKS<br />";
        $sql = "SELECT tig_name FROM teams WHERE ibl_team = ? AND item_type = 0";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($team));
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$picks = Array();

        foreach ($results as $row) {
			$picks[] = trim($row['tig_name']);
		}

		print implode(", ", $picks);
		print "<br><br>";
	}
}

// create an array with the rosters seperated by conference
$sql = "SELECT nickname FROM franchises WHERE conference = 'AC' ORDER BY nickname";
$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
	$ac_teams[] = trim($row['nickname']);
}

$sql = "SELECT nickname FROM franchises WHERE conference = 'NC' ORDER BY nickname";
$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
	$nc_teams[] = trim($row['nickname']);
}

print "American Conference<br><br>";
display_rosters($ac_teams);
print "<br><br>National Conference<br><br>";
display_rosters($nc_teams);
require 'templates/rosters/footer.php';
