<form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="task" value="do_draft">
<input type="hidden" name="id" value="<?php print $id ;?>">
<input type="hidden" name="tig_name" value="<?php print $tig_name; ?>">
<div align="center">
<table>
<tr>
<td><?php print $tig_name; ?></td>
<td><select name="ibl_team">
<?php foreach ($team_list as $ibl_team) : ?>
    <option value="<?php print $ibl_team; ?>"><?php print $ibl_team; ?></option>\n
<?php endforeach; ?>
                                </select>
                                </td>
                                <td><select name="round">
<?php foreach ($round as $value) : ?>
    <option value="<?php print $value; ?>"><?php print "{$value} Round ".YEAR; ?></option>\n
<?php endforeach; ?>
</select>
</td>
<td><input type="submit" value="Draft Player"></td>
</tr>
</table>
</div>
</form>
