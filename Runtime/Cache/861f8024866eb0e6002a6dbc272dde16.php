<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
var doLogin = false;
function login() {
	if(doLogin)
		return false;
	doLogin = true;
	$.post('<?php echo U('chklogin');?>', $('#loginForm').serialize(), function(result) {
		doLogin = false;
		if(result == 'ok')
			document.location.href = '<?php echo ($prev); ?>';
		else {
			$('#error_msg').html(result);
			$('#error_msg').animate({textIndent:10}, 100)
							.animate({textIndent:0}, 100)
							.animate({textIndent:10}, 100)
							.animate({textIndent:0}, 100)
		}
	}).error(function() {
		doLogin = false;
	});
	return false;
}
</script><form onsubmit="return login()" id="loginForm" style="padding: 20px; text-align: center"><h2 style="color: #F00; font-size: 15px" id="error_msg"><?php echo ($msg); ?></h2><h1>管理员 / 服务人员登录</h1><label>用户名：</label><input type="text" name="username" class="field"/><br/><label>密&nbsp;&nbsp;&nbsp;&nbsp;码：</label><input type="password" name="password" class="field"/><br/><br/><input type="submit" style="visibility: hidden" value="登录"/><button onclick="login()" class="btn">登录</button></form>