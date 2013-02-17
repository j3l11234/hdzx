<?php
function options($array, $current='') {
	foreach($array as $k=>$v) {
		echo '<option value="' . $k . '" ' . ($k == $current ? 'selected' : '') . '>' . $v . '</option>';
	}
}
?>
