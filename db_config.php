<?php
require 'vendor/autoload.php';

use Aura\SqlQuery\QueryFactory;

$db = new QueryFactory(
    'pgsql',
    'host=localhost;dbname=ibl_stats',
    'stats',
    'st@ts=Fun'
);
