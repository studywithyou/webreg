<?hh
require 'bootstrap.php';

// Controller for main page
require 'vendor/autoload.php';
require 'db_config.php';


// Grab all our models
require './models/injuries.php';
require './models/franchises.php';
require './models/games.php';

// Collect data
$franchiseModel = new Franchise($db);
$injuryModel = new Injury($db);
$gameModel = new Game($db);
$maxWeek = $gameModel->getMaxWeek();
$injuries = $injuryModel->getAll($maxWeek);

$franchises = $franchiseModel->getAll();

// Display template
require './templates/injury_management.php';
