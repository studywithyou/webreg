<?php

// Controller for main page
include 'DB.php';
include 'db_config.php';

$db = DB::connect(DSN);

// Grab all our models
include './models/injuries.php';
include './models/franchises.php';
include './models/games.php';

// Collect data
$franchiseModel = new Franchise($db);
$injuryModel = new Injury($db);
$injuries = $injuryModel->getAll($maxWeek);

$franchises = $franchiseModel->getAll();

// Display template
include './templates/injury_management.php';
