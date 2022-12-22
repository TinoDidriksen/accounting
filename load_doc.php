<?php
require_once __DIR__.'/inc/library.php';

$file = $argv[1];
if (!is_readable($file)) {
	echo "$file is not readable!\n";
	exit(-1);
}

$type = guess_file_type($file);
if (empty($type)) {
	echo "$file is not an accepted type!\n";
	exit(-1);
}

$data = file_get_contents($file);
$hash = sha1_base64_url($data);
$file = basename($file);

$m = array();
if (!preg_match('@^(\d+-\d+-\d+) -? ?(.+?)\.(\w+)$@u', $file, $m)) {
	echo "$file is not in 'y-m-d - name.suffix' format!\n";
	exit(-1);
}

$query = "INSERT INTO documents (doc_name, doc_mime, doc_date, doc_hash) VALUES (:doc_name, :doc_mime, :doc_date, :doc_hash) ON DUPLICATE KEY UPDATE doc_date=doc_date";
$stm = $GLOBALS['sql']->prepare($query);
$stm->execute(array(
	':doc_name' => $m[2],
	':doc_mime' => $type['mime'],
	':doc_date' => $m[1],
	':doc_hash' => $hash,
	));
$id = $GLOBALS['sql']->lastInsertId();

$query = "INSERT INTO document_data (doc_id, doc_data) VALUES (:doc_id, :doc_data) ON DUPLICATE KEY UPDATE doc_id=doc_id";
$stm = $GLOBALS['sql']->prepare($query);
$stm->execute(array(
	':doc_id' => $id,
	':doc_data' => $data,
	));
