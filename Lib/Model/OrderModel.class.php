<?php
class OrderModel extends Model {
	
	public function autoReject() {
		$sql = D('RoomStatus')->alias('S')->where('`S`.`room` = `O`.`room` AND `S`.`time` >= `O`.`date` * 100 + `O`.`starthour` AND `S`.`time` <= `O`.`date` * 100 + `O`.`endhour`')->buildSql();
		$where['_string'] = 'EXISTS ' . $sql;
		$where['isverified'] = array(0,3,'or');
		$res = $this->alias('O')->where($where)->field('orderid')->select();
		if($res) {
			foreach($res as $f) {
				$this->reject(0, $f['orderid'], '与其他审批通过的预约或锁定时间表冲突');
			}
		}
	}

	public function autoAccept() {
		$sql = D('Room')->where(array('autoverify'=>'1'))->field('roomid')->buildSql();
		$where['_string'] = '`room` IN ' . $sql;
		$where['isverified'] = array(0,3,'or');
		$res = $this->where($where)->field('orderid')->select();
		if($res) {
			foreach($res as $f) {
				$this->accept(0, $f['orderid'], '系统自动审核通过');
			}
		}
	}

	public function accept($uid, $orderid, $comment) {
		$VDao = D('Verify');
		if($VDao->where(array('orderid'=>$orderid, 'verifier'=>$uid))->count())
			return;
		$owh['orderid'] = $orderid;
		$r = $this->where($owh)->field('info,date,starthour,endhour,orderer,room,school,isverified')->select();
		if($r) {
			$r = $r[0];
			$roomid = $r['room'];
			$orderer = $r['orderer'];
			$info = json_decode($r['info'], true);
			$time = num2date($r['date']) . ' ' . $r['starthour'] . '点 - ' . (1 + $r['endhour']) . '点';
			if(!D('RoomStatus')->isFree($roomid, $r['date'], $r['starthour'], $r['endhour']))
				return;
			if($r['school'] == 0 || $r['isverified'] == 3) {
				$r = 1;
			} else {
				$r = 3;
			}
			$this->where($owh)->save(array('isverified'=>$r));
			if($r == 1) {
				D('RoomStatus')->updateRoom($roomid);
				sendmail($orderer . '@bjtu.edu.cn', '预约批准 - 学生活动中心', "您的预约 {$info['roomname']} {$time} 已被审核者批准，请及时领取开门条。");
			}
			$VDao->add(array(
				'orderid' => $orderid,
				'ispass' => 1,
				'verifier' => $uid,
				'comment' => $comment
			));
		}
	}

	public function reject($uid, $orderid, $comment) {
		$VDao = D('Verify');
		if($VDao->where(array('orderid'=>$orderid, 'verifier'=>$uid))->count())
			return;
		$owh['orderid'] = $orderid;
		$r = $this->where($owh)->field('info,date,starthour,endhour,orderer')->select();
		if($r) {
			$r = $r[0];
			$orderer = $r['orderer'];
			$info = json_decode($r['info'], true);
			$time = num2date($r['date']) . ' ' . $r['starthour'] . '点 - ' . (1 + $r['endhour']) . '点';
			$this->where($owh)->save(array('isverified'=>2));
			sendmail($orderer . '@bjtu.edu.cn', '预约驳回 - 学生活动中心', "您的预约 {$info['roomname']} {$time} 已被审核者驳回。");
			$VDao->add(array(
				'orderid' => $orderid,
				'ispass' => 0,
				'verifier' => $uid,
				'comment' => $comment
			));
		}
	}
}
?>
