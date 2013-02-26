<?php if (!defined('THINK_PATH')) exit();?><br/><fieldset><legend>总量统计</legend><label>预约总量：</label><b><?php echo ($orderTotalCount); ?></b>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>通过预约：</label><b><?php echo ($orderPassTotalCount); ?></b>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>用户反馈量：</label><b><?php echo ($feedbackCount); ?></b>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>房屋数量：</label><b><?php echo ($roomCount); ?></b></fieldset><br/><fieldset><legend>本月统计</legend><label>预约总量：</label><b><?php echo ($orderMonthCount); ?></b>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>通过预约：</label><b><?php echo ($orderPassMonthCount); ?></b>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>用户反馈量：</label><b><?php echo ($feedbackMonthCount); ?></b></fieldset>