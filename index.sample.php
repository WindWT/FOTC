<?php
define('LOGINHASH',TRUE);
//设置登录HASH防止盗号，请将LOGINHASHHERE修改成任意长字符串并将config.php中的相应部分修改成相同内容
require("config.php");
require('HttpClient.php');
require('simple_html_dom.php');
require('log.php');
require('func/func_include.php');
if(!$_REQUEST["work"])
{
    if($_REQUEST['aboutpass'])
        $aboutpass=$_REQUEST['aboutpass'];
	require_once("about.php");
	FOTC_about($aboutpass);  //关于信息
	exit();
}
log_runtime_overall(0,LOG_MICROTIME_ENABLE);    //计时开始
if (!is_dir("cookie"))
mkdir("cookie");
$log_runtime_overall_mode=1;
if (!file_exists("PAUSE"))
    foreach($loginsettings as $logininfo)
    //$logininfo=$loginsettings[rand(0,7)];
    {
    	$logininfo[2]=mb_convert_encoding($logininfo[2],"GBK","UTF-8");
    	switch(strtolower($logininfo[0]))
    	{
    		case 'lk' :
    		case 'lightnovel.cn' :
    		case 'light-kingdom' :
    		{
    			$lk=new lk_func($logininfo);
                unset($lk);
    			break;
    		}
            case 'lkextra':
            {
                lkextra_func($logininfo);
            }
    		case 's1':
    		case 'bbs.saraba1st.com/2b/':
    		case 'saraba1st':
    		{
    			s1_func($logininfo);
    			break;
    		}
    		case 'kf':
    		case 'bbs.9gal.com':
    		{
    			kf_func($logininfo);
    			break;
    		}
    		case 'wua':
    		case 'wua.uueasy.com':
    		{
    			wua_func($logininfo);
    			break;
    		}
    	}
    	$logininfo=NULL;
    }
else
    $log_runtime_overall_mode=-1;
$loginsettings=NULL;
log_runtime_overall($log_runtime_overall_mode,LOG_MICROTIME_ENABLE);    //结束计时
?>