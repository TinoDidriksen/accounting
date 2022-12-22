<?php
require_once __DIR__.'/inc/library.php';

$doc = null;
$query = "SELECT * FROM documents NATURAL JOIN document_data WHERE doc_hash = :doc_hash";
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

$mime = suffix2mime($doc['doc_type']);

header('Content-Type: '.$mime);
header('Content-Length: '.strlen($doc['doc_data']));
if (empty($_REQUEST['embed'])) {
	header('Content-Disposition: attachment; filename="'.$doc['doc_date'].' - '.$doc['doc_name'].'.'.$doc['doc_type'].'"; creation-date="'.$doc['doc_date'].'"; size="'.strlen($doc['doc_data']).'";');
}

echo $doc['doc_data'];
