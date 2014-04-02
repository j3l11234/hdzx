<?php
class QueryAction extends Action {
	public function roomtypes() {
		$types = D('Room')->types();
		if(!$types)
		$types = false;
		die(json_encode($types));
	}
	public function roomDayTimelist($room, $date) {
		D('RoomStatus')->refresh();
		$dao = D('RoomStatus');
		$ret = $dao->timetable($room, $date);
		if(!$ret)
		$ret = false;
		else {
			$res = array();
			foreach($ret as $k=>$v) {
				$res[$k % 100] = $v;
			}
			$ret = $res;
		}
		die(json_encode($ret));
	}

	//供管理模式下调用—— 根据占用——显示出预约信息
	//房间id，时间，申请的小时
	public function getOrderByWeb($room, $date, $hour){
		//校团委或管理员模式下 允许查询详细消息
		if(PRV('userid') == 16 || PRV('userid') == 3){
			$where['room'] = $room;
			$where['date'] = $date;
			$where['starthour'] = array('ELT', $hour);
			$where['endhour'] = array('EGT', $hour);
			$where['isverified'] =1;
			$orders = D('Order')->where($where)->select();
			if(orders){
				$orders = $orders[0];
				//$result['info'] = json_decode($result['info'], true);
				//var_dump($where);
				$orders['endhour']++;
				$orders['date'] = num2date($orders['date']);
				die(json_encode($orders));
			}
		}
	}

	public function schools() {
		$ret = D('School')->getList();
		die(json_encode($ret));
	}
}
?>
