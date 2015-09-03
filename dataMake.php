<?php

function saveConnections($loggedUser,$followerArray,$database,$twitConn)
{
	$user = $loggedUser;
	$followers = $followerArray;
	$conn = $database;
	$j = sizeof($followers);
	$connectionT = $twitConn;
	for($i=0;$i<$j;$i++)
	{
		$source = $followers[$i];
		$sqlquery = "Select * from userdata where source=$source AND target=$user";
		if($result = mysqli_query($conn,$sqlquery))
		{
			$row_cnt = mysqli_num_rows($result);
			if($row_cnt==0)
			{
				$query = "INSERT INTO userdata (source,target) VALUES($source,$user)";
				if ($conn->query($query) === TRUE)
				{
					//echo "Saved connection";
				}
			}
		}
		$newQuery = "Select * from gather where userID=$source";
		if($result = mysqli_query($conn,$newQuery))
		{
			$row_cnt = mysqli_num_rows($result);
			if($row_cnt==0)
			{
				$query = "INSERT INTO gather (userID) VALUES($source)";
				if ($conn->query($query) === TRUE)
				{
					//echo "Saved connection";
				}
			}
		}

	}
	$limit = checkRateLim($connectionT);
	$query = mysqli_query($conn,"Select * from gather");
	$array = array();
	while($row = mysqli_fetch_assoc($query))
	{
	  // add each row returned into an array
		$array[] = $row;
	}
	$data = $array;
	print_r($data);
	$go = sizeof($data);
	for($i=0;$i<$go;$i++)
	{
		$userTemp = $data[$i];
		$user = $userTemp['userID'];
		//print_r($user);
		try
		{
			$limit = checkRateLim($connectionT);
			if($limit>0)
			{
				$level2 = $connectionT->get("followers/ids", array("user_id"=>$user,"count"=>'5000'));
				$array = $level2->ids;
				//print_r($array);
				if(is_array($array))
				{
					$query = "DELETE FROM gather WHERE userID=$user"; 
					if ($conn->query($query) === TRUE)
					{
						saveThis($user,$array,$conn);	//echo "Saved connection";
					}
				}
			}
			else
			{
				echo "No more data is available at this time";
			}
		}
		catch(Exception $e)
		{
			echo "This is all the data for now";
		}
	}

}

function saveThis($target,$sourcesArray,$dataConn)
{
	$_target = $target;
	$sources = $sourcesArray;
	$conn = $dataConn;
	$j = sizeof($sources);
	for($i=0;$i<$j;$i++)
	{
		$source = $sources[$i];
		$sqlquery = "Select * from userdata where source=$source AND target=$_target";
		if($result = mysqli_query($conn,$sqlquery))
		{
			$row_cnt = mysqli_num_rows($result);
			if($row_cnt==0)
			{
				$query = "INSERT INTO userdata (source,target) VALUES($source,$_target)";
				if ($conn->query($query) === TRUE)
				{
					//echo "Saved connection";
				}
			}
		}
	}
}
function checkRateLim($twitter_connection)
{
	$connectionT = $twitter_connection;
	//rate limiting excercise
	$RateLim = $connectionT->get("application/rate_limit_status", array('resources' => 'followers'));
	//print_r($RateLim);
	$remaining = ($RateLim->resources);
	$remrem = ($remaining->followers);
	$remember = ($remrem-> {'/followers/ids'});
	$RemLast = ($remember->remaining);
	return $RemLast;

}

function MakeJson($loggedUser,$conDB)
{

	$user=$loggedUser;
	$conn = $conDB;
	$query = mysqli_query($conn,"Select source from userdata where target=$user");
	$array = array();
	while($row = mysqli_fetch_assoc($query))
	{
  		// add each row returned into an array
		$array[] = $row;
	}
	//print_r($array);
	$k=count($array);

	for($i=0;$i<$k;$i++)
	{
		$getfollower = $array[$i];
	 	$follower = $getfollower["source"];
		writeFile($follower,$user);
		secondWrite($follower,$conn);
	}
}

function secondWrite($one,$two)
{
	$person = $one;
	$conn = $two;
	$includedCount=0;
	$query = mysqli_query($conn,"Select source from userdata where target=$person");
	$check = mysqli_query($conn,"Select * from userdata where source = $person");
	$array = array();
	if($result = mysqli_query($conn,"Select source from userdata where target=$person"))
	{
		$num_rows=mysqli_num_rows($result);
		if($num_rows>0)
		{
			while($row = mysqli_fetch_assoc($query))
			{
  				// add each row returned into an array
				$array[] = $row;
			}
			$len = sizeof($array);
			for($i=0;$i<$len;$i++)
			{
				$getfollower = $array[$i];
				$getSource = $getfollower["source"];
				if($check = mysqli_query($conn,"Select * from userdata where source = $getSource"))
				{
					$rows = mysqli_num_rows($check);
					{
						if($rows>2)
						{
							if($includedCount<5)
							{
								writeFile($getSource,$person);
								$includedCount= $includedCount+1;
							}
						}
					}
				}
			}
		}
	}
}

function writeFile($argumentOne,$argumentTwo)
{
	$source = $argumentOne;
	$target = $argumentTwo;
	//createfile();
	$myfile = fopen("datasave.csv","a");
	$EOL = PHP_EOL;
	$comma = ",";
	$txt = $source;
	//echo $txt;
	fwrite($myfile,$source);
	fwrite($myfile,$comma);
	fwrite($myfile,$target);
	fwrite($myfile,",1");
	fwrite($myfile,$EOL);
	
}
function createfile()
{
	$myfile = fopen("datasave.csv","a");
	fwrite($myfile,"source,");
	fwrite($myfile,"target,");
	fwrite($myfile,"value");
	fwrite($myfile,PHP_EOL);
	fclose($myfile);
}

function deleteFile()
{
	$file='datasave.csv';
	if(file_exists(filename))
	{
		unlink('datasave.csv');
	}
}

?>