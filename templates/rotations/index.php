<html>
<head>
<title>WebReg - Rotation Management</title>
</head>
<body>
<h1 align='center'>Rotation Management</h1>
<hr>
<p>
<h3 align="center">Rotations for week <?= $current_week ?></h3>
<form action = "rotation_update.php" method = "post">
<input type="hidden" name="week" value=<?= $current_week ?>>
<table align="center">
<tr>
<th>Team</th>
<th>Rotation</th>
</tr>
<?php foreach ($current_page_results as $result) : ?>
<tr>
    <td><input name=franchise_id[] type="hidden" value=<?= $result['franchise_id'] ?> length=200><?= $franchises[$result['franchise_id']] ?></td>
    <td><input name=rotation[] type="text" size=75 value="<?= $result['rotation'] ?>"></td>
</tr>
<input name="new[]" type="hidden" value=<?= (int)($result['rotation'] == null) ?>>
<?php endforeach; ?>
<tr>
<td>
<?php if ($pagerfanta->hasPreviousPage()) : ?>
<a href="rotation_management.php?week=<?= $pagerfanta->getPreviousPage() ?>">Prev</a>
<?php endif; ?>
</td>
<td>
<?php if ($pagerfanta->hasNextPage()) : ?>
<a href="rotation_management.php?week=<?= $pagerfanta->getNextPage() ?>">Next</a>
<?php endif; ?>
<input type="submit" value="Save">
</td
</tr>
</table>
</form>
</p>
<hr>
<p align="center">
<a href="index.php">Return to main page</a>
</p>
</body>

