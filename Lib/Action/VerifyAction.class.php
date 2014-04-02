<?php
class VerifyAction extends BaseAction {
    
    private $adminUid;

	public function _initialize() {
		if(PRV('verify') === false)
			$this->redirect('User/login', array('prev'=>'Verify:index'));
        $result = D('User')->where('`username`="admin"')->field('userid')->select();
        $this->adminUid = $result[0]['userid'];
	}

	public function index() {
		$this->redirect('pending');
	}

	public function pending() {
		$this->assign('title', '待审核预约');
		$school = PRV('verify');
        if(PRV('userid') == $this->adminUid) { 
        	//管理员模式
            $where['isverified'] = array('IN', array(0,3));
		} elseif($school > 0) { 
			//各学院管理模式
			$where['school'] = $school;
			$where['isverified'] = 0;
		} else {
			//老师_校团委
			$where['isverified'] = 3;
			$where['_complex']['isverified'] = 0;
			$where['_complex']['school'] = 0;
			$where['_logic'] = 'or';
			//var_dump($where);
		}
		
		$wh = $where; unset($where);
		$where['_complex'] = $wh;
		$where['date'] = array('egt', TODAY);
		
		//var_dump($where);
		//die();
		
		$orders = D('Order')->where($where)->select();
		
		if($orders) { //查询结果不为空	
			
			//构造时间表
			//时间表为一个二维数组，[每天的每小时][orderID]
			$timetable = array();
			$room = array();
			foreach($orders as $r=>$v) {
				$id = $v['orderid'];
				$room[$id] = $v['room'];
				
				for($i = $v['starthour']; $i <= $v['endhour']; $i++) {
					if($i < 10)
						$i = '0' . ($i * 1);
					$k = $v['date'] . $i;
					if(!isset($timetable[$k])) {
						$timetable[$k] = array($id);
					} else {
						$timetable[$k][] = $id;
					}
				}
				$orders[$r]['info'] = json_decode($v['info'], true);
			}
			
			//检测冲突，建立冲突表
			//冲突表为二维素组，[主ID][与主ID冲突的ID]
			$conflict = array();
			foreach($timetable as $v) { //每一天的每个小时
				foreach($v as $k) { 
					foreach($v as $id) {
						if($id == $k || $room[$id] != $room[$k])
							continue;
						//存在冲突
						//存入(键名为ID，键 值为1)
						if(!isset($conflict[$k])) {
							$conflict[$k] = array($id => 1);
						} else {
							$conflict[$k][$id] = 1;
						}
					}
				}
			}
			//将冲突表中[键名，键值]改为[键名的数组]
			foreach($conflict as $k=>$v) {
				$conflict[$k] = array_keys($v);
			}
			$this->assign('conflict', $conflict);
			
		} else //查询结果为空
			$orders = array();
			
		$schList = D('School')->getList();
		$schList[0] = '校级';
		$this->assign('school', $schList);
		$this->assign('orders', $orders);
		$this->display();
	}

	// accpet a order
	public function accept() {
		$orderid = ceil($_GET['id']);
		$dao = D('Order');
		$dao->accept(PRV('userid'), $orderid, htmlspecialchars($_POST['comment']));
		if($_GET['reject']) {
			foreach(explode('|', $_GET['reject']) as $r)
				$dao->reject(PRV('userid'), ceil($r), '与其他预约冲突而被自动驳回');
		}
		$this->success('预约批准成功', 'pending', 2);
	}

	// reject a order
	public function reject() {
		$orderid = ceil($_GET['id']);
		$dao = D('Order');
		$dao->reject(PRV('userid'), $orderid, htmlspecialchars($_POST['comment']));
		$this->success('预约驳回成功', 'pending');
	}

    // regret a decision
    public function regret($id) {
        $result = D('Order')->regretVerify(PRV('userid'), ceil($id));
        $this->success($result, 'history');
    }

	// display historical verify record
	public function history($auto = 0, $page=1, $school=-1, $roomtype=0, $date=0) {
		$this->assign('param', array(
				'page' => $page,
				'school' => $school,
				'roomtype' => $roomtype,
				'date' => $date,
				'auto' => $auto
			));
		if(!$auto) {
			$uid = PRV('userid');
            $vwh = PRV('userid') == $this->adminUid ? array('verifier'=>array('neq', 0)) : array('verifier'=>$uid);
		} else {
			$uid = 0;
            $vwh = array('verifier'=>$uid);
		}
		$sql = D('Verify')->where($vwh)->field('orderid')->buildSql();
		$where['_string'] = '`orderid` IN ' . $sql;
		if($school >= 0) {
			if(PRV('verify') && $school != PRV('verify'))
				$where['_string'] = 'false';
			$where['school'] = $school;
		}
		if($date) {
			$date = str_replace('-', '', $date);
			$where['date'] = array('elt', $date);
		}
		if($roomtype) {
			$roomtype = urldecode($roomtype);
			$sql = D('Room')->where(array('type'=>$roomtype))->field('roomid')->buildSql();
			$where['_string'] .= ' AND `room` IN ' . $sql;
		}
		$count = D('Order')->where($where)->count();
		$perPage = 10;
		$this->assign('pageTotal', ceil($count / $perPage));
		$result = D('Order')->where($where)->order(array('time'=>'desc'))->limit($perPage)->page($page)->select();
		$ids = array();
		foreach($result as $k=>$v) {
			$result[$k]['info'] = json_decode($v['info'], true);
			$ids[] = $v['orderid'];
		}
		$this->assign('orders', $result);
		$verify = array();
		$result = D('Verify')->alias('V')->join('`user` U on V.verifier = U.userid')->where($vwh)->field('V.*,U.username')->select();
		foreach($result as $v) {
            if($v['verifier'] == 0)
                $v['username'] = '自动审核';
			$verify[$v['orderid']] = $v;
		}
		$this->assign('verify', $verify);
		$schList = D('School')->getList();
		$schList[0] = '校级';
		$this->assign('school', $schList);
		$roomType = array();
		foreach(D('Room')->types() as $v) {
			$roomType[urlencode($v)] = $v;
		}
		$this->assign('roomtype', $roomType);
		$this->assign('title', '过往审批记录');
		$this->display();
	}

	public function auto($page=1, $school=-1, $roomtype=0, $date=0) {
		$this->redirect('history', array(
				'page' => $page,
				'school' => $school,
				'roomtype' => $roomtype,
				'date' => $date,
				'auto' => 1
			));
	}
}
