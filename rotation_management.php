<?php
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

require 'vendor/autoload.php';

require 'db_config.php';
require 'models/franchises.php';
require 'models/games.php';
require 'models/rotations.php';
require 'RotationAdapter.php';

// find out current week
$current_week = filter_input(INPUT_GET, 'week', FILTER_SANITIZE_NUMBER_INT);
$games = new Game($db);
$max_week = $games->getMaxWeek();

if ($current_week == 0) {
    $current_week = $max_week;
}

// load all rotations into the pager
$franchiseModel = new Franchise($db);
$franchises = $franchiseModel->getAll();
$rotationModel = new Rotation($db);
$rotations = $rotationModel->getAll();

$adapter = new RotationAdapter($rotations, $franchises);
$adapter->processByWeek();
$pagerfanta = new Pagerfanta($adapter);
$pagerfanta->setMaxPerPage(24);
$pagerfanta->setCurrentPage($current_week);
$nb_results = $pagerfanta->getNbResults();
$current_page_results = $pagerfanta->getCurrentPageResults();

// display form with data
require 'templates/rotations/index.php';