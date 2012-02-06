<?php
function s1_func($logininfo)
{
	$client = new HttpClient('bbs.saraba1st.com'); 
	//$client->setDebug(1);
	$cookiefilename="cookie/S1_".$logininfo[2]."_".$logininfo[1];
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
		$client->post('/2b/login.php', array(        
			'forward' => '',
			'jumpurl' => 'http://bbs.saraba1st.com/2b/index.php',
			'step' => '2',
			'lgt' => $lgt,
			'pwuser' => $logininfo[2],
			'pwpwd' => $logininfo[3],
			'hideid' => 0,
			'cktime' => 2592000,
			'submit' => '',
		));
		$cookieS1temp =$client->headers['set-cookie'];
		$file=fopen($cookiefilename,w);
		foreach($cookieS1temp as $temp)
		{
			$cookieTemp=explode(';',$temp);
			$cookieTemp[1]=explode('=',$cookieTemp[0]);
			$cookieS1[$cookieTemp[1][0]]=$cookieTemp[1][1];
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
		//print_r($cookie4file);
		foreach($cookie4file as $cookie4filesplit)
		{
			$cookieTemp=explode('=',$cookie4filesplit);
			$cookieS1[$cookieTemp[0]]=$cookieTemp[1];
		}
		fclose($file);
	}
	log_runtime(0,LOG_MICROTIME_ENABLE);
	$client->setCookies($cookieS1);
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
	$geturl='/2b/index.php';
	$client->get($geturl);
	$errCode=$client->getError();
	//echo "<p>Done.</p>";
	//echo $client->getError();
	log_make($logininfo,"View",$errCode,LOG_ENABLE,LOG_MICROTIME_ENABLE);
	$pageContents = $client->getContent();
	//echo $pageContents;
}
?>