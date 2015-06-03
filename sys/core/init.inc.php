<?php
/*
 * Enable sessions
*/
session_start();
if(@$_GET['logout']=='yes'/* || !$_SESSION['student_reg_id'] || !$_COOKIE['student_set']*/){
	setcookie("rem_emp", '', -(time()+(60*60*15)),'/');
	session_destroy();
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}
/******** Expire the session after a few minutes  *********
if(!@$_COOKIE['rem_emp']  && !@$_GET['return_url']){			//First confirm there is no cookie.
	if(isset($_SESSION['started']) && @$_SESSION['user']){	//Check if there has been any activity in the past 15mins
		if((time() - $_SESSION['started']) > 60*15){		
			unset($_SESSION['user']); 				//If no activity in the past 15mins, destroy student_reg_id Session 
			header('Location: http://'.$_SERVER['HTTP_HOST']);	//Redirect to students login
		}
	}else{
	  $_SESSION['started'] = time();								//Upon any activity, recreate the timer.
	}
}
/******** End Expire the session after a few minutes  *********/

/*
* Include the necessary configuration info
*/
include_once 'config/db-cred.inc.php';
/*
* Define constants for configuration info
*/
foreach ( $C as $name => $val ){
	define($name, $val);
}
define('WEB_ROOT', "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['HTTP_HOST']);

/**
* Define a generic salt
*/
$salt  = "j^i207soif5+_7~%4*%03dql";/*
* Create a PDO object
*/
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
try{
	$dbo = new PDO($dsn, DB_USER, DB_PASS);
	$dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	echo "Failed database connection: ".$e->getMessage();
	exit();
}

/*
* Define the auto-load function for classes
*/
include_once "class/class.db_connect.inc.php";
include_once "class/class.fxn.methodist.inc.php";
include_once "class/class.auth.methodist.inc.php";
?>