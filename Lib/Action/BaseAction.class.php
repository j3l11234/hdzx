<?php
class BaseAction extends Action {
	
	public function __construct() {
		Action::__construct();
		D('Order')->autoReject();
		D('Order')->autoAccept();
		D('RoomStatus')->refresh();
	}

	public function display() {
		$navi = array();
		if(file_exists('navibar.data.php')) {
			$navi = file('navibar.data.php');
			unset($navi[0]);
			if(isset($navi[1]))
				$navi = json_decode($navi[1], true);
		}
		$this->assign('navibar', $navi);
		Action::display('Index:header');
		Action::display();
		Action::display('Index:footer');
	}

	public function success($msg, $url = 'index', $delay = 3) {
		$this->assign('message', $msg);
		if($url{0} != '/' && substr($url, 0, 7) != 'http://')
			$url = U($url);
		$this->assign('url', $url);
		$this->assign('delay', $delay);
		Action::display('Public:success');
		die();
	}
}
?>
