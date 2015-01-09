<?hh
require 'bootstrap.php';

// view_free_agents.php

// Generates a free agent report
require './templates/view_free_agents_header.php';
require 'db_config.php';

// First, display all the pitchers

print "PITCHERS<br><br>";
$sql="SELECT tig_name FROM teams WHERE ibl_team='FA' AND item_type=1 ORDER BY tig_name";
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $tig_name = $row['tig_name'];
    print "{$tig_name}<br>";
}

// Now, the batters
print "<br>BATTERS<br>";

$sql="SELECT tig_name FROM teams WHERE ibl_team='FA' AND item_type=2 ORDER BY tig_name";
$sth = $db->prepare($sql);
$sth->execute();
$result=$sth->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $tig_name = $row['tig_name'];
    print "$tig_name<br>";
}

require './templates/view_free_agents_footer.php';
