<?php if (!defined('THINK_PATH')) exit();?><!doctype html><html><head><title>系统反馈信息</title><script type="text/javascript">
		var counter = <?php echo ($delay); ?>;
		function countDown() {
			document.getElementById('countdown').innerHTML = counter;
			if(counter > 0)
				setTimeout(countDown, 1000);
			else
				document.location.href = '<?php echo ($url); ?>';
			counter--;
		}
		</script></head><body onload="countDown()"><div style="text-align: center; padding-top: 100px"><h1 style="font-size: 40px; color: #222;"><?php echo ($message); ?></h1><div style="font-size: 13px">
				将在<span id="countdown"><?php echo ($delay); ?></span>秒后跳转，或者<a href="<?php echo ($url); ?>">点击这里直接跳转</a></div></div></body></html>