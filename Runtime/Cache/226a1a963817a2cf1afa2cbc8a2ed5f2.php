<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
.left-content {
	margin-left: 50px;
	margin-bottom: 20px;
}
.right-bar h1 {
	color: #000;
	font-size: 25px;
}
</style><div class="float-fix" style="margin-top: 30px;"><div style="float:left; width:62%;"><div class="left-content"><h1 style="color: #DA1045; margin: 0"><?php echo ($news["title"]); ?></h1></div><div class="ribbon"><div class="padding">
			发布于：<?php echo ($news["time"]); ?></div></div><div class="left-content"><?php echo ($news["content"]); ?></div></div><div class="right-bar" style="float:right; width: 30%"><div class="nav-bar"><div class="nav-bar2"><div class="nav-bar3 listed" style="padding-left:50px"><img src="__PUBLIC__/<?php echo ($news["thumb"]); ?>" style="width: 152px; height: 82px; border: 1px solid #DDD;" /></div></div></div><form style="padding-left: 10px;" action="<?php echo U('feedback');?>" method="post"><h1>反馈留言</h1><label style="color: #222">邮箱：</label><input style="width: 200px;" class="field" type="text" name="email"/><textarea style="width: 250px; height: 70px" class="field" name="content"></textarea><button onclick="submit()" class="btn">发送</button></form></div></div>