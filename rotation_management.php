<?php
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

require 'vendor/autoload.php';

require 'db_config.php';
require 'models/franchises.php';
require 'models/games.php';
require 'models/rotations.php';
require 'RotationAdapter.php';

$franchiseModel = new Franchise($db);
$rotationModel = new Rotation($db);
$games = new Game($db);

$current_week = filter_input(INPUT_GET, 'week', FILTER_SANITIZE_NUMBER_INT);
$max_week = $games->getMaxWeek();

if ($current_week == 0) {
    $current_week = $max_week;
}

$franchises = $franchiseModel->getAll();
$rotations = $rotationModel->getAll();

/**
 * We need to add an empty set of rotations to our existing list for the week
 * after the most current ones we have
 */
$rotation_max_week = $rotationModel->getMaxWeek();
$rotations = array_merge($rotations, $rotationModel->addWeek($rotation_max_week + 1, $franchises));

// load all rotations into the pager
$adapter = new RotationAdapter($rotations, $franchises);
$adapter->processByWeek();
$pagerfanta = new Pagerfanta($adapter);
$pagerfanta->setMaxPerPage(24);
$pagerfanta->setCurrentPage($current_week);
$nb_results = $pagerfanta->getNbResults();
$current_page_results = $pagerfanta->getCurrentPageResults();

// display form with data
require 'templates/rotations/index.php';
