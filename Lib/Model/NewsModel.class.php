<?php
class NewsModel extends Model {
	public function selectWithThumb() {
		$news = $this->select();
		if($news)
			foreach($news as $k => $v) {
				if(file_exists('Public/images/news/' . $v['newsid'] . '.jpg')) {
					$news[$k]['thumb'] = 'images/news/' . $v['newsid'] . '.jpg';
				} else {
					$news[$k]['thumb'] = 'images/news/default.jpg';
				}
			}
		return $news;
	}

	public function fetch($id) {
		$ret = $this->where(array('newsid'=>$id))->selectWithThumb();
		if($ret)
			return $ret[0];
		return $ret;
	}

	public function remove($id) {
		$this->where(array('newsid'=>$id))->delete();
		if(file_exists('Public/images/news/' . $id . '.jpg')) {
			unlink('Public/images/news/' . $id . '.jpg');
		}
	}
}
?>
