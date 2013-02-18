<?php
define('_LOCKED', 1);
define('_OCCUPIED', 2);
class RoomStatusModel extends Model {
	public function updateAll() {
		D('Room')->where('1=1')->save(array('update'=>array('%NULL')));
	}
	public function refresh() {
		$roomDao = D('Room');
		$res = $roomDao->where('ISNULL(`update`) OR `update` < DATE_SUB(NOW(), INTERVAL 1 DAY)')->field('roomid')->select();
		if($res) {
			foreach($res as $v) {
				$ids[] = $v['roomid'];
				$this->updateRoom($v['roomid']);
			}
		}
	}
	// update the time table of a certain room
	public function updateRoom($roomid) {
		D('Room')->where(array('roomid'=>$roomid))->save(array('update'=>array('%NOW', '')));
		// fetch all verified order of this room
		$orderDAO = D('Order');
		$orders = $orderDAO->where( array(
			'room' => $roomid,
			'isverified' => 1,
			'date' => array('egt', TODAY)
		) )->field('date,starthour,endhour')->select();
		$stat = array();
		if($orders) {
			foreach($orders as $item) {
				for($i = $item['starthour']; $i <= $item['endhour']; $i++) {
					$v = ($i < 10)? '0' . $i : $i;
					$stat[$item['date'] . $v] = _OCCUPIED;
				}
			}
		}

		// delete expired locks
		$lock = M('Lock');
		$lock->where( array(
			'_string' => '`expire` < now()'
		) )->delete();

		// process all locks
		$lock = $lock->select();
		if($lock) {
			foreach($lock as $item) {
				$res = $this->___process_lock($roomid, $item);
				if($res) {
					foreach($res as $f) {
						$stat[$f] = _LOCKED;
					}
				}
			}
		}
		// refresh room status
		$data = array();
		foreach($stat as $k=>$v) {
			$data[] = array(
				'room' => $roomid,
				'time' => $k,
				'islock' => $v == _LOCKED
			);
		}
		$this->where( array(
			'room' => $roomid
		) )->delete();
		if($data)
			$this->addAll($data);
		D('Room')->where(array('roomid'=>$roomid))->save(array('update'=>array('%NOW()')));
	}

	public function ___process_lock($roomid, $param) {
		$__file = 'lock/' . $param['lockcode'] . '.php';
		$param = json_decode($param['param'], true);
		$room = D('Room')->where(array('roomid'=>$roomid))->field('intro',true)->select();
		if($room) {
			$room = $room[0];
			$match = false;
			if($param['number'] == '' && $param['type'] == '') {
				$match = true;
			} else {
				if($param['number'] != '') {
					$numbers = explode(',', $param['number']);
					if(in_array($room['number'], $numbers)) {
						$match = true;
					}
				}
				if(!$match && $param['type'] != '') {
					$types = explode(',', $param['type']);
					if(in_array($room['type'], $types)) {
						$match = true;
					}
				}
			} 
			if($match && file_exists($__file)) {
				unset($match);
				unset($types);
				unset($numbers);
				return @include $__file;
			}
		}
	}

	public function isAvailable($room, $date, $start, $end = false) {
		if($end === false)
			$end = $start;
		$time = array();
		foreach(range($start, $end) as $f) {
			if($f < 10)
				$f = '0' . $f;
			$time[] = $date . $f;
		}
		return 0 == $this->where( array(
			'room' => $room,
			'time' => array('IN', $time)
		) )->count();
	}

	public function timetable($room, $datestart, $dateend = false) {
		if($dateend === false)
			$dateend = $datestart;
		$ret = array();
		$multi = true;
		if(!is_array($room)) {
			$room = array($room);
			$multi = false;
		}
		foreach($this->where( array(
			'room' => array('IN', $room),
			'time' => array( array('EGT', $datestart * 100), array('ELT', $dateend * 100 + 99) ),
		) )->select() as $f) {
			$ret[($multi ? $f['room'] . '_' : '') . $f['time']] = $f['islock'] ? _LOCKED : _OCCUPIED;
		}
		return $ret;
	}

	public function isFree($room, $date, $start, $end = NULL) {
		if(($start = 1 * $start) < 10) $start = '0' . $start;
		if(!$end)
			$end = $start;
		elseif(($end = 1 * $end) < 10) $end = '0' . $end;
		return 0 == $this->where(array(
			'room' => $room,
			'time' => array(array('egt', $date . $start), array('elt', $date . $end))
		))->count();
	}
}
?>
