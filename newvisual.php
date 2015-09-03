<?php
require 'twitteroauth/autoload.php';
require 'DataMake.php';
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
if ($conn->connect_error)
{
	die("Connection failed: ". $conn->connect_error);
}
$currentUser = $_SESSION['cUser'];
$sqlquery = "Select * from registered where userID=$currentUser";
if($result = mysqli_query($conn,$sqlquery))
{
	$row_cnt = mysqli_num_rows($result);
	if($row_cnt==0)
	{
		$limit= checkRateLim($connectionT);
		//print_r($limit);

		if($limit>0)
		{
			$row_cnt = mysqli_num_rows($result);
			if($row_cnt==0)
			{
				$user = $connectionT->get("followers/ids", array("count"=>'5000'));
				//print_r($user);
				$array = $user->ids;
				$sql = "INSERT INTO registered (userID) VALUES($currentUser)";
				if ($conn->query($sql) === TRUE)
				{
					//echo "NEW RECORD CREATED SUCCESSFULLY";
				}
			//	print_r($array);
				saveConnections($currentUser,$array,$conn,$connectionT);
				deleteFile();
				createFile();
				MakeJson($currentUser,$conn);
			}
		}
		else
		{
			echo "Due to limitations we cannot retrieve any more data at this time. Please try again soon.";
		}
	}
	else
	{
		deleteFile();
		createfile();
		MakeJson($currentUser,$conn);
	}
}

echo "We have the data that is currently available for your network.";
echo "<a href='visualisation.php'>Visualise!</a>";


?>