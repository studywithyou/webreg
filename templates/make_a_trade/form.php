<div align=center>
<form action=<?php print $_SERVER["PHP_SELF"];?> method=POST>
<input type="hidden" name="task" value="do_trade">
<input type="hidden" name="team1" value="<?php print $team1;?>">
<input type="hidden" name="team2" value="<?php print $team2;?>">
<table>
<tr>
<td align=center><b><?php print $team1;?></td>
<td align=center><b><?php print $team2;?></td>
</tr>
<tr>
<td><?php print $team1_dropdown;?></td>
<td><?php print $team2_dropdown;?></td>
</tr>
<tr>
<td align=center colspan=2><input type="submit" value="Make Trade"></td>
</tr>
</table>
</form>
</div>

