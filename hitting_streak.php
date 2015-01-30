<?hh
require './bootstrap.php';
require './db_config.php';
$year = 'bat' . trim($_GET['year']);

$sql = "SELECT mlb,name FROM {$year} GROUP BY mlb,name";
$stmt = $db->prepare($sql);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
$longestStreak = Map {};

foreach ($players as $player) {
    $streak = 0;
    $sql = "SELECT h, ab, bb, week FROM {$year} WHERE mlb = ? AND name = ? ORDER BY week, home, away";
    $stmt = $db->prepare($sql);
    $stmt->execute([$player['mlb'], $player['name']]);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$key = $player['mlb'] . ' ' . $player['name'];

    foreach ($games as $game) {
		if ($game['h'] > 0 || ($game['ab'] = 0 && $game['bb'] > 0)) {
			$streak++;
        } else {
            $p = Pair {"{$key} ending week {$game['week']}", $streak};
            $longestStreak = $longestStreak->add($p);
			$streak = 0;
		}
	}

    if ($streak > 0) {
        $longestStreak->remove("{$key} ending week {$game['week']}");
        $p = Pair {"{$key} stil active", $streak};
        $longestStreak->add($p);
    }
}

$streaks = $longestStreak->filterWithKey(function($k, $v) {
    return $v >= 10;
});

arsort($streaks);
echo "Hitting streaks for $year<br />";

foreach ($streaks as $player => $streak) {
	echo "$player - $streak games";
	echo "<br />";
}
