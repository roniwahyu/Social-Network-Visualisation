<?php
require 'twitteroauth/autoload.php';
require 'dataMake.php';
use Abraham\TwitterOAuth\TwitterOAuth;
session_start();

$consumer_key = "***************";
$consumer_secret = '*******************';
$oauth_callback =  'http://127.0.0.1/Login/callback.php';

$request_token = [];
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

if(isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] != $_REQUEST['oauth_token'])
{
	echo "Something is wrong";
}
$connection = new TwitterOAuth($consumer_key,$consumer_secret,$request_token['oauth_token'],$request_token['oauth_token_secret']);
$access_token = $connection->oauth("oauth/access_token",array("oauth_verifier" => $_REQUEST['oauth_verifier']));
$_SESSION['access_token'] = $access_token;
$connectionT = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$credentials = $connectionT->get("account/verify_credentials");//this works
$currentUser = ($credentials->id);
$name = ($credentials->name);
$_SESSION['cUser'] = $currentUser;
$_SESSION['username'] = $name;
echo "welcome, ";
echo $_SESSION['username'];
echo "<a href='newvisual.php'>Build My Network</a>";
echo "<br>";
echo "<a href='FinishGatherTable.php'>Gather More Data</a>";


?>
