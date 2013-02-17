<?php
return array(
	'简单锁' => array(
		'code' => 'simple',
		'hint' => '锁定某个房间（或某个类型的房间）的某个时间',
		'param' => array(
			'date' => array(
				'title' => '日期',
				'type' => 'date'
			)
		)
	),
	'日循环锁' => array(
		'code' => 'daily',
		'hint' => '从某个日期开始，每日锁定某个房间（或某个类型的房间）的某个时段',
		'param' => array(
			'start' => array(
				'title' => '开始日期',
				'type' => 'date'
			)
		)
	),
	'周循环锁' => array(
		'code' => 'weekly',
		'hint' => '每周锁定某个房间（或某个类型的房间）的某个时间',
		'param' => array(
			'day' => array(
				'title' => '每周几',
				'type' => array(
					'0' => '周日',
					'1' => '周一',
					'2' => '周二',
					'3' => '周三',
					'4' => '周四',
					'5' => '周五',
					'6' => '周六'
				),
			)
		)
	)
);
?>
