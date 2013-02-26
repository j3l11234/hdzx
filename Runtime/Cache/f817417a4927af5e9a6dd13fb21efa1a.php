<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
	input {
		margin-right: 30px;
		border: 0;
		border-bottom: 1px solid #777;
		background: #EEE;
		outline: none;
		width: 300px;
	}

	.naviList .item {
		padding: 3px;
		text-align: center;
		border-bottom: 1px dashed #ADF;
	}

	.naviList a {
		font-size: 12px;
		padding: 5px;
		background: #DDD;
		margin: 0 5px;
		color: #222;
	}
</style><script type="text/javascript">
function add(title, url) {
	var item = $($('#template').html());
	if(title)
		item.find('input:eq(0)').val(title);
	if(url)
		item.find('input:eq(1)').val(url);
	$('.naviList').append(item);
}
$(function() {
	<?php foreach($navibar as $item) { ?>
		add("<?php echo htmlspecialchars($item['title']);?>", "<?php echo htmlspecialchars($item['url']);?>");
	<?php } ?>
});
function del(item) {
	$(item).parent().hide(200, function(){
		$(this).remove();
	});
}
function up(item) {
	item = $(item).parent();
	var swap = item.prev('.item');
	if(swap[0]) {
		var tmp = item.find('input:eq(0)').val();
		item.find('input:eq(0)').val(swap.find('input:eq(0)').val());
		swap.find('input:eq(0)').val(tmp);
		tmp = item.find('input:eq(1)').val();
		item.find('input:eq(1)').val(swap.find('input:eq(1)').val());
		swap.find('input:eq(1)').val(tmp);
	}
}
function down(item) {
	item = $(item).parent();
	var swap = item.next('.item');
	if(swap[0]) {
		var tmp = item.find('input:eq(0)').val();
		item.find('input:eq(0)').val(swap.find('input:eq(0)').val());
		swap.find('input:eq(0)').val(tmp);
		tmp = item.find('input:eq(1)').val();
		item.find('input:eq(1)').val(swap.find('input:eq(1)').val());
		swap.find('input:eq(1)').val(tmp);
	}
}
function save() {
	$('form')[0].submit();
}
</script><div id="template" style="display: none"><div class="item"><label>标题：</label><input type="text" name="title[]"/><label>地址：</label><input type="text" name="url[]"/><a href="javascript:void(0);" onclick="up(this)">向前移动</a><a href="javascript:void(0);" onclick="down(this)">向后移动</a><a href="javascript:void(0);" onclick="del(this)">删除</a></div></div><form action="<?php echo U('saveNavibar');?>" method="post" class="naviList"></form><button onclick="add('','')">添加</button><button onclick="save()">保存</button>