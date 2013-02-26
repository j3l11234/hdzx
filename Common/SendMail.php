<?php
return;
include "Mail.php";
function sendmail($to, $subject, $body) {
	$from = " \"学生活动服务中心\" <xshdzx@bjtu.edu.cn>";
	$host = "mail.bjtu.edu.cn";
	$username = "xshdzx";
	$password = "51687078";
	
	$headers = array (
		 'From' => $from,
		 'To' => $to,
		 'Subject' => $subject
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
	$email = new Mail_mimePart('', array('content_type'=>'multipart/alternative'));
	$email->addSubPart($body, array(
		'charset'=>'utf-8',
		'content_type'=>'text/html',
		'encoding'=>'quoted/printable'
	));
	$email = $email->encode();
	$headers = array_merge($headers, $email['headers']);
	$body = $email['body'];
	$chance=5;
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
