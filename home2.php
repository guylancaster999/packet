<html>
<head>
</head>
<body>
<h1>Scheduler v.2</h1>
<?php
// load and schedule one file for each job--------------------------------------------
function processJob ($s)
{
	$Solution	="";
	$aJob		= array();  
	$aDependency= array();
	$aFlag		= array();
	$fhdl		= fopen($s);
	if ($fhdl	==false)echo "Error - No File".$s."<br/>";
	else	
	{
		// load data----------------------
		$ctr=0;
		$g	=true;
		while ($g!==false)
		{
			$g			=fgets($fhdl); 
			$Job        =substr($g,0,1);
			$Dependency =substr($g,3,1);
			echo $Job."=>". $Dependency ."<br/>";
			if (strlen($Dependency)==0)$Solution .= $Job;
			else		
			{	
				$aJob[]     =$Job;
				$aDependency[]=$Dependency;
				$aFlag[]	  =true;
				$ctr++;
			}
		}
		// process dependencies if any-------------------------------
		$lastTime=999;
		while (($ctr>=0) && ($lastTime>0)) // something to do and something happened last time
		{
			$lastTime=0;
			for ($i=0;$i<=$ctr;$i++)
			{
				if ($aFlag[$i])
				{
					if (strpos($Solution,$aDependency[$i])!== false)
					{
						$aFlag[$i]=false;
						$Solution.=$aJob[$i];
						$lastTime++;
						$ctr--;
					}
				}				
			}
		}
		if ($lastTime==0)echo "Error - Cyclic<br/>";
		else echo "Solution is ".$Solution."<br/>";
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