	<div align=center>
	<form action=<?php print $_SERVER["<?php_SELF"];?> method=post>
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
