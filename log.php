<?php
function log_make($logininfo,$status,$errCode,$enablelog,$enableruntime)
{
	$date=getdate();
	if($date[mon]<10)
	$logfilename=$date[year].'0'.$date[mon];
	else
	$logfilename=$date[year].$date[mon];
	if($date[mday]<10)
	$logfilename.='0'.$date[mday].'.log';
	else
	$logfilename.=$date[mday].'.log';
	if ($enablelog)
	{
		if (!is_dir("log"))
		mkdir("log");
		$logfile=fopen("log/".$logfilename,a);	//logfile
		$date=getdate();
		if($date[hours]<10)
		fwrite($logfile,"0".$date[hours]);
		else
		fwrite($logfile,$date[hours]);
		if($date[minutes]<10)
		fwrite($logfile,':0'.$date[minutes]);
		else
		fwrite($logfile,':'.$date[minutes]);
		if($date[seconds]<10)
		fwrite($logfile,':0'.$date[seconds]);
		else
		fwrite($logfile,':'.$date[seconds]);
		if($logininfo[0])
		fwrite($logfile,"\tForum:".$logininfo[0]);
		fwrite($logfile,"\t".$logininfo[1].':'.$logininfo[2]);
		if($status)
		fwrite($logfile,"\tStatus:".$status);
		if($errCode)
		fwrite($logfile,"\tError:".$errCode);
		if($enableruntime)
		{
			$logruntime=(int)log_runtime(1,$enableruntime);
			fwrite($logfile,"\tRunTime:".$logruntime."ms");
		}
		fwrite($logfile,"\r\n");
		fclose($logfile);
	}
}
function log_runtime($mode,$enable)
{
	if(!$enable)
	return;
	static $runtime;
	if(!$mode)
	{
		$runtime=microtime(1);
		return;
	}
	$runtime1=microtime(1);
	return ($runtime1-$runtime)*1000;
}
function log_runtime_overall($mode,$enable)
{
	if(!$enable)
	return;
	static $runtimeoverall;
    switch ($mode)
    {
        case 0: //init
    	{
    		$runtimeoverall=microtime(1);
    		$runtimeinfo[1]="RunTime";
    		$runtimeinfo[2]="Start";
    		log_make($runtimeinfo,"StartLine----------------------------------------",0,LOG_ENABLE,0);
    		return 0;
    	}
        case 1; //success
        {
            $runtimeoverallfin=microtime(1);
        	$runtimeinfo[1]="RunTimeOverall";
        	$runtimeinfoms=(int)(($runtimeoverallfin-$runtimeoverall)*1000);
        	$runtimeinfomin=(int)($runtimeinfoms/60000);
        	$runtimeinfosec=(int)($runtimeinfoms%60000/1000);
        	$runtimeinfo[2]=$runtimeinfoms."ms(";
        	if($runtimeinfomin)
        	$runtimeinfo[2].=$runtimeinfomin."m";
        	$runtimeinfo[2].=$runtimeinfosec."s)";
        	log_make($runtimeinfo,"EndLine----------------------------------------",0,LOG_ENABLE,0);
            return 0;
        }
        case -1:    //pause
        {
            $runtimeoverallfin=microtime(1);
        	$runtimeinfo[1]="RunTimeOverall";
        	$runtimeinfoms=(int)(($runtimeoverallfin-$runtimeoverall)*1000);
        	$runtimeinfomin=(int)($runtimeinfoms/60000);
        	$runtimeinfosec=(int)($runtimeinfoms%60000/1000);
        	$runtimeinfo[2]=$runtimeinfoms."ms(";
        	if($runtimeinfomin)
        	$runtimeinfo[2].=$runtimeinfomin."m";
        	$runtimeinfo[2].=$runtimeinfosec."s)";
        	log_make($runtimeinfo,"PauseLine----------------------------------------",0,LOG_ENABLE,0);
            return 0;
        }
    }
	
}
?>