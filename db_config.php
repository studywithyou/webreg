<?php
$connection_factory = include 'vendor/aura/sql/scripts/instance.php';
$db = $connection_factory->newInstance(
    'pgsql',
    'host=localhost;dbname=ibl_stats',
    'stats',
    'st@ts=Fun'
);
