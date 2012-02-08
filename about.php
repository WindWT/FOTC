<?php
function FOTC_about($aboutpass)
{
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" />
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<meta name="author" content="WindWT" />
<meta name="Copyright" content="Copyright WindWT@C0de::W1ndWT All Rights Reserved." />  
<title>FOTC</title>
</head>
<body>
<p>如果你设置正确的话，FOTC已经开始工作了。</p>
    <?php
    if(file_exists('config.sample.php')||file_exists('cron.sample.php')||file_exists('index.sample.php'))
        echo '<br /><p>如果你不会设置的话……唔，自己想办法！<br />我才不会管呐！</p><br />';
    echo '<p>Author：WindWT@C0de::W1ndWT</p>';
    
    if(!$aboutpass)
    {
        ?>
        <form action="index.php" method="post">
        管理密码：<input type="text" name="aboutpass" maxlength="4" /><br />
        <input type="submit" value="进入管理界面" />
        </form>
        <?php
    }
    else if($aboutpass!=ABOUT_PASSWORD)
    {
        ?>
        <p>密码错误，请重新输入。</p>
        <form action="index.php" method="post">
        管理密码：<input type="text" name="aboutpass" maxlength="4" /><br />
        <input type="submit" value="进入管理界面" />
        </form>
        <?php
    }
    else
    {
        //暂停部分
        if (!file_exists("PAUSE"))
        {
            ?>
            <form action="aboutcon.php?pause=1" method="post">
            <input type="submit" value="暂停FOTC" />
            </form>
            <?php
        }
        else
        {
            ?>
            <form action="aboutcon.php?unpause=1" method="post">
            <input type="submit" value="启动FOTC" />
            </form>
            <?php        
        }
        //清除日志部分
        $logsize=0;
        if ($handle = opendir('log'))
        {
            while (false !== ($file = readdir($handle)))
            {   
                if ($file != "." && $file != "..")
                {
                    $logsize+=filesize(getcwd().'/log/'.$file);
                }
            }
            closedir($handle);
            if ($logsize<1000)
            {
                echo "<p>日志占用空间：$logsize B</p>";
            }
            else if ($logsize<1000000)
            {
                $logsize=round($logsize/1024,2);
                echo "<p>日志占用空间：$logsize KB</p>";
            }
            else if ($logsize<1000000000)
            {
                $logsize=round($logsize/1024/1024,2);
                echo "<p>日志占用空间：$logsize MB</p>";
            }
            else
            {
                $logsize=round($logsize/1024/1024/1024,2);
                echo "<p>日志占用空间：$logsize GB</p>";
            }
            ?>
            <form action="aboutcon.php?clearlog=1" method="post">
            <input type="submit" value="清除所有日志" />
            </form>
            <?php
        }
        else
        {
            echo "<p>日志目录暂时不存在，请等待其自行生成。</p>";
        }
        //清除cookie部分
        $cookiecount=0;
        if ($handle = opendir('cookie'))
        {
            while (false !== ($file = readdir($handle)))
            {   
                if ($file != "." && $file != "..")
                {
                    $cookiecount++;
                }
            }
            closedir($handle);
            echo "<p>存在cookie数量：$cookiecount</p>";
            ?>
            <form action="aboutcon.php?clearcookie=1" method="post">
            <input type="submit" value="清除cookie" />
            </form>
            <?php
        }
        else
        {
            echo "<p>cookie目录暂时不存在，请等待其自行生成。</p>";
        }
    }
echo '</body>';
}
?>