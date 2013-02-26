<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
.settings {
	overflow: hidden;
	zoom: 1;
}
.settings .item {
	float: left;
	padding: 10px;
	margin: 5px;
	border: 1px solid #CCC;
	font-size: 13px;
	width: 440px;
}
.settings .item label {
	margin-right: 10px;
}
.settings .item input {
	background: #EEE;
	border: 0;
	border-bottom: 1px solid #CCC;
	padding: 5px;
	outline: none;
	width: 250px;
}
</style><script type="text/javascript">
function change(item) {
	$.post('<?php echo U('saveSettings');?>',{"key":$(item).attr('id'),"value":$(item).val()});
}
</script><div class="settings"><?php foreach($settings as $key=>$value) { ?><div class="item"><table cellspacing="0" cellpadding="0" style="width: 400px"><tr><td width="30%"><label for="<?php echo ($key); ?>"><?php echo SL($key);?>：</label></td><td width="70%"><input type="text" id="<?php echo ($key); ?>" value="<?php echo htmlspecialchars($value);?>" onchange="change(this)"/></td></tr></table></div><?php } ?></div>