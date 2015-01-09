<?hh

// transaction_log.php

// Function that writes entries into the transaction log, so that
// a list of transactions can be generated later
function transaction_log($ibl_team, $log_entry, $db)
{
    $sql = "INSERT INTO transaction_log (ibl_team, log_entry, transaction_date)
        VALUES (?, ?. NOW())";
    $stmt = $db->prepare($sql);
    return $stmt->execute(array($ibl_team, $log_entry));

}
