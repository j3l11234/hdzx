<?php include 'header.php'; ?>
<style type="text/css">
.left-content {
	margin-left: 50px;
	margin-bottom: 20px;
}
.right-bar h1 {
	color: #000;
	font-size: 25px;
}
</style>
<div class="float-fix" style="margin-top: 30px;">
	<div style="float:left; width:62%;">
		<div class="left-content">
			<h1 style="color: #DA1045; margin: 0">
				 文章标题
			</h1>
			<div class="hr" style="width: 100%"></div>
		</div>
		<div class="ribbon">
			<div class="padding">
				子栏目1标题
			</div>
		</div>
		<div class="left-content">
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel leo vitae mi iaculis tincidunt. Sed ipsum diam, semper et adipiscing sit amet, gravida ac ipsum. Phasellus rutrum est non eros ultrices a molestie tellus suscipit.
		</div>
		<div class="ribbon">
			<div class="padding">
				子栏目2标题
			</div>
		</div>
		<div class="left-content">
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel leo vitae mi iaculis tincidunt. Sed ipsum diam, semper et adipiscing sit amet, gravida ac ipsum. Phasellus rutrum est non eros ultrices a molestie tellus suscipit.
		</div>
	</div>
	<div class="right-bar" style="float:right; width: 30%">
		<div class="nav-bar">
			<div class="nav-bar2">
				<div class="nav-bar3 listed">
					<a class="active">内容目录</a>
					<a href="" class="item">子栏目1</a>
					<a href="" class="item">子栏目2</a>
					<a href="" class="item">子栏目3</a>
				</div>
			</div>
		</div>
		<div class="hr" style="width: 85%; margin: 0 10px;"></div>
		<form style="padding-left: 10px;">
			<h1>反馈留言</h1>
			<label style="color: #222">邮箱：</label>
			<input style="width: 200px;" class="field" type="text" />
			<textarea style="width: 250px; height: 70px" class="field"></textarea>
			<button onclick="submit()" class="btn">发送</button>
		</form>
	</div>
</div>
<?php include 'footer.php'; ?>
