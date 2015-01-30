    <div align=center>
    <form action=<?= $_SERVER["PHP_SELF"];?> method="post">
    <input type=hidden name=get_team value=1>
    <table>
    <tr><td><input type=submit value="Choose A Team"></td>
    <td><select name="ibl_team">
<?php foreach ($ibl_team as $team) : ?>
        <option value="<?= $team;?>"><?= $team;?></option>
<?php endforeach; ?>
    </select></td></tr>
    </table>
    </form>
    </div>

