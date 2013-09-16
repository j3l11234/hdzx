<?php
class OrderModel extends Model {
	
	public function autoReject() {
		$sql = D('RoomStatus')->alias('S')->where('`S`.`room` = `O`.`room` AND `S`.`time` >= `O`.`date` * 100 + `O`.`starthour` AND `S`.`time` <= `O`.`date` * 100 + `O`.`endhour`')->buildSql();
		$where['isverified'] = array(0,3,'or');
		$where['date'] = array('egt', TODAY);
		$where['_string'] = 'EXISTS ' . $sql;
		$res = $this->alias('O')->where($where)->field('orderid')->select();
		if($res) {
			foreach($res as $f) {
				$this->reject(0, $f['orderid'], '与其他审批通过的预约或锁定时间表冲突');
			}
		}
		$sql = D('RoomStatus')->alias('S')->where('`S`.`islock`=1 AND `S`.`room` = `O`.`room` AND `S`.`time` >= `O`.`date` * 100 + `O`.`starthour` AND `S`.`time` <= `O`.`date` * 100 + `O`.`endhour`')->buildSql();
		$where['_string'] = 'EXISTS ' . $sql;
		$where['isverified'] = array('neq', 3);
		$res = $this->alias('O')->where($where)->field('orderid')->select();
		if($res) {
			foreach($res as $f) {
				$this->reject(0, $f['orderid'], '与官方锁定时间表冲突');
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
		//if($VDao->where(array('orderid'=>$orderid, 'verifier'=>$uid))->count())
		//	return;
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
			if($r['school'] == 0 || $r['isverified'] == 3 || $uid == 0) {
				$r = 1;
			} else {
				$r = 3;
			}
			$this->where($owh)->save(array('isverified'=>$r));
			if($r == 1) {
				D('RoomStatus')->updateRoom($roomid);
				/**
				* WARNING! HARD CODE AHEAD!
				*/
				$addon = '';
				if($roomid == 53 || $roomid == 52) {
					$url = 'http://' . $_SERVER['HTTP_HOST'] . U('index/downloadPaperOrderSheet');
					$addon = '
					注意：多功能厅与小剧场不需要开门条但是需要打印三份纸制版申请单。（下载地址：' . $url . '）';
				}
				/**
				* You've survied a hard code
				*/
				sendmail($orderer . '@bjtu.edu.cn', '预约批准 - 学生活动中心', "您的预约 {$info['roomname']} {$time} 已被审核者批准，请及时领取开门条。
				批注：" . $comment . $addon);
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
			sendmail($orderer . '@bjtu.edu.cn', '预约驳回 - 学生活动中心', "您的预约 {$info['roomname']} {$time} 已被审核者驳回。
			批注：" . $comment);
			$VDao->add(array(
				'orderid' => $orderid,
				'ispass' => 0,
				'verifier' => $uid,
				'comment' => $comment
			));
		}
	}

    public function regretVerify($uid, $verifyId) {
        $id = ceil($id);
        $result = D('Verify')->where(array('verifyid'=>$verifyId))->select();
        if(!$result)
            return '这个审批记录不存在';
        $result = $result[0];
        $sid = PRV('verify');
        if($uid != $result['verifier'] && $result['verifier'] != 0)
            return '您无权撤销这个审批记录';
        $owh = array('orderid'=>$result['orderid']);
        $order = $this->where($owh)->select();
        if(!$order)
            return '预约不存在';
        // if the user is a department but order is aproved by school
        if($order['isverified'] == 1 && $sid != 0)
            return '这个预约已被校级审核通过，您无权撤销';
        $order = $order[0];
        $info = json_decode($order['info'], true);
        $time = num2date($order['date']) . ' ' . $order['starthour'] . '点 - ' . (1 + $order['endhour']) . '点';
        sendmail($order['orderer'] . '@bjtu.edu.cn', '预约审核撤销 - 学生活动中心', "您的预约 {$info['roomname']} {$time} 之前的审核决定已被撤销，请等待变更的审核结果。");

        if($sid == 0) {
            $newState = 3;
            D('Verify')->where(array('verifyid'=>$verifyId))->delete();
        } else {
            $newState = 0;
            D('Verify')->where(array('orderid'=>$order['orderid']))->delete();
        }
        $this->where($owh)->save(array('isverified' => $newState));
        D('RoomStatus')->updateRoom($order['room']);
        return '审核决定已撤销，请再在待审核预约中完成审核';
    }

	public function remove($orderid) {
		$wh = array('orderid'=>$orderid);
		D('Verify')->where($wh)->delete();
		$room = $this->where($wh)->field('room')->select();
		if($room) {
			D('RoomStatus')->updateRoomLater($room[0]['room']);
		}
		$this->where($wh)->delete();
	}
}
?>
