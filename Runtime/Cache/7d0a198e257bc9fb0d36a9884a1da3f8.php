<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="__PUBLIC__/kindeditor-min.js"></script><script type="text/javascript">
$(function() {
	KindEditor.create('textarea');
});
function showtip() {
	$.get('<?php echo U('Query/roomtypes');?>', function(result) {
		$('#tips').html('');
		if(result) {
			for(k in result) {
				$('#tips').append('<a href="javascript:void(0)" onclick="settype(this)">' + result[k] + '</a>');
			}
			$('#tips').slideDown(200);
		}
	}, 'JSON');
}
function hidetip() {
	setTimeout(function() {
		$('#tips').slideUp(200);
	}, 100);
}
function settype(item) {
	$('#field_type').val($(item).html());
}
function remove() {
	var dialog = KindEditor.dialog({
		title: '操作确认',
		body: '<div style="padding: 10px">真的要删除这个房间吗？</div>',
		yesBtn: {
			name: '删除',
			click: function() {
				document.location.href = '<?php echo U('deleteRoom', array('id'=>$roomid));?>';
			}
		},
		noBtn: {
			name: '取消',
			click: function() {
				dialog.remove();
			}
		},
		closeBtn: {
			name: '取消',
			click: function() {
				dialog.remove();
			}
		}
	});
}
</script><style type="text/css">
.field {
	outline: none;
	padding: 5px;
	background: #EEE;
	border: 0; border-bottom: 1px solid #AAA;
	width: 150px;
}
#tips {
	position: absolute;
	width: 150px;
	padding: 4px;
	background: #F2F2F2;
	border: 1px solid #CCC;
	display: none;
	top: -2px;
	left: 0;
}
#tips a {
	display: block;
	padding: 2px 5px;
	color: #557;
	font-size: 13px;
}
#tips a:hover {
	background: #FFF;
}
</style><form action="<?php echo U('saveRoom');?>" method="post"><input type="hidden" name="roomid" value="<?php echo ($roomid); ?>"/><table cellspacing="10" cellpadding="0"><tr><td style="text-align: right"><label>房间名：</label></td><td><input type="text" name="name" class="field" value="<?php echo ($room["name"]); ?>"/></td><td rowspan="8"><label style="display: block; padding: 10px;">房间介绍：</label><textarea name="intro" style="width: 670px; height: 400px"><?php echo ($room["intro"]); ?></textarea></td></tr><tr><td style="text-align: right"><label>房间号：</label></td><td><input type="text" name="number" class="field" value="<?php echo ($room["number"]); ?>"/></td></tr><tr><td style="text-align: center" colspan="2"><label>楼层：</label><select name="floor"><?php echo options($floors,$room['floor']);?></select>
				&nbsp;&nbsp;&nbsp;
				<label>开放：</label><input type="checkbox" name="isopen" <?php echo $room['isopen'] ? 'checked':'';?>/></td></tr><tr><td style="text-align: right"><label>房间类型：</label></td><td><input type="text" name="type" class="field" id="field_type" onfocus="showtip()" onblur="hidetip()" value="<?php echo ($room["type"]); ?>"/><div style="position:relative"><div id="tips"></div></div></td></tr><tr><td style="text-align: right"><label>容纳人数：</label></td><td><input type="text" name="capacity" class="field" value="<?php echo ($room["capacity"]); ?>"/></td></tr><tr><td style="text-align: right"><label>房间设施：</label></td><td><input type="text" name="facility" class="field" value="<?php echo ($room["facility"]); ?>"/></td></tr><tr><td style="text-align: center" colspan="2"><label for="needsecure">需要填写安保措施：</label><input type="checkbox" name="needsecure" id="needsecure" <?php echo $room['needsecure']?'checked':'';?>/><br/><label for="hasmedia">可提供多媒体设备：</label><input type="checkbox" name="hasmedia" id="hasmedia" <?php echo $room['hasmedia']?'checked':'';?>/><br/><label for="autoverify">系统自动审批预约：</label><input type="checkbox" name="autoverify" id="autoverify" <?php echo $room['autoverify']?'checked':'';?>/><br/><label for="maxhour">最长预约时间：</label><input type="text" name="maxhour" id="maxhour" class="field" style="width: 40px" value="<?php echo ($room['maxhour']); ?>"/></td></tr><tr><td colspan="2" style="text-align: center"><input type="submit" value="<?php echo $roomid==0?'添加':'保存';?>房间" style="padding: 10px"/><?php if($roomid > 0) { ?><input type="button" value="删除房间" style="padding: 10px; color: #F00" onclick="remove()"/><?php } ?></td></tr></table></form>