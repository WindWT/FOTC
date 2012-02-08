<?php

/**
 * @author WindWT
 * @copyright 2011
 */
require("config.php");
if(!$_REQUEST["pause"]&&!$_REQUEST['unpause']&&!$_REQUEST['clearlog']&&!$_REQUEST['clearcookie'])
{
    die('Access Denied.');
}
else if($_REQUEST["pause"])
{
    $pauseSetFile="PAUSE";
    if (!$file=fopen($pauseSetFile,"w"))
        die("暂停失败");
    else
    {
        fwrite($file,"如果你看到了这个文件，说明FOTC现在处于暂停状态。\n如果需要FOTC恢复工作，请删除该文件。");
        fclose($file);
        exit("FOTC已暂停。");
    }
}
else if($_REQUEST['unpause'])
{
    if (!unlink($pauseSetFile))
        die("取消暂停失败");
    else
        exit("FOTC已恢复正常工作。");
}
else if($_REQUEST['clearlog'])
{
    if ($handle = opendir('log'))
    {
        while (false !== ($file = readdir($handle)))
        {   
            if ($file != "." && $file != "..")
            {
                unlink('log/'.$file);
            }
        }
        closedir($handle);
        if(rmdir('log'))
            exit('删除日志成功。');
        else
            die('删除日志目录失败');
    }
    else
    {
        die('打开日志目录失败');
    }        
}
else if($_REQUEST['clearcookie'])
{
    if ($handle = opendir('cookie'))
    {
        while (false !== ($file = readdir($handle)))
        {   
            if ($file != "." && $file != "..")
            {
                unlink('cookie/'.$file);
            }
        }
        closedir($handle);
        if(rmdir('cookie'))
            exit('删除cookie成功。');
        else
            die('删除cookie目录失败');
    }
    else
    {
        die('打开cookie目录失败');
    }
}
else
{
    die('Access Denied.');
}
?>