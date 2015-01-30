<?hh
$sql = "SELECT DISTINCT(ibl_team) FROM teams ORDER BY ibl_team";
$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$ibl_team = Vector {};

if ($results != false) {
    foreach ($results as $row) {
        $ibl_team->add($row['ibl_team']);
    }
}

include './templates/modify_roster/show_form.php';
