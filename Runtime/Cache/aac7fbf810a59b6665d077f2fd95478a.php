<?php if (!defined('THINK_PATH')) exit();?><!doctype html><html><head><title><?php echo $title;?></title><link rel="stylesheet" type="text/css" href="__PUBLIC__/admin.css"/><script type="text/javascript" src="__PUBLIC__/jquery.js"></script><script type="text/javascript">
		$(function() {
			$('.navigate li').hover(function() {
				$(this).find('ul:first').stop(true, true).slideDown(200);
			}, function() {
				$(this).find('ul').stop(true, true).slideUp(200);
			});
		});
		</script></head><body><div class="navigate"><div class="fix-width"><label>
					北京交通大学 - 学生活动服务中心 - 后台管理系统
				</label><?php
 function recursiveDir(&$dir) { $ret = '<ul>'; foreach($dir as $k => $v) { if(is_array($v)) { $url = 'javascript:void(0);'; $sub = recursiveDir($v); } else { $url = U($v); $sub = ''; } $ret .= '<li><a href="' . $url . '">' . $k . '</a>' . $sub . '</li>'; } $ret .= '</ul>'; return $ret; } echo recursiveDir($dir); ?></div></div><div class="fix-width" style="margin-top: 35px">