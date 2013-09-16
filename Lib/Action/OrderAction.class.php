<?php
class OrderAction extends BaseAction {
	// show the list of rooms
	public function index($page=1, $name='', $date='', $start=8, $end=22, $floor=0, $type=0) {
		$param = array(
			'name' => $name,
			'date' => $date,
			'start' => $start,
			'end' => $end,
			'floor' => $floor,
			'type' => $type,
			'page' => $page
		);
		$this->assign('param', $param);
		$dao = D('Room');
		$where = array();
		if($name) {
			$where['_complex'] = array();
			$where['_complex']['_logic'] = 'or';
			$numbers = preg_split('/[^0-9]+/', $name);
			$where['_complex']['number'] = array('IN', $numbers);
			$names = preg_split('/[ \.\,\;\|\/\-_\\\\]+/', $name);
			foreach($names as $k => $v) {
				$names[$k] = array('like', '%' . $v . '%');
			}
			$names[] = 'or';
			$where['_complex']['name'] = $names;
		}
		if($date && $start && $end) {
			$m = D('RoomStatus');
			$date = str_replace('-', '', $date);
			$start = $date * 100 + $start;
			$end = $date * 100 + $end;
			$sql = $m->where(array(
				'time' => array(array('egt', $start), array('lt', $end))
			))->field('room')->buildSql();
			$where['_string'] = '`roomid` NOT IN ' . $sql;
		}
		if($floor) {
			$where['floor'] = $floor;
		}
		if($type) {
			$type = urldecode($type);
			$where['type'] = $type;
			$this->assign('type', $type);
		}
		$where['isopen'] = 1;
		$perPage = 7;
		$rooms = $dao->where($where)->order('number')->page($page)->limit($perPage)->select();
		$this->assign('rooms', $rooms);

		$count = $dao->where($where)->count();
		$this->assign('pageTotal', ceil($count / $perPage));

		$dates = array();
		$day = strtotime(TODAY);
		$weekday = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
		for($i = 0; $i < 11; $i++) {
			$str = date('Y-m-d ', $day);
			$str .= $i == 0 ? '今天' : $weekday[date('w', $day)];
			$dates[] = $str;
			$day += 24 * 3600;
		}
		$this->assign('dates', $dates);

		if($rooms) {
			$rid = array();
			foreach($rooms as $r) {
				$rid[] = $r['roomid'];
			}
			$this->assign('status', D('RoomStatus')->timetable($rid, TODAY, date('Ymd', $day)));
		}
		
		$types = array();
		foreach(D('Room')->types() as $type) {
			$types[urlencode($type)] = $type;
		}
		$this->assign('roomTypes', $types);

		$settings = D('Settings');
		$floors = array();
		$c = $settings->get('max_floor');
		for($i = $settings->get('min_floor'); $i <= $c; $i++) {
			$floors[$i] = $i;
		}
		$this->assign('floors', $floors);
		$this->assign('title', '房间列表');
		$this->display();
	}

	// display order form
	public function order($room, $date) {
        if($date <= TODAY)
            $this->success('必须至少提前一天预约！');
		if(isset($_SESSION['pendingorder']) && $_SESSION['pendingorder'])
			$this->redirect('pendingorder');
		$this->assign('title', '房间预约');
		$this->assign('readme', D('Settings')->get('order_readme_url'));
		$this->assign('roomid', $room);
		$this->assign('date', $date);
		$r = D('Room')->where(array('roomid'=>$room))->field('intro', true)->select();
		if($r) {
			$this->assign('schools', D('School')->getList());
			$this->assign('room', $r[0]);
			$this->display();
		}
	}

	private function sendOrderMail() {
		if(!isset($_SESSION['pendingorder']))
			$this->success('没有未完成验证的预约', 'Index/index');
		$data = $_SESSION['pendingorder'];
		$data['endhour'] -= 1;
		$url = 'http://' . $_SERVER['HTTP_HOST'] . U('confirm', array('key'=>$data['key']));
		$date = substr($data['date'], 0, 4) . '-' . substr($data['date'], 4, 2) . '-' . substr($data['date'], 6, 2);
		sendmail($data['orderer'] . '@bjtu.edu.cn',
			'学生活动服务中心预约验证',
			"您正在预约{$data['info']['roomname']} {$date} {$data['starthour']}点 - {$data['endhour']}点。
			访问链接： {$url} 完成验证。");
	}

	// display pending order
	public function pendingorder($sendmail = false) {
		if(!isset($_SESSION['pendingorder']))
			$this->success('没有未完成验证的预约', 'Index/index');
		if($sendmail) {
			if(isset($_SESSION['maildelay']) && $_SESSION['maildelay'] > NOW) {
				$this->success('为防止恶意攻击，发邮件操作必须至少间隔15秒', 'pendingorder');
			}
			$_SESSION['maildelay'] = NOW + 15;
			$this->sendOrderMail();
			$this->assign('title', '预约验证邮件已发送');
		} else {
			$this->assign('title', '您有尚未验证的预约');
		}
		$this->assign('order', $_SESSION['pendingorder']);
		$this->display();
	}

	// remove pending order
	public function regret() {
		if(isset($_SESSION['pendingorder']))
			unset($_SESSION['pendingorder']);
		$this->success('预约已删除', 'Index/index');
	}

	// commit order
	public function commit() {
		if(isset($_SESSION['pendingorder']) && $_SESSION['pendingorder']) {
			die('您已提交了一份申请，在完成邮箱认证前无法进行其他申请');
		}
		if(isset($_SESSION['ordercommit']) && $_SESSION['ordercommit'] > NOW) {
			die('为保护服务器，提交间隔必须至少为5秒');
		}
		$_SESSION['ordercommit'] = NOW + 10;
		foreach(array(
					'ordererid' => array('预约人学号', '/^[0-9]{8}$/'),
					'orderer' => array('预约人姓名', 'isset($${4})'),
					'contact' => '预约人联系方式',
					'date' => array('日期', '$$ > '. TODAY, '预约日期必须至少提前一天'),
					'starthour' => array('开始时间', '$$ > 7', '开始时间无效'),
					'endhour' => array('结束时间', '($$ -= 1) < 22 && $$ >= $_POST["starthour"]', '结束时间无效'),
					'topic' => array('活动主题', 'isset($${7})', '活动主题太短'),
					'content' => array('活动内容', 'isset($${7})', '活动内容太短'),
					'unit' => '举办单位',
					'school' => array('学院', '$$ > 0', '请选择学院', '$_POST["unit"] != "top"'),
					'unitname' => array('社团名称', 'isset($${4})', '社团名称太短', '$_POST["unit"] != "personal"'),
					'secure' => array('安保措施', 'isset($${20})', '安保措施太短', 'isset($_POST["secure"])'),
				) as $key => $param) {
			$res = true;
			if(is_array($param) && isset($param[3])) {
				$v = '$res = (' . $param[3] . ');';
				eval($v);
				if(!$res)
					continue;
			}
			if(!isset($_POST[$key]) || ($_POST[$key] = trim($_POST[$key])) == '') {
				if(is_array($param))
					$param = $param[0];
				die('必须填写' . $param);
			}
			if(is_array($param)) {
				if($param[1]{0} == '/') {
					$res = preg_match($param[1], $_POST[$key]);
				} else {
					$v = '$res = (' . str_replace('$$', '$_POST[\'' . $key . '\']', $param[1]) . ');';
					eval($v);
				}
				if(!$res) {
					if(isset($param[2]))
						die($param[2]);
					die($param[0] . '格式不正确');
				}
			}
		}
		$room = D('Room')->where(array('roomid'=>$_POST['room']))->field('info', true)->select();
		if(!$room) {
			die('本房间不存在');
		}
		$room = $room[0];
		if(!$room['isopen'])
			die('本房间不开放申请');
		if($_POST['endhour'] - $_POST['starthour'] >= $room['maxhour']) {
			die('本房间单个预约最多可预定 ' . $room['maxhour'] . ' 个小时');
		}

		$data = array();
		foreach($_POST as $k=>$v) {
			$_POST[$k] = htmlspecialchars($v);
		}
		foreach(array('room', 'school', 'date', 'starthour', 'endhour') as $v) {
			$data[$v] = $_POST[$v];
			unset($_POST[$v]);
		}
		$data['orderer'] = $_POST['ordererid'];
		unset($_POST['ordererid']);
		if($_POST['unit'] == 'top') {
			$data['school'] = 0;
		}
		unset($_POST['unit']);
		$data['info'] = $_POST;
		$data['key'] = $key = md5(NOW . rand(100, 999)) . rand(100, 999);
		$_SESSION['pendingorder'] = $data;
		die('ok');
	}

	// put order into database
	public function confirm($key) {
		if(!isset($_SESSION['pendingorder']))
			$this->success('没有未完成验证的预约', 'Index/index');
		if($key != $_SESSION['pendingorder']['key'])
			$this->success('这个预约验证不存在或已过期', 'Index/index');
		$data = $_SESSION['pendingorder'];
		unset($_SESSION['pendingorder']);
		$data['info'] = json_encode($data['info']);
		unset($data['key']);
		M('Order')->add($data);
		$this->success('预约验证完成，审核结果会发往您的邮箱', U('query', array('id'=>$data['orderer'])));
	}

	// check order status
	public function query($id='') {
		if($id) {
			$result = D('Order')->order('`isverified` = 1 desc, ISNULL(`checkouttime`) desc, `orderid` asc')
				->where(array('date'=>array('egt', TODAY), 'orderer'=>$id))->select();
			$this->assign('id', $id);
		} else {
			$result = false;
		}
		$this->assign('orders', $result);
		$this->assign('title', '预约查询');
		$this->display();
	}

	// confirm a order checked out
	public function checkout($id) {
		if(!PRV('checkout'))
			die();
		if(D('Order')->where(array('orderid'=>$id))->save(array('checkouttime'=>array('%NOW()'))))
			die(json_encode(array(
				'result' => true,
				'date' => date('Y-m-d H:i:s', NOW)
			)));
	}
	
	// remove existing order
	public function remove($id) {
		$data = D('Order')->where(array('orderid'=>$id))->select();
		if($data) {
			$data = $data[0];
			$returnUrl = U('query', array('id'=>$data['orderer']));
			if(isset($_SESSION['deldelay']) && $_SESSION['deldelay'] > NOW) {
				$this->success('为防止恶意攻击，删除操作至少间隔60秒', $returnUrl);
			}
			$_SESSION['deldelay'] = NOW + 60;
			$data['info'] = json_decode($data['info'], true);
			$oprval = json_encode(array('delete'=>NOW, 'id'=>$id, 'orderer'=>$data['orderer']));
			$key = substr(md5($oprval), 11, 16);
			if(!isset($_SESSION['del']))
				$_SESSION['del'] = array();
			$_SESSION['del'][$key] = $oprval;
			$url = 'http://' . $_SERVER['HTTP_HOST'] . U('confirmremove', array('key'=>$key));
			$date = substr($data['date'], 0, 4) . '-' . substr($data['date'], 4, 2) . '-' . substr($data['date'], 6, 2);
			sendmail($data['orderer'] . '@bjtu.edu.cn',
				'学生活动服务中心预约删除',
				"您要删除预约{$data['info']['roomname']} {$date} {$data['starthour']}点 - {$data['endhour']}点。
				访问链接： {$url} 完成删除操作验证。");
			$this->success('删除操作需要邮箱验证，请查看您的邮箱', $returnUrl, 5);
		} else {
			$this->success('此预约不存在', 'query');
		}
	}

	// confirm remove
	public function confirmremove($key) {
		$key = $key;
		if(isset($_SESSION['del'][$key])) {
			$data = json_decode($_SESSION['del'][$key], true);
			unset($_SESSION['del'][$key]);
			$id = $data['id'];
			D('Order')->remove($id);
			$this->success('删除操作完成', U('query', array('id'=>$data['orderer'])), 3);
		}
		$this->success('无效或过期的删除操作', 'query');
	}
}
?>
