<?php
require_once __DIR__.'/../vendor/autoload.php';

$db = $GLOBALS['sql'] = new \TDC\PDO\SQLite(__DIR__.'/../data.sqlite');
$db->exec("PRAGMA journal_mode = MEMORY");
$db->exec("PRAGMA locking_mode = EXCLUSIVE");

function num_format($num) {
	$num = number_format($num, 2, '.', '');
	if ($num < 0) {
		$num = '<span class="negative">'.$num.'</span>';
	}
	return $num;
}

function num_format_null($num, $null='&nbsp;') {
	if (empty($num)) {
		return $null;
	}
	return num_format($num);
}

function msc_convert_num($amount) {
	if (preg_match('@\( ([^)]+)\)@u', $amount, $m)) {
		$amount = $m[1];
	}
	else {
		$amount = '-'.$amount;
	}
	$amount = str_replace(',', '', $amount);
	return $amount;
}

function sha1_base64_url($data) {
	$hash = sha1($data, true);
	$hash = base64_encode($hash);
	$hash = str_replace('+', '-', $hash);
	$hash = str_replace('/', '_', $hash);
	$hash = trim($hash, '=');
	return $hash;
}

function suffix2mime($suffix) {
	$mime = 'application/x-download';
	if ($suffix === 'pdf') {
		$mime = 'application/pdf';
	}
	else if ($suffix === 'jpg') {
		$mime = 'image/jpeg';
	}
	return $mime;
}

function guess_file_type($f) {
	$type_raw = trim(shell_exec('file '.escapeshellarg($f)));
	$type = trim(preg_replace('@^[^:]+:\s*@u', '', $type_raw));
	$mime_raw = trim(shell_exec('file -i '.escapeshellarg($f)));
	$mime = trim(preg_replace('@^[^:]+:\s*([^;]+).*$@u', '\1', $mime_raw));

	if (strpos($mime, 'message/rfc822') !== false) {
		return array('mime' => $mime, 'suf' => 'eml');
	}
	if (strpos($mime, 'image/jpeg') !== false) {
		return array('mime' => $mime, 'suf' => 'jpg');
	}
	if (strpos($mime, 'image/png') !== false) {
		return array('mime' => $mime, 'suf' => 'png');
	}
	if (strpos($type, 'OpenDocument Text') !== false) {
		return array('mime' => $mime, 'suf' => 'odt');
	}
	if (strpos($type, 'OpenOffice.org 1.x Writer') !== false) {
		return array('mime' => $mime, 'suf' => 'sxw');
	}
	if (strpos($type, 'OpenDocument Presentation') !== false) {
		return array('mime' => $mime, 'suf' => 'odp');
	}
	if (strpos($mime, 'application/msword') !== false) {
		return array('mime' => $mime, 'suf' => 'doc');
	}
	if (strpos($type, 'Microsoft Office Document') !== false) {
		$s = trim(shell_exec('strings -n10 '.escapeshellarg($f).' | egrep "PowerPoint|Word Document"'));
		if (strpos($s, 'Microsoft Office PowerPoint') !== false) {
			return array('mime' => $mime, 'suf' => 'ppt');
		}
		if (strpos($s, 'Microsoft Office Word Document') !== false) {
			return array('mime' => $mime, 'suf' => 'doc');
		}
		$s = trim(shell_exec('strings -n10 -el '.escapeshellarg($f).' | egrep "PowerPoint|WordDocument"'));
		if (strpos($s, 'PowerPoint Document') !== false) {
			return array('mime' => $mime, 'suf' => 'ppt');
		}
		if (strpos($s, 'WordDocument') !== false) {
			return array('mime' => $mime, 'suf' => 'doc');
		}
	}
	if (strpos($mime, 'text/rtf') !== false) {
		return array('mime' => $mime, 'suf' => 'rtf');
	}
	if (strpos($type, '(Corel/WP)') !== false) {
		return array('mime' => $mime, 'suf' => 'wpd');
	}
	if (strpos($mime, 'text/html') !== false) {
		return array('mime' => $mime, 'suf' => 'html');
	}
	if (strpos($mime, 'application/pdf') !== false) {
		return array('mime' => $mime, 'suf' => 'pdf');
	}
	if (strpos($mime, 'application/postscript') !== false) {
		return array('mime' => $mime, 'suf' => 'ps');
	}
	if (strpos($mime, 'application/zip') !== false) {
		$l = shell_exec('unzip -l '.escapeshellarg($f));
		if (strpos($l, 'ppt/presentation.xml') !== false) {
			return array('mime' => $mime, 'suf' => 'pptx');
		}
		if (strpos($l, 'word/document.xml') !== false) {
			return array('mime' => $mime, 'suf' => 'docx');
		}
		if (strpos($l, 'content.xml') !== false) {
			return array('mime' => $mime, 'suf' => 'odt');
		}
	}
	if (strpos($mime, 'text/plain') !== false) {
		return array('mime' => $mime, 'suf' => 'txt');
	}
	if (strpos($mime, 'text/x-') !== false) {
		return array('mime' => $mime, 'suf' => 'txt');
	}
	if (strpos($mime, 'application/xml') !== false) {
		if (intval(trim(shell_exec('grep -i TRADOStag '.escapeshellarg($f).' | wc -l'))) > 0) {
			return array('mime' => $mime, 'suf' => 'ttx');
		}
	}
	return null;
}

function update_balances() {
	$aces = array();
	$query = "SELECT acc_id, entry_id, entry_amount FROM accounts NATURAL JOIN account_entries NATURAL JOIN entries ORDER BY entry_id DESC";
	$stm = $GLOBALS['sql']->query($query);
	while ($row = $stm->fetch()) {
		if (empty($aces[$row['acc_id']])) {
			$aces[$row['acc_id']] = 0;
		}
		$aces[$row['acc_id']] += $row['entry_amount'];
	}
	unset($stm);

	$query = "UPDATE accounts SET acc_balance = :sum + acc_initial WHERE acc_id = :id";
	$stm = $GLOBALS['sql']->prepare($query);
	foreach ($aces as $id => $sum) {
		$stm->execute([':id' => $id, ':sum' => $sum]);
	}
	unset($stm);
}
