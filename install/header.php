<?php
if(!isset($logout))
	define('GO_NO_SESSION',true);

require(dirname(dirname(__FILE__)).'/GO.php');

if(isset($logout)){
	//make sure exiting logins are killed
	GO::session()->logout();
}

function redirect($url){
	header('Location: '.$url);
	exit();
}

function printHead()
{
	echo '<html><head>'.
	'<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />'.
	'<link href="install.css" rel="stylesheet" type="text/css" />'.
	'<title>'.GO::config()->product_name.' Installation</title>'.
	'</head>'.
	'<body style="font-family: Arial,Helvetica;background-color:#f1f1f1">';
	echo '<form method="post">';
	echo '<div style="width:800px;padding:20px;margin:10px auto;background-color:white">';
	echo '<img src="logo.gif" border="0" align="middle" style="margin:0px;margin-bottom:20px;" />';
	
}

function printFoot()
{
	$footer = '</div></form>' . "\n" .
    "<script src='//cdn.maestrano.com/apps/mno_libs/mno-loader.js' type='text/javascript'></script>" . "\n" .
    "<script type='text/javascript'>
      window.mnoLoader.init('groupoffice','1');
    </script>" . "\n" .
    '</body></html>';
  
  echo $footer;
}

function errorMessage($msg){
	echo '<p class="errortext">'.$msg.'</p>';
}

function continueButton(){
	echo '<br /><div align="right"><input type="submit" value="Continue" /></div>';
}