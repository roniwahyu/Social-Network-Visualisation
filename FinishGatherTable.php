<?php
require 'twitteroauth/autoload.php';
require 'dataMake.php';
use Abraham\TwitterOAuth\TwitterOAuth;
session_start();
$consumer_key = "rpDEdoWwo8Yv7hteea1rKwTHA";
$consumer_secret = 'N4fBrzBQy3ZyaH9kqTml4MZNNrCTiSzIVFl5BaATgyi4K4hrEb';
$access_token = $_SESSION['access_token'];
$connectionT = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$servername = "localhost";
$username = "kieran";
$password = "";
$dbname = "twitterdata";
$conn = new mysqli($servername, $username, $password, $dbname);

$limit = checkRateLim($connectionT);
$query = mysqli_query($conn,"Select * from gather");
$array = array();
while($row = mysqli_fetch_assoc($query))
{
  // add each row returned into an array
	$array[] = $row;
}
$data = $array;
//print_r($data);
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
					saveThis($user,$array,$conn);	
					echo "Saved connection";
				}
			}
		}
		else
		{
			sleep (900);
			echo "No more data is available at this time";
			$level2 = $connectionT->get("followers/ids", array("user_id"=>$user,"count"=>'5000'));
			$array = $level2->ids;
			//print_r($array);
			if(is_array($array))
			{
				$query = "DELETE FROM gather WHERE userID=$user"; 
				if ($conn->query($query) === TRUE)
				{
					saveThis($user,$array,$conn);	
					echo "Saved connections. Resuming Data Retrieval";
				}
			}
		}
	}
	catch(Exception $e)
	{
		echo "This is all the data for now";
	}
}
?>