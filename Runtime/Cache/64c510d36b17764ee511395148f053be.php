<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="__PUBLIC__/roomlist.css"/><link rel="stylesheet" type="text/css" href="__PUBLIC__/jqueryui.css"/><script type="text/javascript" src="__PUBLIC__/jqueryui.js"></script><script type="text/javascript">
var timeSlide;
$(function() {
	$('.roomitem:odd').css('background', '#FFF');
	var delay = false;
	var mt = $('.nav-bar').offset().top;
	function placeBar () {
		if(delay) {
			clearTimeout(delay);
			delay = false;
		}
		(function() {
			var st = $(document).scrollTop() - mt;
			if(st < 0) {
				st = 0;
			}
			var bt = st + $('.nav-bar').height() - $('.nav-bar').parent().height();
			if(bt > 0) {
				st -= bt;
			}
			$('.nav-bar').css({marginTop: st});
			$('.timelist').css({top: st});
		})();
	}
	placeBar();
	$(window).scroll(placeBar);
	$(document).resize(placeBar);
	$('.datepicker').datepicker({minDate: 0, maxDate: "+2W"});
	$('.slider').slider({range: true, min:8, max: 21, values:[8,21], slide:function(event, ui) {
		$('#timestart').val(ui.values[0]);
		$('#timeend').val(ui.values[1] + 1);
	}});
	var xx=false;
	var tpos = 0;
	$('#timedrag').mousedown(function(e) {
		xx=e.originalEvent.x||e.originalEvent.layerX||0;
		return false;
	});
	document.getElementById('timedrag').onselectstart = function() {return false;};
	var timelined = $('.timelined');
	$(document).mousemove(function(e) {
		if(xx==false)
			return;
		var nxx=e.originalEvent.x||e.originalEvent.layerX||0;
		tpos += nxx - xx;
		timelined.css('margin-left', tpos+'px');
		xx=nxx;
	});
	$(document).mouseup(function() {
		xx=yy=false;
		if(tpos > 0) {
			timelined.animate({marginLeft:0}, {duration: 300, step:function(now) {
				tpos = now;
			} });
		} else if(tpos < -1000) {
			timelined.animate({marginLeft:-1000}, {duration: 300, step:function(now) {
				tpos = now;
			} });
		}
	});
	timeChange();
});
function timeChange() {
	var s = $('#timestart').val() * 1;
	if(s < 8)
		s = 8;
	if(s > 22)
		s = 22;
	$('#timestart').val(s);
	$('.slider').slider('values', 0, s);
	var t = $('#timeend').val() * 1 - 1;
	if(t < s)
		t = s;
	if(t > 22)
		t = 22;
	$('#timeend').val(t + 1);
	$('.slider').slider('values', 1, t);
}
</script><div class="float-fix"><form method="post" action="<?php echo __ACTION__;?>" class="nav-bar" style="float: right; width: 30%; margin-right: 10px;"><div class="nav-bar2"><div class="nav-bar3"><a class="active">
					房间名称或房间号检索
				</a><div class="tool-box"><label>房间名/号:</label><input type="text" class="field" style="width:150px" name="name" value="<?php echo ($_POST["name"]); ?>"/></div><a class="active">
					房间可预约时间检索
				</a><div class="tool-box"><label>日期:</label><input type="text" name="date" class="field datepicker" readonly style="width: 150px;" value="<?php echo ($_POST["date"]); ?>"/><a href="javascript:void(0)" onclick="$('.datepicker').val('')">清除</a><br/><label>时段：</label><input type="text" class="plaintext" name="timestart" id="timestart" value="<?php echo ($tstart); ?>" onchange="timeChange()"/>
					点 到
					<input type="text" class="plaintext" name="timeend" id="timeend" value="<?php echo ($tend); ?>" onchange="timeChange()"/>
					点
					<div class="slider" style="margin: 10px; z-index: 0"></div></div><a class="active">
					房间类别检索
				</a><div class="tool-box" style="padding: 10px"><label>类型:</label><select name="type"><option value="0">任意</option><?php echo options($roomTypes, $type);?></select>
					&nbsp;&nbsp;&nbsp;
					<label>楼层:</label><select name="floor"><option value="0">任意</option><?php echo options($floors, $floor);?></select></div><div class="hr"></div><div class="tool-box" style="text-align: center"><button class="btn" onclick="submit()">按以上条件检索</button><br/><i>表格中未填写内容不参与检索</i></div></div></div></form><div style="float: left; width: 650px; margin-left: 25px; position: relative"><div class="timelist float-fix"><table cellspacing="0" cellpadding="0"><tr><td style="width: 150px; text-align: center"><i style="font-size: 10px; color: #FFF">拖动此轴以查看更多时间</i></td><td style="width: 500px;"><div class="timelist2"><ul class="timelined"><?php
 $rdates = array(); foreach($dates as $day) { $rdates[] = str_replace('-', '', substr($day, 0, 10)); ?><li><?php echo ($day); ?></li><?php } ?></ul></div></td></tr></table></div><div id="timedrag" class="timelist float-fix">&nbsp;</div><div style="height: 35px"></div><?php foreach($rooms as $room) { ?><div class="float-fix roomitem"><table style="650px" cellspacing="0" cellpadding="0"><tr><td style="color: #222; width: 150px; text-align: center"><strong><?php echo ($room["name"]); ?></strong><br/><i><?php echo ($room["number"]); ?></i><br/><br/><a href="<?php echo U('Index/room', array(id=>$room['roomid']));?>">房间详情</a></td><td style="width: 500px;"><div style="width: 500px; overflow: hidden"><table class="timelined" style="width: 1500px" cellspacing="0" cellpadding="0"><tr><?php for($m=0; $m<15; $m++){ ?><td><a class="timecell" href="<?php echo U('order', array('room' => $room['roomid'], 'date'=>$rdates[$m]));?>" target="_blank" title="点击这里在此日期申请此房间"><?php
 for($i = 8; $i <= 21; $i++) { if($i < 10) $i = '0' . $i; $key = $room['roomid'] . '_' . $rdates[$m] . $i; if(isset($status[$key])) { $stat = $status[$key] == _LOCKED ? 'restrict' : 'ordered'; } else $stat = ''; ?><div class="item <?php echo ($stat); ?>"><?php echo ($i); ?></div><?php if($i == 14) { ?><div class="item placeholder"></div><?php } } ?></a></td><?php } ?></tr></table></div></td></tr></table></div><?php } ?></div></div>