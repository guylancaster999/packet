<html>
<head>
</head>
<body>
<?php
/*
Program home.php in \prime
Copyright primedoc 
Written by Guy Lancaster

This implememts :
	A.Confirmation File
	A second (small) files is submitted after the main data file (first file) has been submitted.
	B.	File Age
	We wait until the age of the file exceeds a specific time (i.e. 3 minutes)
	C.	Number of Passes
	We wait until we have seen the file a set number of times (without it changing)
	D.	Delay
	We wait (in real-time) for a specific time (i.e. 30 seconds) and see if the file has changed

/cfg/zzzz.cfg
	- where zzzz is 4 char customer code
	- contains lines of the format 
			file=aaaabbb*.ccc,		 	conf=NO
			or
			file=aaaabbb*.ccc,		 	conf=YES
	where aaaa = customer code
			bbb = job code
			*   = wildcard
			ccc = suffix e.g. zip
/docs/aaaabbbddd.ccc
		where aaaa = customer code
			bbb = job code
			ddd = run code
			ccc = suffix e.g. zip
/aaaa/aaaabbbddd.ccc
	where aaaa = customer code (directory will be created if not there)
			bbb = job code
			ddd = run code
			ccc = suffix e.g. zip			
*/
function mvfl($a,$b)
{
		$fd = fopen($a, "r");
		print ">>>>>>>>>>>>>>>>>>>>>>>".$a;
		var_dump($fd);
		$data = "";
		while (!feof($fd)) 
		{
			$data .= fread($fd, 1024);
		}
		fclose($fd);
		$hdl=fopen($b,"w");
		fwrite($hdl,$data);
		fclose($hdl);
		print ">>>>>>>>>>>>>>>>>>>>>>>".$data;
	 
}

function loadCnf($fcust)
{
	$cfgFlnm="./cfg/".$fcust.".cfg";
 	$fhdl	=fopen($cfgFlnm,"r");
	$aconfg	=array();
	if ($fhdl	!=false)
	{
 		$g=true;
		while ($g!==false)
		{
			$g=fgets ($fhdl); 
 
			if ( (substr($g,0,5)=="file=") && (strpos(strtolower($g),"conf") >0) )
		    {
 				$g	=str_replace ("file=" ,"", $g);
				$pos=strpos( $g,",");
				$fl	=substr($g,0,$pos);
				$dat=substr($g,$pos+1);
				$dat=str_replace(" ","",$dat);
		 		$aconfg[$fl]	=$dat;
			}
		}
		fclose($fhdl);
	}
 	return $aconfg;
}
function fileEnd($f)
{
	return substr($f,strripos ( $f , "."));
}
function fileStart($f)
{
	return substr($f,0,strripos ( $f , "."));
	return $ret;
}
function fileCustid($f)
{
	return substr($f,0,4);
}
function get_last_n($arr,$thisPtr,$flnm)
{
	$aRet	=array();
	$ret	="#";
	$num	=count($arr);
	for ($i=0;$i<=$num && strlen($ret)>0;$i++)
	{
		$thisArr=$arr[$thisPtr];
		$ret	=$thisArr[$flnm];
		if (strlen($ret)>0)
		{
			$aRet[]=$ret;
			$thisPtr--;
			if ($thisPtr<=0)$thisPtr =count($arr)-1;
		}		 
	}
	return $aRet;
}

//-----------------------------------------------------------------------------------------------------------------
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);
echo "<br/>Incoming file detection System V.03<br/>";
$lastA				=array();
$lastAsz			=array();
$lastAtm			=array();
$passesMax			=4;
$agedForSeconds		=300;
$unchangedForSeconds=10;
$unchangedForPasses	=3;
$aptr				=-1;
for ($i=0;$i<$passesMax;$i++)$lastA[]=0;
 
for ($j=0;$j<=3;$j++) // will be while true{
{
	$thisA=(scandir("./docs"));
	array_shift( $thisA);
	array_shift( $thisA);
	$thisAsz=array();
	$thisAtm=array();
    // find out about this file 
	 for ($i=0; $i<count($thisA);$i++)
	 {
		 $f			=$thisA[$i];
		 $fsz		=filesize("./docs/".$f);
		 $ftime 	=filectime("./docs/".$f);
		 $thisAsz[$f]= $fsz;
		 $thisAtm[$f]=$ftime ;
	 }
	 //store files
	 $aptr++;
	 if ($aptr>=$passesMax)$aptr=0;
	 $lastAsz[$aptr]=$thisAsz;
	 $lastAtm[$aptr]=$thisAtm;
	// are we done?
	for ($i=0; $i<count($thisA);$i++)
	{
		$finished	=false;
		$f 			=$thisA[$i];
	 	$fcust		=fileCustid($f);	
		$hasConf 	=false;
		$aCnf		=loadCnf($fcust);
		
		foreach($aCnf as $a=>$b)
		{
			 if  (fnmatch($a,$f) && strpos($b,"conf=YES")!==false)
			{
				$hasConf=true;	
				break;
			}
		}
  		if ($hasConf )
		{
			$flconf="./docs/".fileStart($f).".conf";
 print "<br/>opening ".	$flconf."<br/>";		
			$finished=fopen($flconf,"r");
			print "opened ";
		var_dump( $finished);
 			if (($finished!==false)&& (fileEnd($a)!=".conf"))
	        {
				if (!mkdir("./".$fcust)) print " failed ";
                print "<br/>Move : ./docs/".$thisA[$i]."=> ./".$fcust."/".$thisA[$i]."<br/>";
				mvfl("./docs/".$thisA[$i],      "./".$fcust."/".$thisA[$i]);			 
			}
			fclose($finished);
		}
		else
		{	
		//test c
		$same=true;
		$aRetSz=get_last_n($lastAsz,$aptr,$f);
		$aRetTm=get_last_n($lastAtm,$aptr,$f);
		
		if (count($aRetSz)>=$unchangedForPasses)
		{
			$v=$aRetSz[0];
			for ($k=0;$k<$unchangedForPasses && $same; $k++)
			{
				$v1=$aRetSz[$k];
				$same=($v1==$v);		
			}
		}
		$finished=$same;
		if (!$finished)
		{
			$finished=(date() -$aRetTm[count($aRetTm)-1])>$agedForSeconds;
		}	
		if (!$finished)
		{
			//testb
			$unchangedForSeconds=10;
			if (count($aRetSz)>=1)
			{
				$v=$aRetSz[0];
				for ($k=1;$k<count($aRetSz) && $same && (!$finished); $k++)
				{
					$v1			= $aRetSz[$k];
					$same		=($v1==$v);		
					$timeDif 	= $aRetTm[0]-$aRetTm[$k];
					$finished	=$same && ($timeDif >$unchangedForSeconds);
				}
			}	
		}	
		if ($finished)
		{
print "<br/>Create dir 	./".$fcust;			
			if (!mkdir($fcust))print " failed ";
			mvfl( "./docs/".$f, "./".$fcust."/".$f);
		}
		}  //has conf=N
	} //for
} // while forever
?>
</body>
</html>