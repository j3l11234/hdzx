<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
.left-content {
	margin-left: 50px;
	margin-bottom: 20px;
}
.right-bar h1 {
	color: #000;
	font-size: 25px;
}
.right-bar .nav-bar label {
	font-weight: bold;
}
</style><div class="float-fix" style="margin-top: 30px;"><div style="float:left; width:62%;"><div class="left-content"><h1 style="color: #AA1045; margin: 0">
				 房间：<?php echo ($room["name"]); ?> (<?php echo ($room["number"]); ?>)
			</h1></div><div class="ribbon"><div class="padding" style="font-size: 12px">
			时间表最后更新于：
			&nbsp;&nbsp;
			<?php echo ($room["update"]); ?></div></div><div class="left-content"><?php echo ($room["intro"]); ?></div></div><div class="right-bar" style="float:right; width: 30%"><div class="nav-bar"><div class="nav-bar2"><div class="nav-bar3 listed" style="padding-left: 20px; line-height: 30px; color: #222"><label>楼层：</label><?php echo ($room["floor"]); ?>
					&nbsp;&nbsp;
					<label>容纳人数：</label><?php echo ($room["capacity"]); ?><br/><label>类型：</label><?php echo ($room["type"]); ?><br/><label>房间设施：</label><?php echo ($room["facility"]); ?></div></div></div><form style="padding-left: 10px;" action="<?php echo U('feedback');?>" method="post"><h1>反馈留言</h1><label style="color: #222">邮箱：</label><input style="width: 200px;" class="field" type="text" name="email"/><textarea style="width: 250px; height: 70px" class="field" name="content"></textarea><button onclick="submit()" class="btn">发送</button></form></div></div>