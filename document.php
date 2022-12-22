<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Tino Didriksen's Accounting Tools</title>
	<link href="static/style.css" type="text/css" rel="stylesheet">
</head>
<body>
<?php
require_once __DIR__.'/inc/library.php';

$doc = null;
$query = "SELECT * FROM documents WHERE doc_hash = :doc_hash";
$stm = $GLOBALS['sql']->prepare($query);
$stm->execute(array(
	':doc_hash' => $_REQUEST['hash']
	));
while ($row = $stm->fetch()) {
	$doc = $row;
}
$stm = null;

if (empty($doc)) {
	echo "No such document!";
	exit(0);
}

$name = htmlspecialchars($doc['doc_name']);
echo <<<XOUT
<h1>Document: {$name}</h1>
Download: <a href="download.php?hash={$doc['doc_hash']}">{$doc['doc_date']} - {$name}.{$doc['doc_type']}</a>
XOUT;


$entries = array();
$total = 0;
$query = "SELECT * FROM document_entries NATURAL JOIN entries WHERE doc_id = :doc_id ORDER BY entry_date DESC, entry_id DESC";
$stm = $GLOBALS['sql']->prepare($query);
$stm->execute(array(
	':doc_id' => $doc['doc_id']
	));
while ($row = $stm->fetch()) {
	$entries[] = $row;
	$total += $row['entry_amount'];
}
$stm = null;

if (!empty($entries)) {
	$total = num_format($total);
	echo <<<XOUT
	<h2>Entries</h2>
	<table>
	<thead>
	<tr>
		<th>Date</th>
		<th>Text</th>
		<th class="right">Amount</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th></th>
		<th>Total</th>
		<th class="right">{$total}</th>
	</tr>
	</tfoot>
	<tbody>
XOUT;
	foreach ($entries as $entry) {
		$entry['entry_text'] = htmlspecialchars($entry['entry_text']);
		$entry['entry_amount'] = num_format($entry['entry_amount']);
		echo <<<XOUT
	<tr title="ID {$entry['entry_id']}">
		<td>{$entry['entry_date']}</td>
		<td>{$entry['entry_text']}</td>
		<td class="right">{$entry['entry_amount']}</td>
	</tr>
XOUT;
	}
	echo <<<XOUT
	</tbody>
	</table>
XOUT;
}

$hash = htmlspecialchars($_REQUEST['hash']);
$mime = suffix2mime($doc['doc_type']);
$width = '';
if ($doc['doc_type'] === 'pdf') {
	$width = ' width="100%"';
}

echo <<<XOUT
<h2>View</h2>
<embed src="download.php?hash={$hash}&amp;embed=1" type="{$mime}" height="600"{$width}>
XOUT;

?>
</body>
</html>
