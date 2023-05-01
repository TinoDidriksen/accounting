<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Tino Didriksen's Accounting Tools</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2/dist/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="static/style.css" type="text/css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.6/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2/dist/js/bootstrap.min.js"></script>
	<script src="static/script.js"></script>
</head>
<body class="container-fluid">
<?php
require_once __DIR__.'/inc/library.php';

$clss = [];
$stm = $GLOBALS['sql']->prepexec("SELECT * FROM entry_classes WHERE c_id != 0 ORDER BY c_id");
while ($row = $stm->fetch()) {
	$clss[$row['c_id']] = $row['c_name'];
}
$clss[9998] = 'Subtotal';
$clss[9999] = 'Løbende sum';

$yms = [];
$rows = $GLOBALS['sql']->prepexec("SELECT strftime('%Y-%m', entry_date) as ym, entry_class as class, sum(entry_amount) as amount FROM entries WHERE entry_date >= date('now', 'start of year', '-1 year') AND entry_class != 0 GROUP BY ym, entry_class ORDER BY ym ASC, entry_class ASC")->fetchAll();
foreach ($rows as $row) {
	$yms[$row['ym']][$row['class']] = $row['amount'];
}

$last_sum = 0;
$last_group = 1;
$sums = [];
echo '<table><tr><th>Type</th>';
foreach ($yms as $ym => $_) {
	echo "<th>{$ym}</th>";
	$yms[$ym][9998] = 0;
	$yms[$ym][9999] = 0;
}
echo '</tr>';
foreach ($clss as $cid => $cname) {
	if (intval($cid/1000) != $last_group) {
		echo '<tr><th>---- Sum</th>';
		foreach ($sums as $sum) {
			echo '<th class="right">'.num_format_null($sum).'</th>';
		}
		echo '</tr>';
		echo '<tr><td colspan="20" class="bg-white">&nbsp;</td></tr>';
		$last_group = intval($cid/1000);
		$sums = [];
	}

	echo '<tr><th>'.str_pad($cid, 4, '0', STR_PAD_LEFT).' '.htmlspecialchars($cname).'</th>';
	foreach ($yms as $ym => $es) {
		if (!array_key_exists($cid, $es)) {
			$es[$cid] = 0;
		}
		if ($cid < 9000) {
			$sums[$ym] = ($sums[$ym] ?? 0) + $es[$cid];
			$yms[$ym][9998] += $es[$cid];
			$yms[$ym][9999] += $es[$cid];
		}
		if ($cid == 9999) {
			$es[$cid] += $last_sum;
			$last_sum = $es[$cid];
		}
		echo '<td class="right">'.num_format_null($es[$cid]).'</td>';
	}
	echo '</tr>';
}
echo '</table>';

?>
</body>
</html>
