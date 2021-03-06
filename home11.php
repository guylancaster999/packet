<html>
<head>
</head>
<body  style="font-family:Arial"     >
<h1>Scheduler v.11</h1>
<?php
//  remove a few lines of code-------------------------------------------------------------------

function processJob ($s)
{ //initialise------------------------------- 
	$Solution	="";
	$aJob		= array();  
	$aDependency= array();
	$aFlag		= array();
	$self		= false;
	// load data----------------------
	$s			="http://cuthbertson.de/sched/docs/".$s;
	echo "<hr/>File:".$s."<br/>";
	$fhdl		= fopen($s,"r");
	if ($fhdl===false)echo "Error - No File ".$s."<br/>";
	else	
	{
		$ctr	= 0;
		$g		= true;
		while ($g!==false)
		{
			$g			=fgets($fhdl); 
			$Job        =trim(substr($g,0,1));
			$Dependency =trim(substr($g,3,1));
			if (strlen($Job)>0)
			{
				echo $Job."=>". $Dependency ."<br/>";
				if ($Job==$Dependency)$self=true;
				elseif (strlen($Dependency)==0)$Solution .= $Job;
				else		
				{	
					$aJob[]     	=$Job;
					$aDependency[]	=$Dependency;
					$aFlag[]	  	=true;
					$ctr++;
				}
			}
		}
		if ($self)echo "Error - Jobs can't depend on themselves";
		elseif ($ctr==0)echo "No dependencies, Solution :".$Solution;
		else
		{ 	// process dependencies ------------------------------
			$ctrMax		=$ctr;
			do
			{
				$lastTime=0;
				for ($i=0; $i<$ctrMax; $i++)
				{
					if ($aFlag[$i] && (strpos($Solution,$aDependency[$i])!== false) )
					{
							$aFlag[$i]=false;
							$Solution.=$aJob[$i];
							$lastTime++;
							$ctr--;				
							break;
					}
				}
			}
			while (($ctr>0) && ($lastTime>0)); // something to do and something happened last time
			if ($lastTime==0 && $ctr>0) echo "Error - Cyclic<br/>";
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