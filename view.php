<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Tino Didriksen's Accounting Tools</title>
	<link href="static/style.css" type="text/css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="static/script.js"></script>
</head>
<body>
<?php
require_once __DIR__.'/inc/library.php';


$entries = array();
$totals = array();
$query = "SELECT * FROM view_entries NATURAL JOIN entries WHERE view_id = :view_id ORDER BY entry_date DESC, entry_id DESC";
$stm = $GLOBALS['sql']->prepare($query);
$stm->execute(array(
	':view_id' => $_REQUEST['id']
	));
while ($row = $stm->fetch()) {
	$month = substr($row['entry_date'], 0, 7);
	if (empty($totals[$month])) {
		$totals[$month] = ['mov' => 0, 'mov-vat' => 0, 'in' => 0, 'in-vat' => 0];
	}
	$entries[$month][] = $row;
	$totals[$month]['mov'] += $row['ve_amount'];
	$totals[$month]['mov-vat'] += $row['ve_vat'];

	if ($row['ve_amount'] > 0) {
		$totals[$month]['in'] += $row['ve_amount'];
		$totals[$month]['in-vat'] += $row['ve_vat'];
	}
}
$stm = null;

if (empty($entries)) {
	echo "Empty view!";
	exit(0);
}


$docs = array();
$query = "SELECT entry_id, documents.* FROM view_entries NATURAL JOIN entries NATURAL JOIN document_entries NATURAL JOIN documents WHERE view_id = :view_id ORDER BY doc_date DESC, doc_id DESC";
$stm = $GLOBALS['sql']->prepare($query);
$stm->execute(array(
	':view_id' => $_REQUEST['id']
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
	$total['ex'] = num_format($total['in'] - $total['mov']);
	$total['ex-vat'] = num_format($total['in-vat'] - $total['mov-vat']);
	$total['mov'] = num_format($total['mov']);
	$total['mov-vat'] = num_format($total['mov-vat']);
	$total['in'] = num_format($total['in']);
	$total['in-vat'] = num_format($total['in-vat']);

	echo <<<XOUT
	<form method="post" action="upload.php" enctype="multipart/form-data">
	<a name="{$m}"></a>
	<h2>{$m}</h2>
	<table>
	<thead>
	<tr>
		<th class="center">X</th>
		<th>Date</th>
		<th>Text</th>
		<th class="right italic smaller">(Amount)</th>
		<th class="right">Ex-VAT</th>
		<th class="right">VAT</th>
		<th class="right">Action</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th></th>
		<th></th>
		<th>Income</th>
		<th></th>
		<th class="right">{$total['in']}</th>
		<th class="right">{$total['in-vat']}</th>
		<th></th>
	</tr>
	<tr>
		<th></th>
		<th></th>
		<th>Expenses</th>
		<th></th>
		<th class="right">{$total['ex']}</th>
		<th class="right">{$total['ex-vat']}</th>
		<th></th>
	</tr>
	<tr>
		<th></th>
		<th></th>
		<th>Movement</th>
		<th></th>
		<th class="right">{$total['mov']}</th>
		<th class="right">{$total['mov-vat']}</th>
		<th></th>
	</tr>
	</tfoot>
	<tbody>
XOUT;
	foreach ($month as $entry) {
		$entry['entry_text'] = htmlspecialchars($entry['entry_text']);
		$entry['ve_comment'] = htmlspecialchars($entry['ve_comment']);
		$entry['entry_amount'] = num_format($entry['entry_amount']);
		$entry['ve_amount'] = num_format($entry['ve_amount']);
		$entry['ve_vat'] = num_format($entry['ve_vat']);
		if ($entry['ve_vat'] === '0.00') {
			$entry['ve_vat'] = '';
		}
		if ($entry['entry_amount'] === $entry['ve_amount']) {
			$entry['entry_amount'] = '';
		}
		else {
			$entry['entry_amount'] = '('.$entry['entry_amount'].')';
		}
		if (!empty($entry['ve_comment'])) {
			$entry['entry_text'] .= '<br/><span class="italic">'.$entry['ve_comment'].'</span>';
		}
		if (!empty($docs[$entry['entry_id']])) {
			foreach ($docs[$entry['entry_id']] as $doc) {
				$entry['entry_text'] .= '<br><span class="italic"><a href="document.php?hash='.$doc['doc_hash'].'">'.htmlspecialchars($doc['doc_name']).'</a> (<a href="download.php?hash='.$doc['doc_hash'].'" class="smaller">dl</a>)</span>';
			}
		}
		echo <<<XOUT
	<tr title="{$entry['entry_id']}">
		<td class="center"><input type="checkbox" name="entries[]" value="{$entry['entry_id']}"></td>
		<td>{$entry['entry_date']}</td>
		<td>{$entry['entry_text']}</td>
		<td class="right italic smaller">{$entry['entry_amount']}</td>
		<td class="right">{$entry['ve_amount']}</td>
		<td class="right">{$entry['ve_vat']}</td>
		<td class="right"><a href="#" onclick="removeFromBusiness(this); return false;">Rem</a></td>
	</tr>
XOUT;
	}
	echo <<<XOUT
	</tbody>
	</table>
	<input type="file" name="file">
	<input type="submit" value="Upload &amp; Attach">
	</form>
XOUT;
}

?>
</body>
</html>
