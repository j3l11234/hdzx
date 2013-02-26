<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="__PUBLIC__/kindeditor-min.js"></script><link rel="stylesheet" type="text/css" href="__PUBLIC__/themes/default/default.css"/><style type="text/css">
#editForm input {
	background: #EEE;
	border: 0;
	border-bottom: 1px solid #AAA;
	outline: none;
	padding: 5px;
}
#editForm {
	padding: 5px;
}
.schools .item {
	margin: 5px;
	border-bottom: 1px dashed #ADF;
	overflow: hidden;
	zoom: 1;
}
.schools .item label {
	line-height: 30px;
	height: 30px;
	margin-left: 150px;
	font-size: 13px;
	display: block;
	width: 400px;
	float: left;
}
.schools .item button {
	line-height: 30px;
	height: 30px;
	background: transparent;
	border: none;
	color: #557;
	padding: 0;
	margin: 0;
	margin-left: 10px;
	font-size: 12px;
	cursor: pointer;
	float: left;
}
.schools .item button:hover {
	text-decoration: underline;
}
</style><script type="text/javascript">
function edit(id, item) {
	var name = item ? $(item).prev('label').html() : '';
	var dialog = KindEditor.dialog({
		title: (id==0 ? '添加' : '修改') + '学院',
		body: '<form id="editForm">' +
				'<label>学院名称：</label>' +
				'<input type="text" name="name" value="' + name + '"/>' +
				'<input type="hidden" name="schoolid" value="' + id + '"/>' +
				'</form>',
		closeBtn: {
			name: '关闭',
			click: function() {
				dialog.remove();
			}
		},
		noBtn: {
			name: '关闭',
			click: function() {
				dialog.remove();
			}
		},
		yesBtn: {
			name: '保存',
			click: function() {
				$.post('<?php echo U('editSchool');?>', $('#editForm').serialize(), function(data) {
					if(data.success) {
						if(item) {
							$(item).prev('label').html(data.name);
						} else {
							addItem(data.schoolid, data.name);
						}
						dialog.remove();
					}
				}, 'JSON');
			}
		}
	});
	$('#editForm input').focus();
}
function addItem(id, name) {
	var item = $('#template').html();
	item = item.replace(/{name}/g, name);
	item = item.replace(/{id}/g, id);
	$('.schools').append(item);
}
$(function() {
	$('.schools .item:odd').css('background', '#F7F7F7');
	<?php foreach($schools as $item) { ?>
	addItem(<?php echo ($item["schoolid"]); ?>, '<?php echo ($item["name"]); ?>');
	<?php } ?>
});
function up(id, item) {
	var target = $(item).parent().prev('.item');
	if(target[0])
		swap(id, $(item).parent(), target[0], target.find('span').html());
}
function down(id, item) {
	var target = $(item).parent().next('.item');
	if(target[0])
		swap(id, $(item).parent(), target[0], target.find('span').html());
}
function swap(id, item, target, targetId) {
	$.post('<?php echo U('swapSchool');?>', {'id':id,'target':targetId}, function(data) {
		if(data.success) {
			var tmp = $(target).html();
			$(target).html($(item).html());
			$(item).html(tmp);
		}
	},'JSON');
}
function remove(id, item) {
	$.get('<?php echo U('deleteSchool', array('id'=>''));?>' + id, function(data) {
		if(data.success) {
			$(item).parent().remove();
		}
	},'JSON');
}
</script><div id="template" style="display:none"><div class="item"><span style="display: none">{id}</span><label>{name}</label><button onclick="edit({id}, this)">修改名称</button><button onclick="up({id}, this)">提前</button><button onclick="down({id}, this)">后移</button><button onclick="remove({id}, this)" style="color: #F00; margin-left: 100px">删除</button></div></div><div class="schools"></div><div style="padding: 5px; text-align: center"><button onclick="edit(0, false)">添加学院</button></div>