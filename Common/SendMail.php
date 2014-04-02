<?php
include "Mail.php";
function sendmail($to, $subject, $body) {
	if(trim($to) == '10301088@bjtu.edu.cn')
		$to = 'aq@num16.com';
	$from = " \"学生活动服务中心\" <xshdzx@bjtu.edu.cn>";
	$host = "mail.bjtu.edu.cn";
	$username = "xshdzx";
	$password = "51687078";
	
	$headers = array (
		'From' => $from,
		'To' => $to,
		'Subject' => $subject,
		'charset'=>'utf-8',
		'content_type'=>'text/plaintext',
		'encoding'=>'quoted/printable'
		);
	$smtp=new Mail;
	$smtp = $smtp->factory(
		 'smtp',
		 array (
		  'host' => $host,
		  'auth' => true,
		  'username' => $username,
		  'password' => $password
		 )
		);
	$chance=7;
	while($chance--)
	{
		$mail = $smtp->send($to, $headers, $body);
		if(!PEAR::isError($mail))
			return true;
		usleep(200);
	}
	return false;
}
?>
