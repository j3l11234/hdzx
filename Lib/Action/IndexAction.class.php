<?php
class IndexAction extends BaseAction {
    public function index() {
		if(file_exists('slideshow.data.php')) {
			$slide = file('slideshow.data.php');
			unset($slide[0]);
			$this->assign('slide', $slide);
		}
		if(file_exists('announcement.data.php')) {
			$announcement = implode('', file('announcement.data.php'));
			$this->assign('announcement', $announcement);
			$this->assign('announcementTime', date('Y-m-d H:i', filemtime('announcement.data.php')));
		}
		$news = D('News')->order(array('time'=>'desc'))->limit(4)->field('content', true)->selectWithThumb();
		$this->assign('news', $news);
		$this->assign('title', '首页');
		$this->display();
    }

	public function article($id) {
		$news['thumb'] = 'images/news/default.jpg';
		if($id) {
			$n = D('News')->fetch($id);
			if($n) {
				$news = $n;
				$this->assign('title', $news['title']);
			}
		}
		$this->assign('news', $news);
		$this->display();
	}

	public function feedback() {
		if(isset($_SESSION['feedbackLimit']) && $_SESSION['feedbackLimit'] > NOW)
			$this->success('反馈留言太频繁', $_SERVER['HTTP_REFERER']);
		$data['email'] = trim(htmlspecialchars($_POST['email']));
		$data['content'] = htmlspecialchars($_POST['content']);
		$data['frompage'] = $_SERVER['HTTP_REFERER'];
		if($data['email'] == '' || trim($data['content']) == '') {
			$this->success('邮箱与留言内容不得为空', $_SERVER['HTTP_REFERER']);
		}
		D('Feedback')->add($data);
		$_SESSION['feedbackLimit'] = NOW + 30;
		$this->success('反馈留言已发送，回复将发往您的邮箱', $_SERVER['HTTP_REFERER']);
	}

	public function checkout() {
		if(PRV('checkout')) {
			$this->redirect('Order/query');
		} else {
			$this->redirect('User/login',array('prev'=>'Index:checkout'));
		}
	}

	public function room($id) {
		$room = D('Room')->where(array('roomid'=>$id))->select();
		$this->assign('title', '房间介绍');
		$this->assign('room', $room[0]);
		$this->display();
	}
}
