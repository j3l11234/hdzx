<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
.rooms {
	overflow: hidden;
	zoom: 1;
}
.rooms .item {
	float: left;
	border: 1px solid #557;
	width: 220px;
	height: 30px;
	line-height: 30px;
	text-align: center;
	font-size: 14px;
	color: #335;
	background: #DDD;
	margin: 5px;
}
.rooms .item:hover {
	background: #EEE;
}
.rooms .open {
	background: #FFF;
}
</style><div class="rooms"><?php foreach($rooms as $item) { ?><a href="<?php echo U('editRoom', array('id'=>$item['roomid']));?>" class="item <?php echo $item['isopen']?'open':'';?>"><?php echo ($item["name"]); ?>(<?php echo ($item["floor"]); ?>层 <?php echo ($item["number"]); ?>)
		</a><?php } ?><a href="<?php echo U('editRoom');?>" class="item open">
		+ 添加房间 +
	</a></div>