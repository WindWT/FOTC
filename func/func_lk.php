<?php
class lk_func{
    private $loginCredential;
    private $cookieFilename;
    private $cookie;
    private $pageContents;
    private $errCode;
    private $client;
    private $HTTPdebug=false;
    public function lk_func($logininfo)
    {
        $this->loginCredential=$logininfo;
        $this->cookieFilename="cookie/LK_".md5($this->loginCredential[2].$this->loginCredential[1]);
        $this->client = new HttpClient('www.lightnovel.cn');
        $this->client->setDebug($this->HTTPdebug);
        if ((!file_exists($this->cookieFilename)))   //不存在cookie文件时重新登录
    	{
    		$this->login();
    		$this->view();
    	}
    	else
    	{
    	    $this->view();
    	}
    }
    private function initurl($geturl='')
    {
        if($geturl)
        {
            return $geturl;
        }
        else
        {
            $type=rand(1,3);
      		$tid=rand(10000,200000);
        	$fid=rand(30,130);
            switch ($type)
        	{
       			case 1:
       			{
       				$url='/thread-'.$tid.'-1-1.html';
       				break;
       			}
       			case 2:
       			{
       				$url='/forum-'.$fid.'-1.html';
                    break;
       			}
        		case 3:
        		{
        			$url='/forum.php';
        			break;
        		}
                /*case 4: //专用于每日任务
                {
                    $url='/home.php?mod=task&do=apply&id=98';
                    break;
                }*/
       		}
            return $url;
        }
    }
    private function view($url=NULL)
    {
        log_runtime(0,LOG_MICROTIME_ENABLE);  //计时开始
  		$this->readcookie($this->cookieFilename);    //将cookie文件读取出来
   		$this->client->setCookies($this->cookie);   //设定cookie
        $url=$this->initurl($url);
        $this->client->get($url);
        $this->pageContents = $this->client->getContent();
        $this->errCode=$this->client->getError();
        //echo $this->pageContents;
        $this->errCheck(); //用返回的页面信息判断是否正常登录
     	return(0);
    }
    private function login($refreshcookie=0) //登录
    {
        log_runtime(0,LOG_MICROTIME_ENABLE);
    	$pageContents = HttpClient::quickGet('http://www.lightnovel.cn/member.php?mod=logging&action=login');
    	$html=str_get_html($pageContents);
    	/*$errorPass=$html->find("div.postbox",0)->find('div.alert_info',0)->plaintext;
    	if ($errorPass)
    	{
    		log_make($logininfo,"Error",'TooManyTryPass',LOG_ENABLE,LOG_MICROTIME_ENABLE);
    		exit(-1);
    	}*/
    	$formhashPos=stripos($pageContents,'<input type="hidden" name="formhash" value=');
    	$formhash=NULL;
    	for ($i=0;$i<8;$i++)	//得到formhash
    	{
    		$formhash=$formhash.$pageContents[$formhashPos+44];
    		$formhashPos++;
    	}
        $postAddress=$html->find('form[name=login]',0)->action;
        $postAddress=str_replace('&amp;','&',$postAddress);
    	$this->client->post('/'.$postAddress, array(        
    		'formhash' => $formhash,        
    		'referer' => '',
    		'loginfield' => $this->loginCredential[1],
    		'username' => $this->loginCredential[2],
    		'password' => $this->loginCredential[3],
    		'questionid' => $this->loginCredential[4],
    		'answer' => $this->loginCredential[5],
    		'loginsubmit' => 'true',
    		'cookietime' => 2592000,
    	));
    	$cookieRaw=$this->client->headers['set-cookie'];
    	$file=fopen($this->cookieFilename,w);
    	foreach($cookieRaw as $temp)	//把$cookieRaw[0]变成$cookie[lkww_sid]
    	{
    		$cookieTemp=explode(';',$temp);
    		$cookieTemp[1]=explode('=',$cookieTemp[0]);
    		$this->cookie[$cookieTemp[1][0]]=$cookieTemp[1][1];
    		$cookie4file=$cookieTemp[1][0]."=".$cookieTemp[1][1].";";
    		fwrite($file,$cookie4file);
    	}
    	fclose($file);
    	$this->errCode=$this->client->getError();
    	if($refreshcookie)
    	$status="Refresh";
    	else
    	$status="Init";
    	log_make($this->loginCredential,$status,$errCode,LOG_ENABLE,LOG_MICROTIME_ENABLE);
    	return(0);
    }
    private function readcookie()    //处理cookie文件
    {
    	$file=fopen($this->cookieFilename,r);
    	$temp=fgets($file);
    	$cookie4file=explode(';',$temp);
    	foreach($cookie4file as $cookie4file_line)
    	{
    		$cookieTemp=explode('=',$cookie4file_line);
    		$this->cookie[$cookieTemp[0]]=$cookieTemp[1];
    	}
    	fclose($file);
    }
    private function errCheck()
    //处理各种可能的错误
    //目前能处理以下几种：
    //1：请求未得到任何回应（判定服务器无法连接，直接结束）
    //2：回应Service Unavailable（服务器过载，结束）
    //3：正常回应，但没有登录（重新登录一次）  //可能会在这里死循环
    //（4：回应密码错误过多（结束）   //该状态暂时不存在）
    {
        if (!$this->pageContents) //待处理：可能成功申请任务后无返回信息
    	{
    		log_make($this->loginCredential,"Error","TimeOut",LOG_ENABLE,LOG_MICROTIME_ENABLE);
    		return(0);
    	}
        if (substr_count($this->pageContents,iconv('UTF-8','GBK','抱歉，本期您已申请过此任务，请下期再来'))) //专用于每日任务
        {
            log_make($this->loginCredential,"View","Task_AlreadyDone",LOG_ENABLE,LOG_MICROTIME_ENABLE);
            $this->view('/home.php?mod=task&do=draw&id=98');
      		return(0);
        }
        if (substr_count($this->pageContents,iconv('UTF-8','GBK','不是进行中的任务')))    //专用于每日任务
        {
            log_make($this->loginCredential,"View","Task_AlreadyDraw",LOG_ENABLE,LOG_MICROTIME_ENABLE);
    		return(0);
        }
        if (substr_count($this->pageContents,iconv('UTF-8','GBK','抱歉，您所在的用户组不允许申请此任务')))    //专用于每日任务
        {
            log_make($this->loginCredential,"View","Task_Disallowed",LOG_ENABLE,LOG_MICROTIME_ENABLE);
    		return(0);
        }
        if (substr_count($this->pageContents,'Service Unavailable'))
        {
            log_make($this->loginCredential,"Error","ServiceUnavailable",LOG_ENABLE,LOG_MICROTIME_ENABLE);
    		return(0);
        }
        if (substr_count($this->pageContents,'Bad Request (Invalid Hostname)'))
        {
            log_make($this->loginCredential,"Error","BadRequest",LOG_ENABLE,LOG_MICROTIME_ENABLE);
    		return(0);
        }
    	$html=str_get_html($this->pageContents);
        $loginDiv="fastlg cl";
    	if ($html->find("div[class=$loginDiv]",0))
    	{
            log_make($this->loginCredential,"Error","CookieExpire",LOG_ENABLE,LOG_MICROTIME_ENABLE);
            $this->login(1);
            $this->view();
            return(0);
    	}
        else
        {
            log_make($this->loginCredential,"View",$errCode,LOG_ENABLE,LOG_MICROTIME_ENABLE);   //计时结束，日志记录
            return(0);
        }
    }
}
?>