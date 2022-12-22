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

if (empty($_REQUEST['data'])) {
	$_REQUEST['data'] = '';
}
$_REQUEST['data'] = trim($_REQUEST['data']);

$accounts = array();
$query = "SELECT * FROM accounts ORDER BY acc_id ASC";
$stm = $GLOBALS['sql']->query($query);
while ($row = $stm->fetch()) {
	$accounts[$row['acc_id']] = $row;
}
$stm = null;

if (!empty($_REQUEST['acc']) && !empty($accounts[$_REQUEST['acc']])) {
	$data = $_REQUEST['data'];
	$data = preg_replace("@[\r\n]+@u", "\n", $data);
	$data = explode("\n", $data);
	$lines = array();
	foreach ($data as $line) {
		// Nordea CSV export, new July 2022 format
		// Bogføringsdato;Beløb;Afsender;Modtager;Navn;Beskrivelse;Saldo;Valuta;Afstemt
		if (preg_match('@^(\d+)/(\d+)/(\d+);@', $line)) {
			$line = preg_replace('@(\d+)/(\d+)/(\d+);@u', '$1-$2-$3;', $line);
			list($date,$amount,$_sender,$_receiver,$_name,$desc,$balance,$_currency,$_settled) = explode(';', $line);
			$amount = str_replace(',', '.', $amount);
			$balance = str_replace(',', '.', $balance);

			if (empty($balance)) {
				$balance = 0.00;
			}

			$desc = preg_replace('@\s+@u', ' ', $desc);
			$desc = preg_replace('@\s+@u', ' ', $desc);
			$desc = preg_replace('@[ \t,]$@u', '', $desc);
			$lines[] = array(
				'date' => $date,
				'desc' => $desc,
				'idate' => $date,
				'amount' => $amount,
				'balance' => $balance,
				);
		}
		// Nordea CSV export, new 2022 format
		// Bogføringsdato;Beløb;Afsender;Modtager;Navn;Beskrivelse;Saldo;Valuta
		else if (preg_match('@^(\d+)\.(\d+)\.(\d+);@', $line)) {
			$line = preg_replace('@(\d+)\.(\d+)\.(\d+);@u', '$3-$2-$1;', $line);
			list($date,$amount,$_sender,$_receiver,$_name,$desc,$balance,$_currency) = explode(';', $line);
			$amount = str_replace(',', '.', $amount);
			$balance = str_replace(',', '.', $balance);

			if (empty($balance)) {
				$balance = 0.00;
			}

			$desc = preg_replace('@\s+@u', ' ', $desc);
			$desc = preg_replace('@\s+@u', ' ', $desc);
			$desc = preg_replace('@[ \t,]$@u', '', $desc);
			$lines[] = array(
				'date' => $date,
				'desc' => $desc,
				'idate' => $date,
				'amount' => $amount,
				'balance' => $balance,
				);
		}
		// Nordea CSV export
		else if (preg_match('@^(\d+)-(\d+)-(\d+);@', $line)) {
			$line = preg_replace('@(\d+)-(\d+)-(\d+);@u', '$3-$2-$1;', $line);
			list($date,$desc,$idate,$amount,$balance) = explode(';', $line);
			$amount = str_replace(',', '.', $amount);
			$balance = str_replace(',', '.', $balance);

			if (empty($balance)) {
				$balance = 0.00;
			}

			$desc = preg_replace('@\s+@u', ' ', $desc);
			$desc = preg_replace('@\s+@u', ' ', $desc);
			$desc = preg_replace('@[ \t,]$@u', '', $desc);
			$lines[] = array(
				'date' => $date,
				'desc' => $desc,
				'idate' => $idate,
				'amount' => $amount,
				'balance' => $balance,
				);
		}
		// Nordea Finans copy-paste
		else if (preg_match('@^(\d+)\.(\d+)\.(\d+) ?\t@', $line)) {
			$line = preg_replace('@^(\d+)\.(\d+)\.(\d+) ?\t@u', "20\$3-\$2-\$1\t", $line);
			$line = preg_replace('@ *\t +@u', "\t", $line);
			list($date,$desc,$amount,$balance) = explode("\t", $line);

			$amount = msc_convert_num($amount);
			$balance = msc_convert_num($balance);

			$desc = preg_replace('@\s+@u', ' ', $desc);
			$desc = preg_replace('@\s+@u', ' ', $desc);
			$desc = preg_replace('@[ \t,]$@u', '', $desc);
			$lines[] = array(
				'date' => $date,
				'desc' => $desc,
				'idate' => $date,
				'amount' => $amount,
				'balance' => $balance,
				);
		}
	}

	$reverse = false;
	for ($i=1, $e=count($lines) ; $i < $e ; $i++) {
		if (floatval($lines[$i]['balance']) + floatval($lines[$i-1]['amount']) == floatval($lines[$i-1]['balance'])) {
			$reverse = true;
		}
	}
	if ($reverse) {
		$lines = array_reverse($lines);
	}

	$GLOBALS['sql']->beginTransaction();

	$ids = array();
	$query = "INSERT INTO entries (entry_date, entry_date_interest, entry_text, entry_amount, entry_balance) VALUES (:date, :idate, :desc, :amount, :balance)";
	$stm = $GLOBALS['sql']->prepare($query);
	foreach ($lines as $line) {
		$stm->execute(array(
			':date' => $line['date'],
			':idate' => $line['idate'],
			':desc' => $line['desc'],
			':amount' => $line['amount'],
			':balance' => $line['balance'],
			));
		$ids[] = $GLOBALS['sql']->lastInsertId();
		echo 'Inserted ', var_export($line, true), '<br>';
	}

	$query = "INSERT INTO account_entries (acc_id, entry_id) VALUES (:acc_id, :entry_id)";
	$stm = $GLOBALS['sql']->prepare($query);
	foreach ($ids as $id) {
		$stm->execute(array(
			':acc_id' => $_REQUEST['acc'],
			':entry_id' => $id,
			));
	}

	$GLOBALS['sql']->query('UPDATE accounts as acc NATURAL JOIN (SELECT acc_id, SUM(entry_amount) + acc_initial as new_balance FROM accounts NATURAL JOIN account_entries NATURAL JOIN entries GROUP BY acc_id) as calc SET acc.acc_balance = calc.new_balance');
	$GLOBALS['sql']->commit();
}

$data = htmlspecialchars($_REQUEST['data']);
echo <<<XOUT
<form method="post">
<select name="acc">
XOUT;
foreach ($accounts as $id => $acc) {
	$name = htmlspecialchars($acc['acc_name']);
	echo <<<XOUT
	<option value="{$id}">{$id}: {$name}</option>
XOUT;
}
echo <<<XOUT
</select>
<br>
<textarea name="data">
{$data}
</textarea>
<br>
<input type="submit" value="Insert Entries">
</form>
XOUT;

?>
</body>
</html>
