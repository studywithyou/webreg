<?php

// transaction_log.php

// Function that writes entries into the transaction log, so that
// a list of transactions can be generated later

require_once 'db_config.php';

function transaction_log($ibl_team, $log_entry, $db)
{
    $insert = $db->newInsert();
    $insert->into('transaction_log')
        ->cols(['ibl_team', 'log_entry', 'transaction_date'])
        ->set('transaction_date', 'NOW()');
    $bind = [
        'ibl_team' => $ibl_team,
        'log_entry' => $log_entry
    ];
    return $db->query($insert, $bind);
}
