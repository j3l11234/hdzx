<?php
class AdminAction extends Action {

	public function _initialize() {
		if(!PRV('admin'))
			$this->redirect('User/login', array('prev'=>'Admin:index'));
	}
	
	public function display() {
		$dir = array(
			'全局设置' => array(
				'基本参数' => 'settings',
				'导航栏设置' => 'navibar',
				'学院列表' => 'schools'
			),
			'首页内容' => array(
				'幻灯片' => 'slideShow',
				'公告栏' => 'announcement',
			),
			'新闻内容' => array(
				'新闻列表' => 'news',
				'发布新闻' => 'editNews'
			),
			'房间管理' => array(
				'房间列表' => 'room',
				'房间锁列表' => 'lock',
			),
			'其他杂项' => array(
				'系统统计' => 'index',
				'账户设置' => 'account',
				'用户反馈' => 'feedback',
				'网站首页' => 'Index/index',
			),
			'退出后台' => 'User/logout'
		);
		function getTitle(&$dir) {
			foreach($dir as $k => $v) {
				if(is_array($v)) {
					$ret = getTitle($v);
					if($ret)
						return $ret;
				} elseif(__ACTION__ == U($v)) {
					return $k;
				}
			}
			return false;
		}
		$title = getTitle($dir);
		if($title)
			$this->assign('title', $title);
		$this->assign('dir', $dir);
		Action::display('header');
		Action::display();
		Action::display('footer');
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
	
	// Statistics
	public function index() {
		$this->assign('roomCount', D('Room')->count());
		$this->assign('orderTotalCount', D('Order')->count());
		$this->assign('orderPassTotalCount', D('Order')->where(array('isverified'=>1))->count());
		$month = date('Ym00', NOW);
		$this->assign('orderMonthCount', D('Order')->where('`date` > ' . $month)->count());
		$this->assign('orderPassMonthCount', D('Order')->where(array('isverified'=>1,'date'=>array('gt',$month)))->count());
		$this->assign('feedbackCount', D('Feedback')->count());
		$this->assign('feedbackMonthCount', D('Feedback')->where('`time` > '.$month)->count());
		$this->display();
	}

    // Statistics for json query
    public function stats() {
        function P($key, $default) {
            if(isset($_POST[$key]))
                return $_POST[$key];
            return $default;
        }
        $school = P('school', 0);
        $dateStart = str_replace('-', '', P('dateStart', 10000000));
        $dateEnd = str_replace('-', '', P('dateEnd', 99999999));
        $roomtype = P('roomType', 0);
        $where = array();
        if($school)
            $where['school'] = $school;
        if($dateStart && $dateEnd)
            $where['date'] = array(
                array('egt', $dateStart),
                array('elt', $dateEnd)
            );
        if($roomtype) {
            $type = D('Room')->types();
            $roomtype = $type[$roomtype - 1];
            $res = D('Room')->where(array('type'=>$roomtype))->field('roomid')->select();
            if($res) {
                $rids = array();
                foreach($res as $v) {
                    $rids[] = $v['roomid'];
                }
                $where['room'] = array('in', $rids);
            }
        }
        $result = array();
        $result['oTotal'] = D('Order')->where($where)->count();
        $where['_string'] = 'isverified = 1';
        $result['oAccept'] = D('Order')->where($where)->count();
        $sql = D('Verify')->alias('V')->where('O.orderid = V.orderid and V.ispass = 1 and V.verifier = 0')->buildSql();
        $where['_string'] = 'isverified = 1 and exists ' . $sql;
        $result['oAutoAccept'] = D('Order')->alias('O')->where($where)->count();
        $sql = D('Verify')->alias('V')->where('O.orderid = V.orderid and V.ispass = 0 and V.verifier = 0')->buildSql();
        $where['_string'] = 'isverified = 0 and exists ' . $sql;
        $result['oAutoReject'] = D('Order')->alias('O')->where($where)->count();
        $result['rt'] = $roomtype;
        
        function I($arr) {
            $ret = array();
            foreach($arr as $v) {
                $ret[] = 'info like '.str_replace('\\u', '__', json_encode('%'.$v.'%'));
            }
            return implode(' or ', $ret);
        }
        function PC($v, $t) {
            return (ceil(1000 * $v / $t) / 10) . '%';
        }
        $where['_string'] = I(array('自习', '学习', '练习'));
        $result['oTagStudy'] = D('Order')->where($where)->count();
        $result['oTagStudy'] .= ' (' . PC($result['oTagStudy'], $result['oTotal']) . ')';
        $where['_string'] = I(array('比赛', '大赛', '测试'));
        $result['oTagCompetition'] = D('Order')->where($where)->count();
        $result['oTagCompetition'] .= ' (' . PC($result['oTagCompetition'], $result['oTotal']) . ')';
        $where['_string'] = I(array('讨论', '会议', '开会'));
        $result['oTagMeeting'] = D('Order')->where($where)->count();
        $result['oTagMeeting'] .= ' (' . PC($result['oTagMeeting'], $result['oTotal']) . ')';
        $where['_string'] = I(array('演讲', '表演', '文艺'));
        $result['oTagPresentation'] = D('Order')->where($where)->count();
        $result['oTagPresentation'] .= ' (' . PC($result['oTagPresentation'], $result['oTotal']) . ')';
        $where['_string'] = I(array('合唱', '排练', '彩排'));
        $result['oTagRehearsal'] = D('Order')->where($where)->count();
        $result['oTagRehearsal'] .= ' (' . PC($result['oTagRehearsal'], $result['oTotal']) . ')';
        die(json_encode($result));
    }

	// configure list of schools
	public function schools() {
		$this->assign('schools', 
			D('School')->order(array('align'=>'asc', 'schoolid'=>'asc'))->select() );
		$this->display();
	}

	public function editSchool() {
		$ret['success'] = false;
		$dao = D('School');
		$data['name'] = htmlspecialchars($_POST['name']);
		$ret['name'] = $data['name'];
		if($_POST['schoolid'] == 0) {
			$id = $dao->add($data);
			$ret['schoolid'] = $id;
			$ret['success'] = $dao->where(array('schoolid'=>$id))->save(array('align'=>$id)) ? true : false;
		} else {
			$id = $_POST['schoolid'];
			$ret['schoolid'] = $id;
			$ret['success'] = $dao->where(array('schoolid'=>$id))->save($data) ? true : false;
		}
		die(json_encode($ret));
	}

	public function swapSchool() {
		$dao = D('School');
		$ret = true;
		$alignSelf = $dao->where(array('schoolid'=>$_POST['id']))->getField('align');
		$alignTarget = $dao->where(array('schoolid'=>$_POST['target']))->getField('align');
		$ret = $ret && $dao->where(array('schoolid'=>$_POST['id']))->save(array('align'=>$alignTarget));
		$ret = $ret && $dao->where(array('schoolid'=>$_POST['target']))->save(array('align'=>$alignSelf));
		$ret = array('success'=>$ret);
		die(json_encode($ret));
	}

	public function deleteSchool($id) {
		D('School')->where(array('schoolid'=>$id))->delete();
		die(json_encode(array('success'=>true)));
	}

	// Modify and publish news
	public function news($page = 1) {
		$dao = D('News');
		$itemPerPage = 10;
		$news = $dao->order(array('time'=>'desc'))->page($page)->limit($itemPerPage)->field('content', true)->selectWithThumb();
		$pageTotal = ceil($dao->count() / $itemPerPage);
		$this->assign('news', $news);
		$this->assign('totalPage', $pageTotal);
		$this->assign('currentPage', $page);
		$this->display();
	}

	public function editNews($id = 0) {
		$news['thumb'] = 'images/news/default.jpg';
		if($id) {
			$n = D('News')->fetch($id);
			if($n)
				$news = $n;
		}
		$this->assign('news', $news);
		$this->display();
	}

	public function thumbNews() {
		$id = 0;
		if(isset($_GET['id']))
			$id = $_GET['id'] * 1;
		$result['success'] = false;
		$result['path'] = 0;
		if($id == 0) {
			$path = 'temp/' . NOW . rand(100, 999) . '.jpg';
		} else
			$path = 'images/news/' . $id . '.jpg';
		$result['path'] = $path;
		$path = 'Public/' . $path;
		if(move_uploaded_file($_FILES['upload']['tmp_name'], $path)) {
			$im = @imagecreatefromstring(file_get_contents($path));
			if($im) {
				$tw = 152; $th = 82;
				list($width, $height, $type, $attr) = getimagesize($path);
				$pw = $width;
				$ph = $height;
				if( ($tw / $th) > ($width / $height) ) {
					$height = $height * $tw / $width;
					$width = $tw;
				} else {
					$width = $width * $th / $height;
					$height = $th;
				}
				$nim = imagecreatetruecolor($tw, $th);
				$dx = ($tw - $width) / 2;
				$dy = ($th - $height) / 2;
				imagecopyresampled($nim, $im, $dx, $dy, 0, 0, $width, $height, $pw, $ph);
				imagedestroy($im);
				imagejpeg($nim, $path);
				imagedestroy($nim);
				$result['success'] = true;
			}
		}
		die(json_encode($result));
	}

	public function saveNews() {
		$id = 0;
		if(isset($_GET['id']))
			$id = $_GET['id'] * 1;
		$dao = D('News');
		if($id) {
			$_POST['newsid'] = $id;
		}
		$_POST['summary'] = mb_substr(preg_replace('/(&nbsp;|\s)+/', ' ', preg_replace('/\<.+?\>/', ' ', $_POST['content'])), 0, 100);
		$dao->create();
		if($id) {
			$dao->save();
		} else {
			$id = $dao->add();
			if($_POST['changethumb']) {
				rename('Public/' . $_POST['changethumb'], 'Public/images/news/' . $id . '.jpg');
			}
		}
		$this->success('新闻发布成功', 'news');
	}

	public function deleteNews($id) {
		D('News')->remove($id);
		$this->success('新闻已删除', 'news');
	}

	// configure the slideshow of the homepage
	public function slideShow() {
		if(file_exists('slideshow.data.php')) {
			$slide = file('slideshow.data.php');
			unset($slide[0]);
		} else {
			$slide = array();
		}
		$this->assign('slide', $slide);
		$this->display();
	}

	public function slideUpload() {
		$n = explode('.', $_FILES['image']['name']);
		$c = count($n);
		if($c < 2) {
			$return['success'] = false;
		} else {
			$newPath = NOW . rand(100, 999) . '.' . $n[$c -1];
			$return['path'] = $newPath;
			$newPath = 'Public/images/slides/' . $newPath;
			$return['success'] = move_uploaded_file($_FILES['image']['tmp_name'], $newPath);
		}
		die(json_encode($return));
	}

	public function saveSlideShow() {
		$dir = 'Public/images/slides/';
		$items = array_values($_POST['item']);
		foreach(scandir($dir) as $file) {
			if($file{0} == '.')
				continue;
			if(!in_array($file, $items)) {
				unlink($dir . $file);
			}
		}
		$fp = fopen('slideshow.data.php', 'w');
		$items = array_merge(array('<?php die();?>'), $items);
		fwrite($fp, implode(chr(10), $items));
		fclose($fp);
		$this->success('幻灯片设置保存成功', 'slideShow');
	}

	// configure basic settings
	public function settings() {
		$this->assign('settings', D('Settings')->settings());
		$this->display();
	}

	public function saveSettings() {
		D('Settings')->set($_POST['key'], $_POST['value']);
	}

	// modify the announcement on the home page
	public function announcement() {
		$content = '';
		if(file_exists('announcement.data.php')) {
			$content = implode('', file('announcement.data.php'));
		}
		$this->assign('announcement', $content);
		$this->display();
	}

	public function saveAnnouncement() {
		$fp = fopen('announcement.data.php', 'w');
		fwrite($fp, $_POST['announcement']);
		fclose($fp);
		$this->success('公告保存成功', 'announcement');
	}

	// configure the navigation menu on top of all pages
	public function navibar() {
		$navi = array();
		if(file_exists('navibar.data.php')) {
			$navi = file('navibar.data.php');
			unset($navi[0]);
			if(isset($navi[1]))
				$navi = json_decode($navi[1], true);
		}
		$this->assign('navibar', $navi);
		$this->display();
	}

	public function saveNavibar() {
		$data = array_pack($_POST);
		$fp = fopen('navibar.data.php', w);
		fwrite($fp, '<?php die();?>' . chr(10));
		fwrite($fp, json_encode($data));
		fclose($fp);
		$this->success('导航栏保存成功', 'navibar');
	}

	public function room() {
		$this->assign('rooms', D('Room')->order('number')->field('intro', true)->select());
		$this->display();
	}

	public function editRoom($id=0) {
		$settings = D('Settings');
		$m = $settings->get('max_floor');
		$floors = array();
		for($i = $settings->get('min_floor'); $i <= $m; $i++) {
			$floors[$i] = $i . '层';
		}
		if($id > 0) {
			$room = D('Room')->where(array('roomid'=>$id))->select();
			if($room) {
				$this->assign('room', $room[0]);
			} else {
				$id = 0;
			}
		}
		$this->assign('roomid', $id);
		$this->assign('floors', $floors);
		$this->assign('title', '编辑房间');
		$this->display();
	}

	public function deleteRoom($id) {
		D('Room')->where(array('roomid'=>$id))->delete();
		$this->success('房间已删除', 'room');
	}

	public function batchRoom() {
	}

	public function saveRoom() {
		$dao = D('Room');
		$_POST['isopen'] = isset($_POST['isopen']);
		$_POST['needsecure'] = isset($_POST['needsecure']);
		$_POST['hasmedia'] = isset($_POST['hasmedia']);
		$_POST['autoverify'] = isset($_POST['autoverify']);
		if($_POST['maxhour'] < 1)
			$_POST['maxhour'] = 99;
		$dao->create();
		if($_POST['roomid'] == 0) {
			$id = $dao->add();
		} else {
			$dao->save();
			$id = $_POST['roomid'];
		}
		$this->success('房间保存成功', U('editRoom', array('id'=>$id)));
	}

	// modify locks
	public function lock() {
		$this->assign('locks', D('Lock')->select());
		$this->display();
	}

	public function editLock($id=0) {
		$this->assign('title', '编辑房间锁');
		$this->assign('lockCodes', include('lock/info.php'));
		$this->assign('lockid', $id);
		if($id) {
			$ret = D('Lock')->where(array('lockid'=>$id))->select();
			if($ret) {
				$ret = $ret[0];
				$ret = array_merge($ret, json_decode($ret['param'], true));
				unset($ret['param']);
				$this->assign('lock', $ret);
			} else {
				$id = 0;
			}
		}
		$this->display();
	}

	public function saveLock() {
		$data['title'] = htmlspecialchars($_POST['title']); unset($_POST['title']);
		$data['expire'] = $_POST['expire']; unset($_POST['expire']);
		$data['lockcode'] = $_POST['lockcode']; unset($_POST['lockcode']);
		$id = $_POST['lockid']; unset($_POST['lockid']);
		$data['param'] = json_encode($_POST);
		$data['number'] = preg_replace('/[^0-9]+/', ',', $data['number']);
		$data['hours'] = preg_replace('/[^0-9]+/', ',', $data['hours']);
		if($data['expire'] == '')
			$data['expire'] = array('%NULL');
		$dao = D('Lock');
		if($id) {
			$dao->where(array('lockid'=>$id))->save($data);
		} else {
			$id = $dao->add($data);
		}
		D('RoomStatus')->updateAll();
		$this->success('房间锁保存成功', U('editLock', array('id'=>$id)));
	}

	public function deleteLock($id) {
		D('Lock')->where(array('lockid'=>$id))->delete();
		D('RoomStatus')->updateAll();
		$this->success('锁已删除', 'lock');
	}

	// read and reply feedbacks
	public function feedback($page = 1) {
		$dao = D('Feedback');
		$itemPerPage = 5;
		$this->assign('pageTotal', ceil($dao->count() / $itemPerPage));
		$this->assign('pageCurrent', $page);
		$this->assign('feedback', $dao->order(array('time'=>'desc'))->limit($itemPerPage)->page($page)->select());
		$this->display();
	}

	public function replyFeedback() {
		$dao = D('Feedback');
		$where = array('feedbackid' => $_POST['feedbackid']);
		$res = $dao->where($where)->field('email,reply')->select();
		if($res) {
			$res = $res[0];
			$_POST['content'] = htmlspecialchars($_POST['content']);
			if(sendmail($res['email'], '学生活动服务中心 - 反馈回复', htmlspecialchars($_POST['content']))) {
				$data = $where;
				$data['reply'] = '<div class="title">回复 ' . date('Y-m-d H:i:s', NOW) . '</div><pre class="content">' . $_POST['content'] . '</pre>' . $res['reply'];
				$dao->save($data);
				$this->success('回复发送成功:)', 'feedback');
			}
		}
		$this->success('回复发送失败！', 'feedback');
	}

	public function deleteFeedback($id) {
		$dao = D('Feedback');
		$where = array('feedbackid' => $id);
		$dao->where($where)->delete();
		$this->success('留言及其回复均已删除', 'feedback');
	}

	// list, modify and add admin/operator accounts
	public function account() {
		$this->assign('schools', 
			D('School')->order(array('align'=>'asc', 'schoolid'=>'asc'))->select() );
		$this->assign('accounts', D('user')->select());
		$this->display();
	}

	public function editAccount() {
		if(isset($_POST['school']) && $_POST['school']=='null')
			$_POST['school'] = array('%NULL');
		if(isset($_POST['password']))
			$_POST['password'] = array('%MD5', $_POST['password']);
		$dao = D('User');
        $result = D('User')->where('`username`="admin"')->field('userid')->select();
        $adminUid = $result[0]['userid'];
        if($_POST['userid'] == $adminUid) {
			$_POST['school'] = 0;
			$_POST['isadmin'] = 1;
			$_POST['ischeckout'] = 1;
        }
		$dao->create();
		if(isset($_POST['userid']) && $_POST['userid']) {
			$dao->save();
		} else {
			$dao->add();
			$this->success('用户添加成功', 'account');
		}
		die();
	}

	public function deleteAccount($id) {
		D('User')->where(array('userid'=>$id,'username'=>array('neq','admin')))->delete();
	}
}
?>
