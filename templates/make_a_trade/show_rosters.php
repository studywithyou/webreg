        <div align=center>
        <form action=<?php print $_SERVER["PHP_SELF"];?> method="POST">
        <input name="task" type="hidden" value="show_rosters">
        <select name="team1">
                <?php print $team_option;?>
        </select>
        <select name="team2">
                <?php print $team_option;?>
        </select>
        <br>
        <input type="submit" value="Use These Teams">
        </form>
        </div>

