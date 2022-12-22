<?php
require_once __DIR__.'/inc/library.php';

$db = new \TDC\PDO\SQLite('acc.sqlite');
$db->beginTransaction();
$ins = $db->prepare("INSERT INTO document_data (doc_id, doc_data) VALUES (?, ?)");

$res = $GLOBALS['sql']->prepexec("SELECT doc_id, doc_data FROM document_data ORDER BY doc_id");
while ($row = $res->fetch()) {
	$ins->execute(array_values($row));
}

$db->commit();
