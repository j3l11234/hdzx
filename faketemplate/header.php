<!doctype html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="common_style.css"/>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
		<script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript">
		$(function() {
			$('button.btn').each(function() {
				$(this).html('<span>' + $(this).html() + '</span>');
			});
		});
		</script>
	</head>
	<body>
		<div class="page-wrap">
			<div class="main-menu-wrap float-fix">
				<div class="main-menu-padding">
					<div class="head"> </div>
					<div class="body"> </div>
				</div>
				<div class="padding-left"></div>
				<ul class="main-menu">
					<li><a href="index.php">首页</a></li>
					<li><a href="roomlist.php">房间查询</a></li>
					<li><a href="article.php">中心介绍</a></li>
					<li><a href="">流程帮助</a></li>
					<li><a href="">规章制度</a></li>
					<li class="separator"></li>
					<li style="float: right"><input type="text" id="searchfield"/></li>
				</ul>
				<div class="padding-right"></div>
			</div>
			<div class="page-body">
				<div class="logo">
					<div class="logotext"></div>
				</div>
