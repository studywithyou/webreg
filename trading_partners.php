<?hh

// Controller for page that allows user to select their trading partners
require 'bootstrap.php';
require 'db_config.php';

// Grab all our models
require './models/franchises.php';

$franchiseModel = new Franchise($db);
$franchises = $franchiseModel->getAll();

require './templates/trading_partners.php';
