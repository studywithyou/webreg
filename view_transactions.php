<?hh
require 'bootstrap.php';
require 'db_config.php';
require 'templates/transactions/header.php';
$task="";

if (isset($_POST["task"])) $task=$_POST["task"];

if ($task=="show_report")
{
    $from_date=$_POST["from_year"]."-".$_POST["from_month"]."-".$_POST["from_day"];
    $to_date=$_POST["to_year"]."-".$_POST["to_month"]."-".$_POST["to_day"];

    if ($from_date>$to_date)
    {
        echo "
            <div align='center'>
            <font color='red'>The 'from' date cannot be newer than the 'to' date</font>
            </div>
            ";
        $task="";
    }
    else
    {
        // Collect all the transactions that took place in that date range
        $sql="SELECT ibl_team,log_entry FROM transaction_log WHERE (transaction_date >= ? AND transaction_date <= ?) ORDER BY ibl_team";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($from_date, $to_date));
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            foreach ($result as $row) {
                $ibl_team=$row['ibl_team'];
                $log_entry=$row['log_entry'];
                $transaction[$ibl_team][]=$log_entry;
            }
        }

        if (count($transaction)>0)
        {
            echo "Transactions for <b>$from_date></b> to <b>$to_date</b><br><br>
                <table>";

            foreach ($transaction as $ibl_team=>$key)
            {
                $printed_team_name=FALSE;

                foreach ($key as $log_entry)
                {
                    echo "<tr>";
                    if ($printed_team_name==FALSE)
                    {
                        echo "<tr><td>$ibl_team</td><td>-- " . stripslashes($log_entry) . "</td></tr>";
                        $printed_team_name=TRUE;
                    }
                    else
                    {
                        echo "<tr><td></td><td>-- " . stripslashes($log_entry) . "</td></tr>";
                    }
                }
            }
            echo "</table>";
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
    require 'templates/transactions/form.php';
}

require 'templates/transactions/footer.php';
