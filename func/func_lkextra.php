<?php
function lkextra_func($logininfo)
{
	$client = new HttpClient('www.lightnovel.cn'); 
	//$client->setDebug(1);
	$cookiefilename="cookie/LKextra_".$logininfo[2].".".$logininfo[1];
	if ((!file_exists($cookiefilename)))
	{
		lkextra_func_login($logininfo,$cookiefilename,0);
		lkextra_func($logininfo);
	}
	else
	{
        
		$cookieLKextra=lkextra_func_make_cookie($cookiefilename);
		log_runtime(0,LOG_MICROTIME_ENABLE);
		$client->setCookies($cookieLKextra);
		$type=rand(0,3);
		$uid=131468;
		$tid=156227;
		$fid=58;
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
			case 3:
			{
				$geturl='/index.php';
				break;
			}
		}
		//$geturl='/index.php';
		$client->get($geturl);
		$pageContents = $client->getContent();
        echo $pageContents;
		if (!$pageContents)
		{
			log_make($logininfo,"ViewError","ServerDown",LOG_ENABLE,LOG_MICROTIME_ENABLE);
			return(0);
		}
        if (strstr($pageContents,"font-family: Verdana, Tahoma; color: #666666; font-size: 11px"))
        {
            $cookieLKextratempextra=$client->headers['set-cookie'];
        	$file=fopen($cookiefilename.'extra',w);
        	foreach($cookieLKextratempextra as $tempextra)	//把$cookieLKextratemp[0]变成$cookieLKextra[lkww_sid]
        	{
        		$cookieTempextra=explode(';',$tempextra);
        		$cookieTempextra[1]=explode('=',$cookieTempextra[0]);
        		$cookie4fileextra=$cookieTempextra[1][0]."=".$cookieTempextra[1][1].";";
        		fwrite($file,$cookie4fileextra);
        	}
        	fclose($file);
            log_make($logininfo,"ViewError","ServerProtect",LOG_ENABLE,LOG_MICROTIME_ENABLE);
            sleep(10);
            lkextra_func($logininfo);
        }
		$html=str_get_html($pageContents);
		if (($html->find("div#umenu",0)->find('a',0)->href)=='register.php')
		{
			log_make($logininfo,"ViewError","CookieExpire",LOG_ENABLE,LOG_MICROTIME_ENABLE);
			lkextra_func_login($logininfo,$cookiefilename,1);
			lkextra_func($logininfo);
		}
		else
		{
			$errCode=$client->getError();
			log_make($logininfo,"View",$errCode,LOG_ENABLE,LOG_MICROTIME_ENABLE);
			return(0);
		}
		//echo $pageContents;
	}
	
}
function lkextra_func_login($logininfo,$cookiefilename,$refreshcookie)
{
	$client = new HttpClient('www.lightnovel.cn');
	log_runtime(0,LOG_MICROTIME_ENABLE);
	$pageContents = HttpClient::quickGet('http://www.lightnovel.cn/logging.php?action=login');
    if (strstr($pageContents,"font-family: Verdana, Tahoma; color: #666666; font-size: 11px"))
    {
        
        log_make($logininfo,"LoginError","ServerProtect",LOG_ENABLE,LOG_MICROTIME_ENABLE);
        sleep(10);
        lkextra_func_login($logininfo,$cookiefilename,$refreshcookie);
    }
	$html=str_get_html($pageContents);
	$errorPass=$html->find("div.postbox",0)->find('div.alert_info',0)->plaintext;
	if ($errorPass)
	{
		log_make($logininfo,"LoginError",'TooManyTryPass',LOG_ENABLE,LOG_MICROTIME_ENABLE);
		exit(-1);
	}
	$formhashPos=stripos($pageContents,'<input type="hidden" name="formhash" value=');
	$formhash=NULL;
	for ($i=0;$i<8;$i++)	//得到formhash
	{
		$formhash=$formhash.$pageContents[$formhashPos+44];
		$formhashPos++;
	}	
	$client->post('/logging.php?action=login&loginsubmit=yes', array(        
		'formhash' => $formhash,        
		'referer' => '',
		'loginfield' => $logininfo[1],
		'username' => $logininfo[2],
		'password' => $logininfo[3],
		'questionid' => $logininfo[4],
		'answer' => $logininfo[5],
		'loginsubmit' => 'true',
		'cookietime' => 2592000,
	));
	$cookieLKextratemp=$client->headers['set-cookie'];
	$file=fopen($cookiefilename,w);
	foreach($cookieLKextratemp as $temp)	//把$cookieLKextratemp[0]变成$cookieLKextra[lkww_sid]
	{
		$cookieTemp=explode(';',$temp);
		$cookieTemp[1]=explode('=',$cookieTemp[0]);
		$cookieLKextra[$cookieTemp[1][0]]=$cookieTemp[1][1];
		$cookie4file=$cookieTemp[1][0]."=".$cookieTemp[1][1].";";
		fwrite($file,$cookie4file);
	}
	fclose($file);
	$errCode=$client->getError();
	if($refreshcookie)
	$status="Refresh";
	else
	$status="Init";
	log_make($logininfo,$status,$errCode,LOG_ENABLE,LOG_MICROTIME_ENABLE);
	return(0);
}
function lkextra_func_make_cookie($cookiefilename)
{
	$file=fopen($cookiefilename,r);
	$temp=fgets($file);
	$cookie4file=explode(';',$temp);
	for($i=0;$i<3;$i++)
	{
		$cookieTemp=explode('=',$cookie4file[$i]);
		$cookieLKextra[$cookieTemp[0]]=$cookieTemp[1];
	}
	fclose($file);
    if ((file_exists($cookiefilename.'extra')))
    $file=fopen($cookiefilename.'extra',r);
	$temp=fgets($file);
	$cookie4file=explode(';',$temp);
	foreach($cookie4file as $cookie4filetemp)
	{
		$cookieTemp=explode('=',$cookie4filetemp);
		$cookieLKextra[$cookieTemp[0]]=$cookieTemp[1];
	}
	fclose($file);
	return $cookieLKextra;
}
?>