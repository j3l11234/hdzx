<?php
session_start();
//define('APP_DEBUG',true);

define('APP_NAME', 'hdzxnew/');
foreach(scandir('Common/') as $_incfile) {
	if(substr($_incfile, -4) == '.php')
		include('Common/' . $_incfile);
}
require_once('ThinkPHP/ThinkPHP.php');
?>
