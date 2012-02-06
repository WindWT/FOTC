<?php
define('LOG_ENABLE',0);
//是否启用日志
define('LOG_MICROTIME_ENABLE',0);
//是否启用含毫秒计时的日志（要求启用日志
define('PAUSE_PASSWORD','PASS');
//暂停时所需的密码，限制为4位，请修改默认密码
if (defined('LOGINHASH'))
//设置登录HASH防止盗号，请随机输入一串东西并修改index.php中的相应部分
{
	$loginsettings[0]=array('','','','','','');
}
//设置登录ID
//范例0：$loginsettings[1]=array('登录论坛','登录方式','帐号','密码','可能会有的','其他要求的输入');
//范例1：$loginsettings[1]=array('lk','username','sample1','samplepass1','0','');
//使用用户名登录，没有安全提问。
//范例2：$loginsettings[2]=array('lk','uid','sample2','samplepass2','0','');
//使用UID登录，没有安全提问。
//范例3：$loginsettings[3]=array('lk','email','sample3','samplepass3','4','Teacher's Name');
//使用邮箱登陆，安全问题为“您其中一位老师的名字”，答案为“Teacher's Name”
//范例4：$loginsettings[4]=array('s1','username','WTF','WTF');
//使用用户名登陆S1
//请写func_XX.php的各位注意数组前四项是固定的，如果论坛登录还要加什么的话请在后面加
?>