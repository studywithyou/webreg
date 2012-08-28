<?php

// transaction_log.php

// Function that writes entries into the transaction log, so that
// a list of transactions can be generated later

require_once 'DB.php';
require_once 'db_config.php';

function transaction_log($ibl_team,$log_entry)
{
	$db = DB::connect(DSN);
	$sth = $db->prepare('INSERT INTO transaction_log(ibl_team, log_entry, transaction_date) VALUES(?, ?, NOW())');
	$db->execute($sth, array($ibl_team, $log_entry));
}
