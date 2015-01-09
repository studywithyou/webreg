<?hh
require 'bootstrap.php';
require 'db_config.php';
require 'models/franchises.php';
require 'models/rosters.php';

require 'templates/make_a_trade/header.php';
$task="";

if (isset($_POST["task"])) {
    $task=$_POST["task"];
}
if ($task=="show_rosters") {
    // Make sure they didn't pick the same team for both parties
    $team1=$_POST["team1"];
    $team2=$_POST["team2"];

    if ($team1 == $team2) {
        echo "<div align=center><font color=red>You must pick two different teams!</font></div>";
        $task="";
    } else {
        // Okay, let's show the rosters so we can do a trade
        $rosterModel = new Roster($db);
        $roster1 = $rosterModel->getByNickname($team1);
        $roster2 = $rosterModel->getByNickname($team2);

        foreach ($roster1 as $item) {
            $team1_list[] = $item['tig_name'];
        }

        foreach ($roster2 as $item) {
            $team2_list[] = $item['tig_name'];
        }

        $t1_size=count($team1_list);
        $t2_size=count($team2_list);

        if ($t1_size>$t2_size) {
            $dropdown_size=$t1_size;
        } else {
            $dropdown_size=$t2_size;
        }

        $team1_dropdown="<select multiple name=team1_trade[] size=$dropdown_size><br>";

        foreach ($team1_list as $player) {
            $team1_dropdown .=
                '<option value="' . $player . '">' . $player . '</option><br>';
        }

        $team1_dropdown.="</select>";

        $team2_dropdown="<select multiple name=team2_trade[] size=$dropdown_size><br>";

        foreach ($team2_list as $player) {
            $team2_dropdown .=
                '<option value="' . $player . '">' . $player . '</option><br>';
        }

        $team2_dropdown.="</select>";

        // Let's display the form to do the trade
        include 'templates/make_a_trade/form.php';
    }
}

if ($task=="do_trade")
{
    if (isset($_POST['team1_trade'])) $team1_trade=$_POST["team1_trade"];
    if (isset($_POST['team2_trade'])) $team2_trade=$_POST["team2_trade"];
    if (isset($_POST['team1'])) $team1=$_POST["team1"];
    if (isset($_POST['team2'])) $team2=$_POST["team2"];

    if (isset($team1_trade)) {
        foreach ($team1_trade as $rawPlayer) {
            $player = stripslashes($rawPlayer);
            $team1_trade_players[]=$player;
            $trade_date = date("m/y");
            $comments = "Trade {$team1} {$trade_date}";
            $sth = $db->prepare("UPDATE teams SET ibl_team = ?, comments = ?, status = 2 WHERE tig_name = ?");
            $data = array($team2, $comments, $player);
            $sth->execute($data);
        }
    }

    if (isset($team2_trade)) {
        foreach ($team2_trade as $rawPlayer) {
            $player = stripslashes($rawPlayer);
            $team2_trade_players[]=$player;
            $trade_date = date("m/y");
            $comments = "Trade {$team2} {$trade_date}";
            $sth = $db->prepare("UPDATE teams SET ibl_team = ?, comments = ?, status = 2 WHERE tig_name = ?");
            $sth->execute(array($team1, $comments, $player));
        }
    }

    $team1_trade_report = "";
    $team2_trade_report = "";

    if (isset($team1_trade_players)) $team1_trade_report=implode(", ",$team1_trade_players);
    if (isset($team2_trade_players)) $team2_trade_report=implode(", ",$team2_trade_players);

    $team1_transaction="Trades {$team1_trade_report} to {$team2} for {$team2_trade_report}";
    $team2_transaction="Trades {$team2_trade_report} to {$team1} for {$team1_trade_report}";
    require_once 'transaction_log.php';
    transaction_log($team1,$team1_transaction);
    transaction_log($team2,$team2_transaction);

    print "<div align=center><b>$team1</b> trades $team1_trade_report to <b>$team2</b> for $team2_trade_report<br></div>";

}

if ($task=="") {
    echo "<div align=center>Please select two teams for the trade</div>";
    $franchise = new Franchise($db);

    $ibl_team = $franchise->getAll();
    $team_option="";

    foreach ($ibl_team as $team)
    {
        $team_option.="<option value='$team'>$team</option>\n";
    }

    include 'templates/make_a_trade/show_rosters.php';
}

include 'templates/make_a_trade/footer.php';
