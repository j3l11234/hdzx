<?php
class UserAction extends BaseAction {
	public function logout() {
		PRV(false, true);
		$this->success('注销成功', 'Index/index');
	}

	public function login($prev = NULL) {
		if($prev === NULL) {
			$prev = $_SERVER['HTTP_REFERER'];
		} else {
			$prev = U(str_replace(':', '/', $prev));
			if(PRV('userid'))
				$this->assign('msg','您没有权限做此操作，请尝试其他账户');
		}
		$this->assign('prev', $prev ? $prev : U('index'));
		$this->assign('title', '登录');
		$this->display();
	}

	public function chklogin() {
		if(isset($_SESSION['loginde']) && $_SESSION['loginde'] > NOW) {
			die('登录操作太频繁');
		}
		$_SESSION['loginde'] = NOW + 3;
		$res = D('User')->where(array('username'=>$_POST['username']))->select();
		if(!$res)
			die('没有这个用户');
		$res = $res[0];
		if($res['password'] != md5($_POST['password'])) {
			die('密码错误');
		}
		PRV('userid', $res['userid']);
		PRV('admin', $res['isadmin']);
		if($res['school'] !== NULL)
			PRV('verify', $res['school']);
		PRV('checkout', $res['ischeckout']);
		die('ok');
	}

	public function changepwd($prev = NULL) {
		if($prev === NULL) {
			$prev = $_SERVER['HTTP_REFERER'];
		} else {
			$prev = U(str_replace(':', '/', $prev));
		}
		$this->assign('prev', $prev ? $prev : U('index'));
		$this->assign('title', '密码修改');
		$this->display();
	}
	
	public function dochange() {
		$where = array('userid'=>PRV('userid'));
		$res = D('User')->where($where)->select();
		if($res) {
			$res = $res[0];
			if($res['password'] != md5($_POST['password'])) {
				die('原始密码不正确');
			}
			if($_POST['newpwd'] != $_POST['newpwd2']) {
				die('密码确认不正确');
			}
			if(D('User')->where($where)->save(array('password'=>md5($_POST['newpwd'])))) {
				die('ok');
			}
			die('数据库错误');
		}
		die('您没有登录');
	}
}
?>
