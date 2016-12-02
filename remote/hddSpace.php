<?php
/**
 * zjištění volného místa na disku
 */

$output = NULL;
$return_var = NULL;
exec('df', $output, $return_var);

foreach ($output as $k => $line) {
	if ($k === 0) {
		continue;
	}
	$data = array_values(array_filter(explode(' ', $line), 'strlen'));

	if ($data[5] !== '/') {
		continue;
	}

	echo json_encode([
		'free' => $data[3],
		'total' => $data[1],
	]);
	exit;
}
