<?php if (!defined('THINK_PATH')) exit();?><!doctype html><html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8"/><link rel="stylesheet" type="text/css" href="__PUBLIC__/common_style.css"/><script type="text/javascript" src="__PUBLIC__/jquery.js"></script><script type="text/javascript">
		$(function() {
			$('button.btn').each(function() {
				$(this).html('<span>' + $(this).html() + '</span>');
			});
            var ctx = $('#banner');
            ctx = ctx[0].getContext('2d');
            ctx.shadowColor = '#333';
            ctx.shadowOffsetX = -1;
            ctx.shadowOffsetY = -1;
            ctx.shadowBlur = 4;
            ctx.font = 'bold 40px 微软雅黑';
            var gradient = ctx.createLinearGradient(0,0, 200, 900);
            gradient.addColorStop(0, '#AAA');
            gradient.addColorStop(0.05, '#EEE');
            gradient.addColorStop(0.2, '#BBB');
            gradient.addColorStop(0.27, '#EEE');
            gradient.addColorStop(1, '#AAA');
            ctx.fillStyle = gradient;
            ctx.fillText('北京交通大学  学生活动服务中心场地预约', 12, 92);
            $('#banner2').attr('src', $('canvas')[0].toDataURL());
        });
		</script><title><?php echo ($title); ?> - 学生活动服务中心</title></head><body><div class="page-wrap"><div class="main-menu-wrap float-fix"><div class="main-menu-padding"><div class="head"></div><div class="body"></div></div><div class="padding-left"></div><ul class="main-menu"><li><a href="<?php echo U('Index/index');?>">首页</a></li><li><a href="<?php echo U('Order/index');?>">房间查询</a></li><li><a href="<?php echo U('Order/query');?>">预约查询</a></li><?php foreach($navibar as $item) { ?><li><a href="<?php echo ($item["url"]); ?>"><?php echo ($item["title"]); ?></a></li><?php } ?></ul><div class="padding-right"></div></div><div class="page-body"><div class="logo"><canvas width="750" height="128" id="banner"></canvas></div>