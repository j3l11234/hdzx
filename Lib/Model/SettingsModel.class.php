<?php
function SL($key) {
	static $label = array(
		'min_floor' => '最低楼层数',
		'max_floor' => '最高楼层数',
		'mail_server' => 'SMTP服务器',
		'mail_user' => '邮箱用户名',
		'mail_pwd' => '邮箱密码',
		'order_readme_url' => '管理条例地址'
	);
	if(isset($label[$key]))
		return $label[$key];
	else
		return $key;
}
class SettingsModel extends Model {
	private static $_settings = NULL;
	private static $_changed = array();

	public function __destruct() {
		if(self::$_settings == NULL)
			return;
		$data = array();
		foreach(self::$_changed as $k=>$v) {
			$data = array(
				'key' => $k,
				'value' => self::$_settings[$k]
			);
			$this->save($data);
		}
	}
	
	public function set($key, $value) {
		$this->settings();
		self::$_settings[$key] = $value;
		self::$_changed[$key] = 1;
	}

	public function get($key) {
		$this->settings();
		return self::$_settings[$key];
	}

	public function settings() {
		if(self::$_settings == NULL) {
			self::$_settings = array();
			foreach($this->order('align')->select() as $item) {
				self::$_settings[$item['key']] = $item['value'];
			}
		}
		return self::$_settings;
	}
}
?>
