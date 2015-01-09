<?hh

// import_rosters.<?hh

// Import a roster file based on Rusty's work.
require 'bootstrap.php';
require './templates/import_rosters_header.php';

require 'db_access_class.inc';
require 'iterator_class.inc';

$task="";

if (isset($_POST["task"])) $task=$_POST["task"];

if ($task=="")
{
    require './templates/import_rosters_default.php';
}

if ($task=="upload")
{
    $db=new DB_Access();
    $db->Choose_DB("chartjes");

    $fp=fopen($_FILES["upfile"]["tmp_name"],"r");
    $x=0;

    while (!feof($fp))
    {
        $x++;
        $data=fgets($fp,1024);
        $roster_data=explode(",",$data);
        $comments=preg_replace("/\"/","",$roster_data[2]);
        $ibl_team=preg_replace("/\"/","",$roster_data[4]);
        $tig_name=trim($roster_data[5]." ".$roster_data[6]);
        $tig_name=preg_replace("/\'/","`",$tig_name);

        if ($ibl_team!="" && strpos($ibl_team,"UC")===FALSE) $pick_team=$ibl_team;

        // Let's check to see if we found a draft pick
        if (preg_match("/\\d\-\d/",$comments)==FALSE)
        {
            // Let's look for an uncarded players
            if (strpos($ibl_team,"UC")>0)
            {
                list($ibl_team,$ucyear)=split("-",$ibl_team);
                $ibl_team=trim($ibl_team);
                $ucyear=trim($ucyear);
                $comments="[$ucyear] ".$comments;
            }

            if ($tig_name!="")
            {
                // Build query to insert a record into the rosters database
                $sql="INSERT INTO rosters (tig_name,ibl_team,comments,type,status)";
                $sql.=" VALUES ('$tig_name','$ibl_team','$comments',1,1)";
                $db->Run_Query($sql);
                print "Imported $tig_name<br>";
            }
        }
    }

    print "Added $x players to the roster file<br>";
}
