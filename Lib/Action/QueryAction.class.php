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
}
?>
