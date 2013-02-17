<?php
date_default_timezone_set(@date_default_timezone_get());
function YMD($time) {
	return date('Ymd', $time);
}

define('NOW', time());
define('TODAY', YMD(NOW));

$expire = NOW - 36000;
foreach(scandir('Public/temp/') as $f) {
	if($f{0} == '.')
		continue;
	$f = 'Public/temp/' . $f;
	if(filectime($f) < $expire) {
		unlink($f);
	}
}
