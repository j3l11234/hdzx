<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="__PUBLIC__/jqueryui.css"/><script type="text/javascript" src="__PUBLIC__/jqueryui.js"></script><style type="text/css">
.left-content {
	margin-left: 25px;
	margin-bottom: 20px;
}
.right-bar h1 {
	color: #000;
	font-size: 25px;
}
.row {
	overflow: hidden;
	zoom: 1;
	margin-bottom: 1px;
	color: #222;
}
.item .row:hover {
	margin: 0;
	border-bottom: 1px solid #7D7;
}
.item .row {
	cursor: pointer;
}
.cell {
	float: left;
	height: 30px;
	line-height: 30px;
	text-indent: 5px;
}
.detail {
	display: none;
	padding: 5px;
	background: #F7F7F7 url(__PUBLIC__/images/shadow.png) repeat-x;
	border-bottom: 1px solid #DDD;
	margin: 0;
	line-height: 40px;
}
.detail:hover {
	margin: 0;
}
pre {
	margin: 5px;
	padding: 5px;
	font-family: arial;
	font-size: 13px;
	line-height: 20px;
	background: #AFB;
	border: 1px solid #7B9;
}
#comment {
	width: 300px;
	height: 120px;
	border: 1px solid #777;
	background: #FFF;
	outline: none;
	padding: 5px;
}
.approved {
	background: url(__PUBLIC__/images/approved.png) no-repeat right center;
}
.reject {
	background: url(__PUBLIC__/images/reject.png) no-repeat right center;
}
</style><script type="text/javascript">
$(function() {
	$('#date').datepicker();
	$('.row').each(function() {
		$(this).find('.cell:odd').css('background', '#FFF');
		$(this).find('.cell:even').css('background', '#AFA');
	});
	$('.item .row').click(function() {
		if(!$(this).hasClass('detail'))
			$(this).next('.detail').stop(true, true).slideToggle(300);
	});
	// make order filter
	var filterMask = 1;
	function makeFilter(title, keyname) {
		var mask = filterMask;
		filterMask *= 2;
		var map = {};
		$('.orders .item').each(function() {
			var key = $(this).find('.' + keyname).html();
			if(!map[key]) {
				map[key] = [];
			}
			map[key].push(this);
		});
		var names = [];
		for(name in map) {
			names.push(name);
		}
		if(names.length > 1) {
			$('#' + title).css({cursor:'pointer',background:'url(__PUBLIC__/images/downdrop.gif) no-repeat center right'});
			var index = 0;
			var deft = $('#' + title).html();
			$('#' + title).click(function() {
				if(index == names.length) {
					$(this).html(deft);
					$('.orders .item').each(function() {
						var $i = $(this).find('input');
							$i.val(~(~$i.val() | mask));
					});
					index = 0;
				} else {
					var key = names[index];
					$(this).html(key);
					var arr = map[key];
					$('.orders .item').each(function() {
						var $i = $(this).find('input');
						$i.val($i.val() | mask);
					});
					for(k in arr) {
						var $c = $(arr[k]);
						var $i = $c.find('input');
						$i.val(~(~$i.val() | mask));
					}
					index++;
				}
				$('.orders .item').each(function() {
					if($(this).find('input').val() == 0)
						$(this).show();
					else
						$(this).hide();
				});
			});
		}
	};
	makeFilter('roomtitle', 'roomname');
	makeFilter('schooltitle', 'schoolname');
});
function search() {
	var url = '<?php echo U('history', array('roomtype'=>'$R$','school'=>'$S$','date'=>'$D$','auto'=>$param['auto']));?>';
	url = url.replace('$R$', $('#roomtype').val());
	url = url.replace('$S$', $('#school').val());
	var t = $('#date').val();
	if(!t)
		t = 0;
	url = url.replace('$D$', t);
	document.location.href = url;
}
</script><div class="float-fix"><div class="right-bar" style="float:right; width: 30%"><h1>预约审批</h1><div class="nav-bar"><div class="nav-bar2"><div class="nav-bar3 listed"><a href="<?php echo U('pending');?>" class="item">待审核预约</a><a href="<?php echo U('history');?>" class="<?php echo $param['auto']?'item':'active';?>">过往审批记录</a><a href="<?php echo U('auto');?>" class="<?php echo $param['auto']?'active':'item';?>">自动审批记录</a><a href="<?php echo U('User/changepwd');?>" class="item">账号密码修改</a></div></div></div><div style="padding: 10px; padding-right: 30px; text-align: center"><a href="<?php echo U('User/logout');?>"><button class="btn">退出审批</button></a></div></div><div style="float: left; width: 69%"><form style="text-align: center"><label>房间类型：</label><select id="roomtype"><option value="0">-- 任意 --</option><?php echo options($roomtype, $param['roomtype']);?></select>
			&nbsp;&nbsp;
			<label>学院：</label><select id="school"><option value="-1">-- 任意 --</option><?php echo options($school, $param['school']);?></select>
			&nbsp;&nbsp;
			<label>截至日期：</label><input type="text" id="date" readonly class="field" size="11" value="<?php echo $param['date']?$param['date']:'';?>" onclick="$(this).val('')"/>
			&nbsp;&nbsp;
			<input onclick="search()" type="button" value="检索" /></form><div class="ribbon" style="width: 580px"><div class="padding"></div></div><div class="pager"><?php $tmp = $param;$tmp['page']='$$'; show_pager($pageTotal, $param['page'], U('history', $tmp));?></div><div class="left-content"><?php if($orders) { ?><div class="row" style="font-weight: bold"><div class="cell" style="width: 15%"><div id="schooltitle">
							学院
						</div></div><div class="cell" style="width: 15%">
						申请人
					</div><div class="cell" style="width: 25%"><div id="roomtitle">
							申请房间
						</div></div><div class="cell" style="width: 45%; overflow: hidden;">
						申请主题
					</div></div><div class="orders"><?php foreach($orders as $order) { ?><div class="item"><input type="hidden" value="0"/><div class="row" id="order<?php echo ($order["orderid"]); ?>"><div class="cell schoolname" style="width: 15%"><?php echo ($school[$order['school']]); ?></div><div class="cell" style="width: 15%"><?php echo ($order["info"]["orderer"]); ?></div><div class="cell roomname" style="width: 25%"><?php echo ($order["info"]["roomname"]); ?></div><div class="cell" style="width: 45%" id="hint<?php echo ($order["orderid"]); ?>"><div class="<?php echo ($verify[$order['orderid']]['ispass'])?'approved':'reject';?>"><?php echo ($order["info"]["topic"]); ?></div></div></div><div class="detail"><label>预约人学号：</label><b><?php echo ($order["orderer"]); ?></b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<label>联系方式：</label><b><?php echo ($order["info"]["contact"]); ?></b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<label>申请单提交时间：</label><b><?php echo ($order["time"]); ?></b><br/><label>预约时间：</label><b><?php echo (num2date($order["date"])); ?> &nbsp;
								<?php echo ($order["starthour"]); ?>点 - <?php echo $order['endhour'] + 1;?>点
							</b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<label>活动人数：</label><b><?php echo ($order["info"]["people"]); ?></b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<label>社团/单位：</label><b><?php echo ($order["info"]["unit"]); ?></b><br/><label>活动内容：</label><pre><?php echo ($order["info"]["content"]); ?></pre><label>审批结果：</label><b><?php echo ($verify[$order['orderid']]['ispass'])?'批准':'驳回';?></b><pre><?php echo ($verify[$order['orderid']]['comment']); ?></pre></div></div><?php } ?></div><?php } ?></div></div></div>