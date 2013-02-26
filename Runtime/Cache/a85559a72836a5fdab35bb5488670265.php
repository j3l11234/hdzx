<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="__PUBLIC__/kindeditor-min.js"></script><link rel="stylesheet" type="text/css" href="__PUBLIC__/themes/default/default.css"><style type="text/css">
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
</style><script type="text/javascript">
$(function() {
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
var conflictTable = <?php echo (json_encode($conflict)); ?>;
function conflict(id) {
	var s = [];
	for(i in conflictTable[id]) {
		s.push('#order' + conflictTable[id][i]);
	}
	s = s.join(',');
	$('.row:gt(0)').css({fontWeight: 'normal','color':''});
	$(s).css({fontWeight: 'bold','color':'#F00'});
	$('#order' + id).css({fontWeight: 'bold','color':'#00F'});
}
function accept(id) {
	var url = '<?php echo U('accept', array('id'=>'$id$','reject'=>'$reject$'));?>';
	url = url.replace('$id$', id);
	if(conflictTable[id])
		url = url.replace('$reject$', conflictTable[id].join('|'));
	act('批准预约', url, $('#hint' + id).html());
}
function reject(id) {
	act('驳回预约', '<?php echo U('reject', array('id'=>''));?>' + id, $('#hint' + id).html());
}
function act(title, action, hint) {
	var dialog = KindEditor.dialog({
		title: title + ' - ' + hint,
		body: '<form action="' + action + '" method="post" style="padding:5px"><label>批注：</label><br/><textarea name="comment" id="comment"></textarea></form>',
		closeBtn: {
			name: '关闭',
			click: function() {dialog.remove();}
		},
		noBtn: {
			name: '取消',
			click: function() {dialog.remove();}
		},
		yesBtn: {
			name: title,
			click: function() {
				$('#comment').parent().submit();
			}
		}
	});
	setTimeout(function() {
		$('#comment')[0].focus();
	}, 100);
}
</script><div class="float-fix"><div class="right-bar" style="float:right; width: 30%"><h1>预约审批</h1><div class="nav-bar"><div class="nav-bar2"><div class="nav-bar3 listed"><a href="<?php echo U('pending');?>" class="active">待审核预约</a><a href="<?php echo U('history');?>" class="item">过往审批记录</a><a href="<?php echo U('auto');?>" class="item">自动审批记录</a><a href="<?php echo U('User/changepwd');?>" class="item">账号密码修改</a></div></div></div><div style="padding: 10px; padding-right: 30px; text-align: center"><?php if(PRV('verify')) { ?><i>您拥有权力审批<b><?php echo ($school[PRV('verify')]); ?></b>学院的预约单，您审批通过的预约将提交给更高级的审核者</i><?php } else { ?><i>您拥有权力审批从各学院提交而来或直接向校级提交的预约单，您审批的结果将是最终结果</i><?php } ?><br/><br/><a href="<?php echo U('User/logout');?>"><button class="btn">退出审批</button></a></div></div><div style="float: left; width: 69%"><div class="left-content"><?php if($orders) { ?><div class="row" style="font-weight: bold"><div class="cell" style="width: 15%"><div id="schooltitle">
							学院
						</div></div><div class="cell" style="width: 15%">
						申请人
					</div><div class="cell" style="width: 25%"><div id="roomtitle">
							申请房间
						</div></div><div class="cell" style="width: 45%; overflow: hidden;">
						申请主题
					</div></div><div class="orders"><?php foreach($orders as $order) { ?><div class="item"><input type="hidden" value="0"/><div class="row" id="order<?php echo ($order["orderid"]); ?>"><div class="cell schoolname" style="width: 15%"><?php echo ($school[$order['school']]); ?></div><div class="cell" style="width: 15%"><?php echo ($order["info"]["orderer"]); ?></div><div class="cell roomname" style="width: 25%"><?php echo ($order["info"]["roomname"]); ?></div><div class="cell" style="width: 45%" id="hint<?php echo ($order["orderid"]); ?>"><?php echo ($order["info"]["topic"]); ?></div></div><div class="detail"><label>预约人学号：</label><b><?php echo ($order["orderer"]); ?></b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<label>联系方式：</label><b><?php echo ($order["info"]["contact"]); ?></b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<label>申请单提交时间：</label><b><?php echo ($order["time"]); ?></b><br/><label>预约时间：</label><b><?php echo (num2date($order["date"])); ?> &nbsp;
								<?php echo ($order["starthour"]); ?>点 - <?php echo $order['endhour'] + 1;?>点
							</b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<label>活动人数：</label><b><?php echo ($order["info"]["people"]); ?></b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<label>社团/单位：</label><b><?php echo ($order["info"]["unit"]); ?></b><br/><label>活动内容：</label><pre><?php echo ($order["info"]["content"]); ?></pre><?php if(isset($order['info']['secure'])) { ?><label>安保措施：</label><pre><?php echo ($order["info"]["secure"]); ?></pre><?php } ?><button class="btn" onclick="accept(<?php echo ($order["orderid"]); ?>)" style="font-weight: bold">批准</button><button class="btn" onclick="reject(<?php echo ($order["orderid"]); ?>)">驳回</button><?php if(isset($conflict[$order['orderid']])) { ?><button class="btn" onclick="conflict(<?php echo ($order["orderid"]); ?>)" style="margin-left: 100px">高亮冲突预约单</button><br/><b style="color: #F00; margin-left: 12px">批准此预约将直接驳回所有冲突预约</b><?php } else { ?><i style="margin-left: 100px">没有冲突预约</i><?php } ?></div></div><?php } ?></div><?php } else { ?><h1 style="text-align:center; padding-top: 30px">您的工作已完成，没有待审核的预约</h1><?php } ?></div></div></div>