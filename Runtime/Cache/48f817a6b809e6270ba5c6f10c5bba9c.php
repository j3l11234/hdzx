<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
.locks {
	overflow: hidden;
	zoom: 1;
}
.locks .item {
	float: left;
	border: 1px solid #557;
	width: 220px;
	height: 30px;
	line-height: 30px;
	text-align: center;
	font-size: 14px;
	color: #335;
	background: #EEE;
	margin: 5px;
}
.locks .item:hover {
	background: #FFF;
}
</style><div class="locks"><?php foreach($locks as $item) { ?><a href="<?php echo U('editLock', array('id'=>$item['lockid']));?>" class="item"><?php echo ($item["title"]); ?></a><?php } ?><a href="<?php echo U('editLock');?>" class="item">
		+ 添加锁 +
	</a></div>