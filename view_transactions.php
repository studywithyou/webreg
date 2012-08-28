<?php

// view_transactions.php

// Generates a team-by-team transaction report based on a date range
?>
<html>
<head>
<title>WebReg -- View Transactions</title>
</head>
<body>
<h3 align="center">WebReg -- View Transactions</h3>
<?php

require_once 'DB.php';
require_once 'db_config.php';

$task="";

if (isset($_POST["task"])) $task=$_POST["task"];

if ($task=="show_report")
{
	$from_date=$_POST["from_year"]."-".$_POST["from_month"]."-".$_POST["from_day"];
	$to_date=$_POST["to_year"]."-".$_POST["to_month"]."-".$_POST["to_day"];
	
	if ($from_date>$to_date)
	{
		?>
		<div align="center">
		<font color="red">The "from" date cannot be newer than the "to" date</font>
		</div>
		<?php
		$task="";
	}
	else
	{
		// Collect all the transactions that took place in that date range
		$sql="SELECT ibl_team,log_entry FROM transaction_log WHERE (transaction_date>='$from_date' AND transaction_date<='$to_date') ORDER BY ibl_team";
		$db =& DB::connect(DSN);
		$result=$db->query($sql);

		if ($result!=FALSE)
		{
			
			while ($result->fetchInto($row,DB_FETCHMODE_ASSOC))
			{
				$ibl_team=$row['ibl_team'];
				$log_entry=$row['log_entry'];
				$transaction[$ibl_team][]=$log_entry;
			}
		}
		
		if (count($transaction)>0)
		{
			?>
			Transactions for <b><?php print $from_date;?></b> to <b><?php print $to_date;?></b><br><br>
			<table>
			<?php
			foreach ($transaction as $ibl_team=>$key)
			{
				$printed_team_name=FALSE;
				
				foreach ($key as $log_entry)
				{
					?>
					<tr>
					<?php
					if ($printed_team_name==FALSE)
					{
						?>
						<tr><td><?php print $ibl_team;?></td><td>-- <?php print stripslashes($log_entry);?></td></tr>
						<?php
						$printed_team_name=TRUE;
					}
					else
					{
						?>
						<tr><td></td><td>-- <?php print stripslashes($log_entry);?></td></tr>
						<?php
					}
				}
			}
			?>
			</table>
			<?php
		}
	}
}
		

if ($task=="")
{
	// Display range of years
	$year_dropdown = "";
	
	for ($x=2005;$x<=date('Y');$x++)
	{
	    if ($x == date('Y')) {
	        $year_dropdown .= "<option value=$x selected>" . $x . "</option>";
	    } else {
	        $year_dropdown .= "<option value=$x>".$x."</option>";
	    }
		
	}
	
	// Display range for months
	$month_dropdown="<option value='01'>January</option>";
	$month_dropdown.="<option value='02'>February</option>";
	$month_dropdown.="<option value='03'>March</option>";
	$month_dropdown.="<option value='04'>April</option>";
	$month_dropdown.="<option value='05'>May</option>";
	$month_dropdown.="<option value='06'>June</option>";
	$month_dropdown.="<option value='07'>July</option>";
	$month_dropdown.="<option value='08'>August</option>";
	$month_dropdown.="<option value='09'>September</option>";
	$month_dropdown.="<option value='10'>October</option>";
	$month_dropdown.="<option value='11'>November</option>";
	$month_dropdown.="<option value='12'>December</option>";
	
	// Display range for days
	$day_dropdown = "";
	
	for($x=1;$x<=31;$x++)
	{
		if ($x<10) $x="0".$x;
		$day_dropdown.="<option value='".$x."'>".$x."</option>";
	}
	
	// Now, show the form so they can pick a date range.
	?>
	<div align=center>
	<form action=<?php print $_SERVER["PHP_SELF"];?> method=post>
	<input name="task" type="hidden" value="show_report">
	Please select a date range for the report
	<table>
	<tr>
	<td><b>From</b></td>
	<td><select name="from_year"><?php print $year_dropdown;?></select></td>
	<td><select name="from_month"><?php print $month_dropdown;?></select></td>
	<td><select name="from_day"><?php print $day_dropdown;?></select></td>
	</tr>
	<tr>
	<td><b>To</b></td>
	<td><select name="to_year"><?php print $year_dropdown;?></select></td>
	<td><select name="to_month"><?php print $month_dropdown;?></select></td>
	<td><select name="to_day"><?php print $day_dropdown;?></select></td>
	</tr>
	<tr>
	<td colspan=4 align="center"><input type="submit" value="Run Report"></td>
	</tr>
	</table>
	</form>
	<?php
}


?>	
<hr>
<div align="center"><a href="index.php">Return To WebReg Home Page</a></div>
</body>
</html>
