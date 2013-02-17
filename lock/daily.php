<?php
$end = ($param['expire']) ? str_replace('-', '', $param['expire']) : date('Ymd', NOW + 24 * 3600 * 20);
$current = ($param['start']) ? str_replace('-', '', $param['start']) : TODAY;
$cTime = strtotime($current);
$hours = explode(',', $param['hours']);
foreach($hours as $k => $v) {
	$v *= 1;
	if($v < 10)
		$v = '0' . $v;
	$hours[$k] = $v;
}
$ret = array();
while($current <= $end) {
	foreach($hours as $f) {
		$ret[] = $current . $f;
	}
	$cTime += 24 * 3600;
	$current = date('Ymd', $cTime);
}
return $ret;
?>
