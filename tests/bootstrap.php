<?php

// Add our error handler
include '../error_handler.php';

// Bootstrap things for our test environment
include '../vendor/autoload.php';

// Grab our models that we created
include '../models/franchises.php';
include '../models/games.php';
include '../models/injuries.php';
include '../models/rosters.php';

// Grab our object we use for Mocking PDO stuff
include './pdo_double.php';
