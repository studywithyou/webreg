<?hh

// script that commits the transactions that have been made

require 'bootstrap.php';
require 'db_config.php';

$db =& DB::connect(DSN);
$db->query('COMMIT');

echo '
<div align="center">
	Transactions committed!
	<hr>
    <a href=index.php>Return to main page</a>
';
