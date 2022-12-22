<?php
require_once __DIR__.'/inc/library.php';

$docs = array();
$query = "SELECT * FROM documents";
$stm = $GLOBALS['sql']->query($query);
while ($row = $stm->fetch()) {
	$docs[] = $row;
}
$stm = null;

$GLOBALS['sql']->beginTransaction();

$query = "UPDATE documents SET doc_hash = :doc_hash WHERE doc_id = :doc_id";
$stm = $GLOBALS['sql']->prepare($query);
foreach ($docs as $doc) {
	$hash = hex2bin($doc['doc_hash']);
	$hash = base64_encode($hash);
	$stm->execute(array(
		':doc_id' => $doc['doc_id'],
		':doc_hash' => $hash,
		));
}

$GLOBALS['sql']->commit();
