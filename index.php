<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('vendor/autoload.php');
require_once("config.php");

session_start();

$client = new Google_Client();
$client->setClientId($config['oauth']['CLIENT_ID']);
$client->setClientSecret($config['oauth']['CLIENT_SECRET']);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
    FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);