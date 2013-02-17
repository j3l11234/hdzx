<?php
$date = str_replace('-', '', $param['date']);
$ret = array();
foreach(explode(',', $param['hours']) as $h) {
	$h *= 1;
	if($h < 10)
		$h = '0' . $h;
	$ret[] = $date . $h;
}
return $ret;
?>
