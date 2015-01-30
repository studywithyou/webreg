<?hh
require 'bootstrap.php';
require 'db_config.php';

$year = 'pit' . $_GET['year'];
$sql = "SELECT mlb,name FROM $year GROUP BY mlb,name";
$stmt = $db->prepare($sql);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($players as $player) {
	$sql = "SELECT gs,w,l,week FROM $year WHERE mlb='{$player['mlb']}' AND name = '{$player['name']}' ORDER BY week, home, away";
    $sql = "SELECT gs,w,l,week FROM $year WHERE mlb= ? AND name = ? ORDER BY week, home, away";
    $stmt = $db->prepare($sql);
    $stmt->execute([$player['mlb'], $player['name']]);
	$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$streak = 0;
	$key = $player['mlb'] . ' ' . $player['name'];

	foreach ($games as $game) {
		if ($game['gs'] == 1) {
			if ($game['w'] == 1) {
				$streak++;
			} else if ($game['w'] == 0 && $game['l'] == 0) {
				$streak++;
			} else {
				$longestStreak[$key . " ending week {$game['week']}"] = $streak;
				$streak = 0;
			}
		}
	}

	if ($streak > 0) {
		unset($longestStreak[$key . " ending week {$game['week']}"]);
		$longestStreak[$key . " still active"] = $streak;
	}
}

arsort($longestStreak);
echo "Winning streaks for $year<br />";

foreach ($longestStreak as $player => $streak) {
	echo "$player - $streak games";
	echo "<br />";
}
