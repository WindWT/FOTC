<?php
function FOTC_about()
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
?>
<p>Author：WindWT@C0de::W1ndWT</p>
	<?php
    if (!file_exists("PAUSE"))
    {
        ?>
        <form action="pause.php?pause=1" method="post">
        <input type="text" name="pausepass" maxlength="4" /><br />
        <input type="submit" value="点我暂停FOTC" />
        </form>
        <?php
    }
    else
    {
        ?>
        <form action="pause.php?unpause=1" method="post">
        <input type="submit" value="点我继续FOTC" />
        </form>
        <?php        
    }
    ?>
 </body>
    <?php
}
?>