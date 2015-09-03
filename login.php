<?php
require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
session_start();

$consumer_key = "rpDEdoWwo8Yv7hteea1rKwTHA";
$consumer_secret = "N4fBrzBQy3ZyaH9kqTml4MZNNrCTiSzIVFl5BaATgyi4K4hrEb";
$oauth_callback = 'http://127.0.0.1/Login/newcallback.php';
$connection = new TwitterOAuth($consumer_key,$consumer_secret);
$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $oauth_callback));

$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token'],"force_login" => "true"));
header('Location: ' . $url);

?>