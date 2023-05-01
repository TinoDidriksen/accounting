<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<title>Tino Didriksen's Accounting Tools</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2/dist/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="static/style.css" type="text/css" rel="stylesheet">
</head>
<body class="container-fluid">
<?php
require_once __DIR__.'/inc/library.php';

if (!empty($_REQUEST['update'])) {
	update_balances();
}

$accounts = array();
$total = 0;
$query = "SELECT * FROM accounts ORDER BY acc_id ASC";
$stm = $GLOBALS['sql']->query($query);
while ($row = $stm->fetch()) {
	$accounts[$row['acc_id']] = $row;
	$total += $row['acc_balance'];
}
$stm = null;

$total = num_format($total);

echo <<<XOUT
<h1>Accounts</h1>
<table>
<thead>
<tr>
	<th>ID</th>
	<th>Name</th>
	<th class="right">Balance</th>
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
foreach ($accounts as $acc) {
	$acc['acc_name'] = htmlspecialchars($acc['acc_name']);
	$acc['acc_balance'] = num_format($acc['acc_balance']);
	echo <<<XOUT
<tr>
	<td>{$acc['acc_id']}</td>
	<td><a href="account.php?id={$acc['acc_id']}">{$acc['acc_name']}</a></td>
	<td class="right">{$acc['acc_balance']}</td>
</tr>
XOUT;
}
echo <<<XOUT
</tbody>
</table>
XOUT;


$views = array();
$query = "SELECT * FROM views ORDER BY view_id ASC";
$stm = $GLOBALS['sql']->query($query);
while ($row = $stm->fetch()) {
	$views[$row['view_id']] = $row;
}
$stm = null;

echo <<<XOUT
<h1>Views</h1>
<table>
<thead>
<tr>
	<th>ID</th>
	<th>Name</th>
</tr>
</thead>
<tbody>
XOUT;
foreach ($views as $view) {
	$view['view_name'] = htmlspecialchars($view['view_name']);
	echo <<<XOUT
<tr>
	<td>{$view['view_id']}</td>
	<td><a href="view.php?id={$view['view_id']}">{$view['view_name']}</a></td>
</tr>
XOUT;
}
echo <<<XOUT
</tbody>
</table>
XOUT;


$docs = array();
$query = "SELECT * FROM documents ORDER BY doc_date DESC, doc_id DESC LIMIT 20";
$stm = $GLOBALS['sql']->query($query);
while ($row = $stm->fetch()) {
	$docs[$row['doc_id']] = $row;
}
$stm = null;

echo <<<XOUT
<h1>Recent Documents</h1>
<table>
<thead>
<tr>
	<th>Date</th>
	<th>Name</th>
	<th>Download</th>
</tr>
</thead>
<tbody>
XOUT;
foreach ($docs as $doc) {
	$doc['doc_name'] = htmlspecialchars($doc['doc_name']);
	echo <<<XOUT
<tr title="ID {$doc['doc_id']}">
	<td>{$doc['doc_date']}</td>
	<td><a href="document.php?hash={$doc['doc_hash']}">{$doc['doc_name']}</a></td>
	<td><a href="download.php?hash={$doc['doc_hash']}">Download</a></td>
</tr>
XOUT;
}
echo <<<XOUT
</tbody>
</table>
XOUT;

?>
</body>
</html>
