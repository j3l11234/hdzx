<?php include 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="roomlist.css"/>
<link rel="stylesheet" type="text/css" href="jqueryui.css"/>
<script type="text/javascript" src="jqueryui.js"></script>
<script type="text/javascript">
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
		$('#timestart').html(ui.values[0]);
		$('#timeend').html(ui.values[1] + 1);
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
});
</script>
<div class="float-fix">
	<div class="nav-bar" style="float: right; width: 30%; margin-right: 10px;">
		<div class="nav-bar2">
			<div class="nav-bar3">
				<a class="active">
					房间名称或房间号检索
				</a>
				<div class="tool-box">
					<form>
						<label>房间名/号:</label>
						<input type="text" class="field" style="width:150px"/>
					</form>
				</div>
				<a class="active">
					房间可预约时间检索
				</a>
				<div class="tool-box">
					<form>
						<label>日期:</label>
						<input type="text" class="field datepicker" readonly style="width: 150px;"/>
						<a href="javascript:void(0)" onclick="$('.datepicker').val('')">清除</a>
						<br/>
						<label>时段：</label>
						<span id="timestart">8</span>点 到
						<span id="timeend">22</span>点
						<div class="slider" style="margin: 10px; z-index: 0"></div>
					</form>
				</div>
				<a class="active">
					房间类别检索
				</a>
				<div class="tool-box" style="padding: 10px">
					<label>类型:</label>
					<select>
						<option value="0">任意</option>
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<label>楼层:</label>
					<select>
						<option value="0">任意</option>
					</select>
				</div>
				<div class="hr"></div>
				<div class="tool-box" style="text-align: center">
					<button class="btn">检索符合以上全部条件</button><br/>
					<i>表格中未填写内容不参与检索</i>
				</div>
			</div>
		</div>
	</div>
	<div style="float: left; width: 650px; margin-left: 25px; position: relative">
		<div class="timelist float-fix">
			<table cellspacing="0" cellpadding="0"><tr>
				<td style="width: 150px; text-align: center">
					<i style="font-size: 10px; color: #FFF">拖动此轴以查看更多时间</i>
				</td>
				<td style="width: 500px;">
					<div class="timelist2">
						<ul class="timelined">
							<li>2013-1-15 今天</li>
							<li>2013-1-16 周二</li>
							<li>2013-1-17 周三</li>
							<li>2013-1-18 周四</li>
							<li>2013-1-19 周五</li>
							<li>2013-1-20 周六</li>
							<li>2013-1-21 周日</li>
						</ul>
					</div>
				</td>
			</tr></table>
		</div>
		<div id="timedrag" class="timelist float-fix">&nbsp;</div>
		<div style="height: 35px"></div>
		<?php for($i=0;$i<10;$i++){?>
		<div class="float-fix roomitem">
			<table style="650px" cellspacing="0" cellpadding="0"><tr>
				<td style="color: #222; width: 150px; text-align: center">
					<strong>房间名称</strong><br/>
					<i>414</i>
					<br/><br/>
					<a href="">房间详情</a>
				</td>
				<td style="width: 500px;">
					<div style="width: 500px; overflow: hidden">
						<table class="timelined" style="width: 1500px" cellspacing="0" cellpadding="0"><tr>
						<?php for($m=0;$m<15;$m++){ ?>
							<td>
								<a class="timecell" href="javascript:void(0)" title="点击这里在此日期申请此房间">
									<div class="item">8</div>
									<div class="item">9</div>
									<div class="item">10</div>
									<div class="item">11</div>
									<div class="item ordered">12</div>
									<div class="item ordered">13</div>
									<div class="item">14</div>
									<div class="item placeholder"></div>
									<div class="item">15</div>
									<div class="item">16</div>
									<div class="item">17</div>
									<div class="item">18</div>
									<div class="item restrict">19</div>
									<div class="item">20</div>
									<div class="item">21</div>
								</a>
							</td>
						<?php } ?>
						</tr></table>
					</div>
				</td>
			</tr></table>
		</div>
		<?php } ?>
	</div>
</div>
<?php include 'footer.php'; ?>
