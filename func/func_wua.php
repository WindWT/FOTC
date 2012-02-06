<?php
function wua_func($logininfo)
{
	$client = new HttpClient('wua.uueasy.com'); 
	//$client->setDebug(1);
	$cookiefilename="cookie/WUA_".$logininfo[2]."_".$logininfo[1];
	$cookiefilename=mb_convert_encoding($cookiefilename, "GBK", "UTF-8");
	switch(strtolower($logininfo[1]))
	{
		case 'username':
		{
			$lgt=0;
			break;
		}
		case 'uid':
		{
			$lgt=1;
			break;
		}
		case 'email':
		{
			$lgt=2;
			break;
		}
	}
	if ((!file_exists($cookiefilename))||($_REQUEST["refreshcookie"]))
	{
		log_runtime(0,LOG_MICROTIME_ENABLE);
		$client->post('/login.php', array(        
			'forward' => '',
			'jumpurl' => 'http://wua.uueasy.com',
			'step' => '2',
			'lgt' => $lgt,
			'pwuser' => $logininfo[2],
			'pwpwd' => $logininfo[3],
			'hideid' => 0,
			'cktime' => 2592000,
			'submit' => '登录',
		));
		$cookieWUAtemp =$client->headers['set-cookie'];
		$file=fopen($cookiefilename,w);
		foreach($cookieWUAtemp as $temp)
		{
			$cookieTemp=explode(';',$temp);
			$cookieTemp[1]=explode('=',$cookieTemp[0]);
			$cookieWUA[$cookieTemp[1][0]]=$cookieTemp[1][1];
			$cookie4file=$cookieTemp[1][0]."=".$cookieTemp[1][1].";";
			fwrite($file,$cookie4file);
		}
		fclose($file);
		$errCode=$client->getError();
		if($_REQUEST["refreshcookie"])
		$status="Refresh";
		else
		$status="Init";
		log_make($logininfo,$status,$errCode,LOG_ENABLE,LOG_MICROTIME_ENABLE);
	}
	else
	{
		$file=fopen($cookiefilename,r);
		$temp=fgets($file);
		$cookie4file=explode(';',$temp);
		foreach($cookie4file as $cookie4filesplit)
		{
			$cookieTemp=explode('=',$cookie4filesplit);
			$cookieWUA[$cookieTemp[0]]=$cookieTemp[1];
		}
		fclose($file);
	}
	log_runtime(0,LOG_MICROTIME_ENABLE);
	$client->setCookies($cookieWUA);
	/*$type=rand(0,2);
	$uid=rand(2,300000);
	$tid=rand(100000,300000);
	$fid=rand(10,150);
	switch ($type)
	{
		case 0:
		{
			$geturl='/space.php?uid='.$uid;
			break;
		}
		case 1:
		{
			$geturl='/viewthread.php?tid='.$tid;
			break;
		}
		case 2:
		{
			$geturl='/forumdisplay.php?fid='.$fid;
			break;
		}
	}*/
	$geturl='/index.php';
	$client->get($geturl);
	$errCode=$client->getError();
	//echo "<p>Done.</p>";
	//echo $client->getError();
	log_make($logininfo,"View",$errCode,LOG_ENABLE,LOG_MICROTIME_ENABLE);
	$pageContents = $client->getContent();
	//echo $pageContents;
}
?>