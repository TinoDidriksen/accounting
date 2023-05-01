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

echo '<datalist id="eclass">'."\n";
$rows = $GLOBALS['sql']->prepexec("SELECT * FROM entry_classes ORDER BY c_id")->fetchAll();
foreach ($rows as $row) {
	echo '<option value="'.$row['c_id'].'">'.$row['c_id'].' '.htmlspecialchars($row['c_name']).'</option>'."\n";
}
foreach ($rows as $row) {
	echo '<option value="'.$row['c_id'].'">'.htmlspecialchars($row['c_name']).' ('.$row['c_id'].')</option>'."\n";
}
echo '</datalist>'."\n";

$entries = array();
$totals = array();
$query = "SELECT * FROM account_entries NATURAL JOIN entries WHERE entry_date >= date('now', 'start of year', '-3 year') AND acc_id = :acc_id ORDER BY entry_date DESC, entry_id DESC";
$stm = $GLOBALS['sql']->prepare($query);
$stm->execute(array(
	':acc_id' => $_REQUEST['id']
	));
while ($row = $stm->fetch()) {
	$month = substr($row['entry_date'], 0, 7);
	if (empty($totals[$month])) {
		$totals[$month] = 0;
	}
	$entries[$month][] = $row;
	$totals[$month] += $row['entry_amount'];
}
$stm = null;

if (empty($entries)) {
	echo "Empty account!";
	exit(0);
}


$docs = array();
$query = "SELECT entry_id, documents.* FROM account_entries NATURAL JOIN entries NATURAL JOIN document_entries NATURAL JOIN documents WHERE acc_id = :acc_id ORDER BY doc_date DESC, doc_id DESC";
$stm = $GLOBALS['sql']->prepare($query);
$stm->execute(array(
	':acc_id' => $_REQUEST['id']
	));
while ($row = $stm->fetch()) {
	if (empty($docs[$row['entry_id']])) {
		$docs[$row['entry_id']] = array();
	}
	$docs[$row['entry_id']][] = $row;
}
$stm = null;


echo <<<XOUT
<h2>Jump to...</h2>
<ul>
XOUT;
foreach ($totals as $m => $t) {
	echo <<<XOUT
	<li><a href="#{$m}">{$m}</a></li>
XOUT;
}
echo <<<XOUT
</ul>
XOUT;

foreach ($entries as $m => $month) {
	$total = $totals[$m];
	$total = num_format($total);
	echo <<<XOUT
	<a name="{$m}"></a>
	<h2>{$m}</h2>
	<table>
	<thead>
	<tr>
		<th>Date</th>
		<th>Text</th>
		<th class="right">Amount</th>
		<th class="right">Balance</th>
		<th class="right">Action</th>
		<th class="right">Class</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th></th>
		<th>Movement</th>
		<th class="right">{$total}</th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
	</tfoot>
	<tbody>
XOUT;
	foreach ($month as $entry) {
		$entry['entry_text'] = htmlspecialchars($entry['entry_text']);
		$entry['entry_amount'] = num_format($entry['entry_amount']);
		$entry['entry_balance'] = num_format($entry['entry_balance']);
		if (!empty($docs[$entry['entry_id']])) {
			foreach ($docs[$entry['entry_id']] as $doc) {
				$entry['entry_text'] .= '<br><a href="document.php?hash='.$doc['doc_hash'].'" class="italic">'.htmlspecialchars($doc['doc_name']).'</a> (<a href="download.php?hash='.$doc['doc_hash'].'" class="smaller italic">download</a>)';
			}
		}
		if (empty($entry['entry_class'])) {
			$entry['entry_class'] = '';
		}
		echo <<<XOUT
	<tr title="{$entry['entry_id']}">
		<td>{$entry['entry_date']}</td>
		<td>{$entry['entry_text']}</td>
		<td class="right">{$entry['entry_amount']}</td>
		<td class="right">{$entry['entry_balance']}</td>
		<td class="right"><a href="#" onclick="addToBusiness(this, true); return false;">With</a>, <a href="#" onclick="addToBusiness(this, false); return false;">w/o</a></td>
		<td class="right"><input value="{$entry['entry_class']}" list="eclass" class="eclass"></td>
	</tr>
XOUT;
	}
	echo <<<XOUT
	</tbody>
	</table>
XOUT;
}

?>
</body>
</html>
