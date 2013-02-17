<?php
$end = ($param['expire']) ? str_replace('-', '', $param['expire']) : date('Ymd', NOW + 24 * 3600 * 20);
$cTime = NOW;
$weekday = date('w', $cTime);
$d = ($param['day'] - $weekday + 7) % 7;
$cTime += $d * 24 * 3600;
$current = date('Ymd', $cTime);
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
	$cTime += 7 * 24 * 3600;
	$current = date('Ymd', $cTime);
}
return $ret;
?>
