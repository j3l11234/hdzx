<?php
function array_pack($arr) {
	$keys = array();
	foreach($arr as $v) {
		$keys = array_merge($keys, array_keys($v));
	}
	$keys = array_unique($keys);
	$ret = array();
	foreach($keys as $k) {
		foreach($arr as $t=>$v) {
			if(isset($v[$k])) {
				$ret[$k][$t] = $v[$k];
			}
		}
	}
	return $ret;
}

function show_pager($totalPage, $currentPage, $format = '?page=$$', $padding = 3) {
	if($totalPage <= 1)
		return;
	$first = $currentPage - $padding;
	if($first < 1)
		$first = 1;
	$last = $currentPage + $padding;
	if($last > $totalPage)
		$last = $totalPage;
	$ret = array();
	if($first > 1)
		$ret[] = array('第一页', 1);
	if($currentPage > 1)
		$ret[] = array('前一页', $currentPage - 1);
	for($i = $first; $i <= $last; $i++) {
		$ret[] = $i;
	}
	if($currentPage < $totalPage)
		$ret[] = array('下一页', $currentPage + 1);
	if($last < $totalPage)
		$ret[] = array('最后一页', $totalPage);
	foreach($ret as $v) {
		if(is_array($v)) {
			$url = str_replace('$$', $v[1], $format);
			echo '<a href="' . $url . '">' . $v[0] . '</a>';
		} else {
			$url = str_replace('$$', $v, $format);
			$isCur = ($currentPage == $v) ? ' class="current"':'';
			echo '<a href="' . $url . '"'.$isCur.'>' . $v . '</a>';
		}
	}
}

function num2date($num) {
	return substr($num, 0, 4) . '-' . substr($num, 4, 2) . '-' . substr($num, 6, 2);
}
?>
