<?php include 'header.php'; ?>
<style type="text/css">
.form-content {
	margin-left: 20px;
	padding: 17px;
}
.timelist {
	margin: 0 auto;
	padding: 0;
	width: 100px;
}
.timelist li {
	float: left;
	width: 20px;
	height: 20px;
	display: block;
	margin: 1px;
	font-size: 10px;
	line-height: 20px;
	text-align: center;
	background: #AED;
	cursor: pointer;
	-moz-user-select: none;
	-webkit-user-select: none;
	-ms-user-select: none;
}
.timelist li.ordered {
	background: #DAC;
}
.timelist li.restrict {
	background: #397;
	color: #FFF;
}
.form-content textarea {
	display: block;
	margin: 5px auto;
	width: 550px;
	height: 100px;
}
.form-content label {
	color: #000;
	font-weight: bold;
}
</style>
<link rel="stylesheet" type="text/css" href="jqueryui.css"/>
<script type="text/javascript" src="jqueryui.js"></script>
<script type="text/javascript">
$(function() {
	$('.timelist .ordered').attr('title', '已被预约');
	$('.timelist .restrict').attr('title', '已被锁定');
	$('.datepicker').datepicker({minDate: "+1D", maxDate: "+2W"});
	var time1, time2, down, bad;
	$('.timelist li').mousedown(function() {
		down = true;
		time1 = $(this).html() * 1;
		time2 = time1;
		highLight();
		return false;
	});
	$(document).mouseup(function() {
		if(bad) {
			time1 = time2 = 0;
			highLight();
			$('#timeend').html('00');
		}
		down = false;
	});
	$('.timelist li').hover(function() {
		if(down) {
			time2 = $(this).html() * 1;
			highLight();
		}
	}, function () {
	});
	function highLight() {
		var a = time1 > time2 ? time2 : time1;
		var b = time1 + time2 - a;
		var lit = [];
		bad = false;
		$('.timelist li').css({background: '', color: ''});
		$('.timelist li').each(function() {
			var c = $(this).html() * 1;
			if(c >= a && c <= b) {
				lit.push(this);
				if($(this).hasClass('ordered') || $(this).hasClass('restrict')) {
					bad = true;
				}
			}
		});
		$(lit).css({background: bad ? '#C33':'#A4C050', color: '#222'});
		if(a < 10)
			a = '0' + a;
		b++;
		if(b < 10)
			b = '0' + b;
		$('#timestart').html(a);
		$('#timeend').html(b);
	}
});
</script>
<div class="float-fix">
	<div style="float: right; width: 35%" class="nav-bar">
		<div class="nav-bar2">
			<div class="nav-bar3">
				<a class="active">您正在预约...</a>
				<div style="text-align: center; padding: 10px; padding-right: 50px; color: #222">
					<img src="images/roomimg.jpg" style="width: 200px"/>
					<br/>
					<strong>xxx房间</strong> (502)
					<br/><br/>
					<input type="checkbox" id="confess"/>
					<label for="confess" style="font-size: 13px; color: #222;">我同意遵守学生活动服务中心管理条例</label>
					<br/>
					<button class="btn">提交申请表单</button><br/>
					<i>表单提交后还要经过邮箱认证才会真的提交</i>
				</div>
			</div>
		</div>
	</div>
	<div style="float: left; width: 63%">
		<a name="form-top" class="form-content" style="color: #222">
			请认真填写以下预约表格，申请人学号必须是北京交通大学在校学员的学号。
		</a>
		<div class="ribbon"><div class="padding">
			1. 预约人基本资料
		</div></div>
		<form class="form-content">
			<label>预约人姓名：</label> <input type="text" class="field"/>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<label>预约人学号：</label> <input type="text" class="field"/>
		</form>

		<div class="ribbon"><div class="padding">
			2. 预约时间选择
			<a href="#form-top">回到顶部</a>
		</div></div>
		<form class="form-content">
			<table><tr>
				<td rowspan="2" style="padding:0 30px">
					<div class="datepicker"></div>
				</td>
				<td style="text-align:center;padding: 20px;">
					<label>时段：</label> <span id="timestart">00</span>点 到 <span id="timeend">00</span>点
				</td>
			</tr>
				<td valign="top" style="text-align: center">
					<ul class="float-fix timelist">
						<li>8</li>
						<li>9</li>
						<li>10</li>
						<li>11</li>
						<li>12</li>
						<li class="ordered">13</li>
						<li style="margin-right:20px;">14</li>
						<li>15</li>
						<li class="restrict">16</li>
						<li>17</li>
						<li>18</li>
						<li>19</li>
						<li>20</li>
						<li>21</li>
					</ul>
					<br/>
					<i>用鼠标<u>划过</u>想要的时段，<br/>如在11按下在14松开代表11点到15点</i>
				</td>
			</tr></table>
		</form>

		<div class="ribbon"><div class="padding">
			3. 详细申请资料
			<a href="#form-top">回到顶部</a>
		</div></div>
		<form class="form-content">
			<label>活动主题：</label> <input type="text" class="field"/>
			&nbsp;&nbsp;
			<label>举办单位：</label>
			<select>
				<option>个人</option>
				<option>学院社团</option>
				<option>校级社团</option>
			</select>
			&nbsp;&nbsp;
			<label>活动人数：</label>
			<select>
				<option>&lt; 5</option>
				<option>5-10</option>
				<option>10-50</option>
				<option>50+</option>
			</select>
			<br/><br/>
			<label>活动内容：</label>
			<textarea class="field"></textarea>
			<br/><br/>
			<label>安保措施：</label>
			<textarea class="field"></textarea>
		</form>
	</div>
</div>
<?php include 'footer.php'; ?>
