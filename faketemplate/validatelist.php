<?php include 'header.php'; ?>
<style type="text/css">
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
	cursor: pointer;
	color: #222;
}
.row:hover {
	margin: 0;
	border-bottom: 1px solid #7D7;
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
	background: #F7F7F7 url(images/shadow.png) repeat-x;
	border-bottom: 1px solid #DDD;
	margin: 0;
}
.detail:hover {
	margin: 0;
}
</style>
<script type="text/javascript">
$(function() {
	$('.row').each(function() {
		$(this).find('.cell:odd').css('background', '#FFF');
		$(this).find('.cell:even').css('background', '#AFA');
	});
	$('.row').click(function() {
		if(!$(this).hasClass('detail'))
			$(this).next('.detail').stop(true, true).slideToggle(300);
	});
});
</script>
<div class="float-fix">
	<div class="right-bar" style="float:right; width: 30%">
		<h1>预约审批</h1>
		<div class="nav-bar">
			<div class="nav-bar2">
				<div class="nav-bar3 listed">
					<a href="" class="item">待审核预约</a>
					<a href="" class="active">过往审批记录</a>
					<a href="" class="item">自动审批记录</a>
					<a href="" class="item">账号密码修改</a>
				</div>
			</div>
		</div>
		<div style="padding: 10px; padding-right: 30px; text-align: center">
			<i>您拥有权力审批来自xx学院的预约单，您审批通过的预约将提交给上一级审核者</i>
			<br/>
			<i>您拥有权力审批从各学院提交而来或直接向校级提交的预约单，您审批通结果将是最终结果</i>
			<br/><br/>
			<button class="btn">退出审批</button>
		</div>
	</div>

	<div style="float: left; width: 69%">
		<div class="left-content">
			<div class="row" style="font-weight: bold">
				<div class="cell" style="width: 15%">
					学院
				</div>
				<div class="cell" style="width: 15%">
					申请人
				</div>
				<div class="cell" style="width: 21%">
					申请房间
				</div>
				<div class="cell" style="width: 49%">
					申请主题
				</div>
			</div>
			<?php for($i=0;$i<10;$i++) { ?>
			<div class="row">
				<div class="cell" style="width: 15%">
					软件工程
				</div>
				<div class="cell" style="width: 15%">
					张三李四
				</div>
				<div class="cell" style="width: 21%">
					团队讨论室 (414)
				</div>
				<div class="cell" style="width: 49%">
					软件项目开发开题会议
				</div>
			</div>
			<div class="row detail">
			asdasd
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php include 'footer.php'; ?>
