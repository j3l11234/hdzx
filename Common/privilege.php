<?php
function PRV($key, $value = NULL) {
	if($key === false && $value === true) {
		$_SESSION['privilege'] = array();
		return;
	}
	if(!isset($_SESSION['privilege']))
		$_SESSION['privilege'] = array();
	if($value !== NULL)
		$_SESSION['privilege'][$key] = $value;
	elseif(isset($_SESSION['privilege'][$key]))
		return $_SESSION['privilege'][$key];
	return false;
}
?>
