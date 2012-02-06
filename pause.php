<?php

/**
 * @author WindWT
 * @copyright 2011
 */
require("config.php");
if(!$_REQUEST["pause"]&&!$_REQUEST['unpause'])
{
    die('Access Denied.');
}
$pauseSetFile="PAUSE";
if (!file_exists($pauseSetFile))
    if ($_REQUEST['pausepass']!=PAUSE_PASSWORD)
        die("密码错误");
    else if (!$file=fopen($pauseSetFile,"w"))
        die("暂停失败");
    else
    {
        fwrite($file,"如果你看到了这个文件，说明FOTC现在处于暂停状态。\n如果需要FOTC恢复工作，请删除该文件。");
        fclose($file);
        exit("FOTC已暂停。");
    }
else
    if (!unlink($pauseSetFile))
        die("取消暂停失败");
    else
        exit("FOTC已恢复正常工作。");
?>