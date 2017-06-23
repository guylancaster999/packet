<html>
<head>
</head>
<body>
<h1>Scheduler v.8</h1>
<?php
// remove blank lines, fix cyclic-----------------------------------------------------

function processJob ($s)
{
	$s			="http://cuthbertson.de/sched/docs/".$s;
	echo "<hr/>File:".$s."<br/>";
	$Solution	="";
	$aJob		= array();  
	$aDependency= array();
	$aFlag		= array();
	$fhdl		= fopen($s,"r");
 
	if ($fhdl	===false)echo "Error - No File ".$s."<br/>";
	else	
	{
		// load data----------------------
		$ctr	=0;
		$g		=true;
		while ($g!==false)
		{
			$g			=fgets($fhdl); 
			$Job        =trim(substr($g,0,1));
			$Dependency =trim(substr($g,3,1));
			if (strlen($Job)>0)
			{
				echo "[".$Job."]=>[". $Dependency ."]<br/>";
				if (strlen($Dependency)==0)$Solution .= $Job;
				else		
				{	
					$aJob[]     =$Job;
					$aDependency[]=$Dependency;
					$aFlag[]	  =true;
					$ctr++;
				}
			}
		}
		if ($ctr==0)echo "No dependencies  , Solution :".$Solution;
		else
		{
		// process dependencies ------------------------------
		$lastTime=999;
print 	"ctr=".$ctr."<br/>";
$ctrMax=$ctr;
		while (($ctr>0) && ($lastTime>0)) // something to do and something happened last time
		{
			$lastTime=0;
			for ($i=0; $i<$ctrMax; $i++)
			{
				
print " flag ".$i."=".(	$aFlag[$i]?"T":"F")."<br/>";			
				if ($aFlag[$i])
				{
					print "compare ".$Solution." ".$aDependency[$i]."<br/>";
					if (strpos($Solution,$aDependency[$i])!== false)
					{
						$aFlag[$i]=false;
						$Solution.=$aJob[$i];
						$lastTime++;
						$ctr--;				
echo "[".$aJob[$i]."] ctr=". $ctr." lastTime=".$lastTime."<br/>";
						
					}
				}				
			}
echo "* ctr=". $ctr." lastTime=".$lastTime."<br/>";
			
			
			
		}
echo "	 ctr=". $ctr." lastTime=".$lastTime."<br/>";


		if ($lastTime==0 && $ctr>0)echo "Error - Cyclic".$Solution."<br/>";
		else echo "Solution is ".$Solution."<br/>";
	 }
	}
}
processJob("job1.txt");
processJob("job2.txt");
processJob("job3.txt");
processJob("job4.txt");
processJob("job5.txt");
processJob("job6.txt");
processJob("job7.txt");
?>
</body>
</html>