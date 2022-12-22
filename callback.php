<?php
require_once __DIR__.'/inc/library.php';

while ($_REQUEST['a'] === 'add_to_business') {
	$id = intval($_REQUEST['id']);
	$vat = intval($_REQUEST['vat']);

	$GLOBALS['sql']->beginTransaction();

	$e = $GLOBALS['sql']->prepexec("SELECT * FROM entries WHERE entry_id = ?", [$id])->fetchAll();
	$e = $e[0];

	$GLOBALS['sql']->prepexec("DELETE FROM view_entries WHERE view_id = 1 AND entry_id = ?", [$id]);

	$ev = $e['entry_amount'] * 0.2 * $vat;
	$ea = $e['entry_amount'] - $ev;
	$GLOBALS['sql']->prepexec("INSERT INTO view_entries (view_id, entry_id, ve_amount, ve_vat) VALUES (1, ?, ?, ?)", [$id, $ea, $ev]);

	$GLOBALS['sql']->commit();
	break;
}

while ($_REQUEST['a'] === 'remove_from_business') {
	$id = intval($_REQUEST['id']);

	$GLOBALS['sql']->beginTransaction();
	$GLOBALS['sql']->prepexec("DELETE FROM view_entries WHERE view_id = 1 AND entry_id = ?", [$id]);
	$GLOBALS['sql']->commit();
	break;
}
