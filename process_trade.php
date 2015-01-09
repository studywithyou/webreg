<?hh

require 'bootstrap.php';
require 'DB.php';
require 'db_config.php';

// Process the incoming data that we got from our trade page
require './models/rosters.php';

$rosterModel = new Roster($db);

// Grab the data that's been posted into here
$team1 = filter_input(INPUT_POST, 'team1', FILTER_SANITIZE_ENCODED);
$team2 = filter_input(INPUT_POST, 'team2', FILTER_SANITIZE_ENCODED);
$data1 = filter_input(INPUT_POST, 'data1', FILTER_SANITIZE_ENCODED);
$data2 = filter_input(INPUT_POST, 'data2', FILTER_SANITIZE_ENCODED);

// Update roster entries with new updated teams
foreach ($data1 as $playerInfo) {
	list($dataSet, $playerId) = explode('_', $playerInfo);
	$rosterModel->updatePlayerTeam($team1, $playerId);
}

foreach ($data2 as $playerInfo) {
	list($dataSet, $playerId) = explode('_', $playerInfo);
	$rosterModel->updatePlayerTeam($team2, $playerId);
}
