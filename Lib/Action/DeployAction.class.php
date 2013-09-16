<?php
class DeployAction extends Action {

/*	public function __construct() {
		die();
	}
*/	
	public function deployRoom() {
		echo '<meta charset="utf8"/>';
		$Mor = M('oldroom');
		$Mot = M('oldroomtype');
		$result = $Mot->select();
		$types = array();
		foreach($result as $f) {
			$types[$f['tid']] = $f['title'];
		}
		$data = array();
		$result = $Mor->select();
		foreach($result as $f) {
			$row = array(
				'roomid' => $f['rid'],
				'name' => str_replace('('.$f['rnum'].')', '', $f['name']),
				'number' => $f['rnum'],
				'floor' => $f['floor'],
				'type' => $types[$f['tid']],
				'isopen' => $f['isopen'],
				'intro' => $f['desc_room'],
				'capacity' => $f['capacity'],
				'facility' => $f['desc_facility'],
				'needsecure' => $f['checklevel'] > 1,
				'hasmedia' => 1,
				'autoverify' => $f['desc_limit'] == '网上自动审批' ? 1 : 0,
				'maxhour' => 99
			);
			if($f['checklevel'] == 1)
				$row['maxhour'] = 2;
			$data[] = $row;
		}
		print_r($data);
		M('room')->addAll($data);
	}

	public function deploySchool() {
		$result = M('oldschool')->select();
		$data = array();
		foreach($result as $f) {
			$data[] = array(
				'schoolid' => $f['sid'],
				'align' => $f['sid'],
				'name' => $f['title']
			);
		}
		M('school')->addAll($data);
	}

	public function deployUser() {
		$result = M('olduser')->select();
		$data = array();
		foreach($result as $f) {
			$data[] = array(
				'username' => $f['username'],
				'password' => $f['password'],
				'school' => $f['sid'],
				'isadmin' => 0,
				'ischeckout' => 0,
			);
		}
		$result = M('oldadmin')->select();
		$i = 0;
		foreach($result as $f) {
			$data[] = array(
				'username' => $f['user_name'],
				'password' => md5($f['user_password']),
				'school' => array('%NULL'),
				'isadmin' => $i == 0,
				'ischeckout' => $i > 0,
			);
			$i++;
		}
		print_r($data);
		M('user')->addAll($data);
		echo M()->getLastSql();
	}

 	private function roomidmap() {
		$result = M('room')->field('roomid,name,number')->select();
		$ret = array();
		foreach($result as $f) {
			$ret[$f['roomid']] = $f['name'] . '(' . $f['number'] . ')';
		}
		return $ret;
	}

	public function deployOrder() {
		$idmap = $this->roomidmap();
		$result = M('oldorder')->where('`date` > 20130000')->select();
		$data = array();
		$verifyState = array(
			'0' => '0', // pending 
			'1' => '3', // pending for top level verify
			'2' => '1', // pass
			'3' => '9', // reserved
			'4' => '2', // reject
		);
		foreach($result as $f) {
			$row = array(
				'room' => $f['rid'],
				'orderer' => $f['st_no'],
				'school' => $f['sid'],
				'date' => $f['date'],
				'starthour' => $f['start'],
				'endhour' => $f['end'],
				'time' => $f['createtime'],
				'isverified' => $verifyState[$f['state']]
			);
			if($f['verify'])
				$row['checkouttime'] = array('%FROM_UNIXTIME', $f['verify']);
			else
				$row['checkouttime'] = array('%NULL');
			$info = json_decode($f['detail'], true);
			$row['info'] = array(
				'contact' => $info['contact'],
				'content' => $info['describe'],
				'orderer' => $info['name'],
				'roomname' => $idmap[$f['rid']],
				'needmedia' => $info['usemedia'],
				'topic' => $info['title'],
				'unitname' => $info['unitname'],
				'people' => $info['count'],
			);
			if(isset($info['safety']))
				$row['info']['secure'] = $info['safety'];
			$row['info'] = json_encode($row['info']);
			$data[] = $row;
		}
		M('order')->addAll($data);
		die(M()->getLastSql());
	}
}
?>
