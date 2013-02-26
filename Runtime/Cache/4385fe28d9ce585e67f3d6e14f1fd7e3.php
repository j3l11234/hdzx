<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
$(function() {
	$('#id')[0].focus();
});
function go() {
	document.location.href = '<?php echo U('query', array('id'=>''));?>' + $('#id').val();
	return false;
}
function checkout(id, item) {
	$.get('<?php echo U('checkout', array('id'=>''));?>' + id, function(result) {
		if(result && result.result) {
			$(item).parent().append('<b>' + result.date + '</b>');
			$(item).remove();
		}
	}, 'JSON');
}
</script><?php if($orders) { ?><style type="text/css">
pre {
	margin: 5px;
	padding: 5px;
	font-family: arial;
	font-size: 13px;
	line-height: 20px;
	background: #AFB;
	border: 1px solid #7B9;
}
</style><div class="float-fix"><div style="float: right; width: 35%" class="nav-bar"><div class="nav-bar2"><div class="nav-bar3"><a class="active">继续查询</a><form onsubmit="return go()"  style="text-align: center; padding: 10px; padding-right: 50px; color: #222"><label>学号：</label><input type="text" class="field" id="id" value="<?php echo ($id); ?>"/><button class="btn" onclick="go()">查询</button><br/><i>注意：只有预约日期在今日或之后才会显示</i></form><?php if(PRV('checkout')) { ?><div style="padding: 10px; padding-right: 50px; text-align: center"><br/><br/><a href="<?php echo U('User/logout');?>"><button class="btn">注销</button></a></div><?php } ?></div></div></div><div style="float: left; width: 63%; line-height: 40px; color: #222;"><?php $status = array('待审核', '审核通过', '审核驳回', '待校级审核'); $i = 0; foreach($orders as $order) { $i++; $order['info'] = json_decode($order['info'], true); ?><div class="ribbon"><div class="padding"><?php echo ($i); ?>. <?php echo ($order["info"]["topic"]); ?></div></div><div style="padding-left: 50px"><label>预约人：</label><b><?php echo ($order["info"]["orderer"]); ?> (<?php echo ($order["orderer"]); ?>)</b><br/><label>预约房间：</label><b><?php echo ($order["info"]["roomname"]); ?></b>
				&nbsp;
				<a href="<?php echo U('Index/room', array('id'=>$order['room']));?>" target="_blank">房间详情</a>
				&nbsp;&nbsp;
				<label>预约时间：</label><b><?php echo (num2date($order["date"])); ?> &nbsp;
					<?php echo ($order["starthour"]); ?>点 - <?php echo $order['endhour'] + 1;?>点
				</b><br/><label>审批状态：</label><b><?php echo ($status[$order['isverified']]); ?></b><?php if($order['isverified'] == 1) { ?>
				&nbsp;&nbsp;
				<label>开门条领取时间：</label><b><?php if($order['checkouttime']) { echo ($order["checkouttime"]); } elseif(PRV('checkout')) { ?><button class="btn" onclick="checkout(<?php echo ($order["orderid"]); ?>, this)">发放开门条</button><?php } else { ?>
					尚未领取
					<?php } ?></b><?php } ?></div><?php } ?></div></div><?php } else { ?><form onsubmit="return go()" style="text-align: center; padding-bottom: 30px;"><h1>请输入学号</h1><input type="text" class="field" id="id"/><button class="btn" onclick="go()">查询</button><?php if($id) { ?><div style="color: #F00">没有找到符合条件尚未过期的预约</div><?php } ?></form><?php if(PRV('checkout')) { ?><div style="padding: 20px; text-align: center"><a href="<?php echo U('User/logout');?>"><button class="btn">注销</button></a></div><?php } } ?>