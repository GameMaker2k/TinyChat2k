<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.

    Copyright 2012 Cool Dude 2k - http://idb.berlios.de/
    Copyright 2012 Game Maker 2k - http://intdb.sourceforge.net/
    Copyright 2012 Kazuki Przyborowski - https://github.com/KazukiPrzyborowski

    $FileInfo: api.php - Last Update: 12/20/2012 Ver. 1.0.0 - Author: cooldude2k $
*/

ob_start();
header("Content-Type: text/plain; charset=UTF-8");
$_GET['room'] = preg_replace("/[^a-z0-9]/", "", strtolower($_GET['room']));
$roomname = $_GET['room'];
if($roomname=="") { echo "{error:room};"; exit(); }
if(!file_exists("./sessions/")) { mkdir("./sessions/"); }
if(!file_exists("./sessions/".$roomname."/")) { mkdir("./sessions/".$roomname."/"); }
session_save_path("./sessions/".$roomname."/");
session_name($roomname);
session_start();
$sqlprefix = $roomname."_";
require("./sqlite.php");
if(!isset($_GET['act'])) { $_GET['act'] = "view"; }
if(!isset($_GET['room'])) { $_GET['room'] = "tinychat"; }
if($_GET['act']=="login") { 
$findmember = sqlite3_query($slite3, "SELECT COUNT(*) AS count FROM \"".$sqlprefix."members\" WHERE name='".sqlite3_escape_string($slite3, $_POST['username'])."';"); 
$nummember = sql_fetch_assoc($findmember);
$numrows = $nummember['count'];
if($numrows<=0) { echo "{warning:newuser};"; }
if($numrows>0) {
$findmember = sqlite3_query($slite3, "SELECT * FROM \"".$sqlprefix."members\" WHERE name='".sqlite3_escape_string($slite3, $_POST['username'])."';"); 
$memberinfo = sql_fetch_assoc($findmember); 
if($_POST['userpass']==$memberinfo['password']) {
sqlite3_query($slite3, "UPDATE \"".$sqlprefix."members\" SET \"lastactive\"='".sqlite3_escape_string($slite3, time())."' WHERE \"id\"=".$memberinfo['id'].";");
$_SESSION['userid'] = $memberinfo['id'];
$_SESSION['username'] = $memberinfo['name'];
echo "{success:loginuser};"; }
if($_POST['userpass']!=$memberinfo['password']) {
setcookie(session_id(), "", time() - 3600);
echo "{error:loginuser};"; } } }
if($_GET['act']=="signup") {
$findmember = sqlite3_query($slite3, "SELECT COUNT(*) AS count FROM \"".$sqlprefix."members\" WHERE name='".sqlite3_escape_string($slite3, $_POST['username'])."';"); 
$nummember = sql_fetch_assoc($findmember);
$numrows = $nummember['count'];
if($numrows<=0) {
sqlite3_query($slite3, "INSERT INTO \"".$sqlprefix."members\" (\"name\", \"password\", \"joined\", \"lastactive\", \"validated\", \"bantime\", \"admin\", \"ip\") VALUES ('".sqlite3_escape_string($slite3, $_POST['username'])."', '".sqlite3_escape_string($slite3, hash("sha512", $_POST['userpass']))."', '".sqlite3_escape_string($slite3, time())."', '".sqlite3_escape_string($slite3, time())."', 'yes', 0, 'no', '".sqlite3_escape_string($slite3, $_SERVER['REMOTE_ADDR'])."');"); 
$usersid = sqlite3_last_insert_rowid($slite3); 
$findmember = sqlite3_query($slite3, "SELECT * FROM \"".$sqlprefix."members\" WHERE name='".sqlite3_escape_string($slite3, $_POST['username'])."';"); 
$memberinfo = sql_fetch_assoc($findmember); 
echo "{success:loginuser};"; } 
if($numrows>0) { echo "{error:loginuser};"; } }
if($_GET['act']=="message") { 
if(isset($_SESSION['userid'])&&isset($_SESSION['username'])&&isset($_POST['message'])) {
echo "INSERT INTO \"".$sqlprefix."messages\" (\"userid\", \"username\", \"timestamp\", \"message\", \"ip\") VALUES ('".sqlite3_escape_string($slite3, $_SESSION['userid'])."', '".sqlite3_escape_string($slite3, $_SESSION['username'])."', '".sqlite3_escape_string($slite3, time())."', '".sqlite3_escape_string($slite3, $_POST['message'])."', '".sqlite3_escape_string($slite3, $_SERVER['REMOTE_ADDR'])."');";
sqlite3_query($slite3, "INSERT INTO \"".$sqlprefix."messages\" (\"userid\", \"username\", \"timestamp\", \"message\", \"ip\") VALUES ('".sqlite3_escape_string($slite3, $_SESSION['userid'])."', '".sqlite3_escape_string($slite3, $_SESSION['username'])."', '".sqlite3_escape_string($slite3, time())."', '".sqlite3_escape_string($slite3, $_POST['message'])."', '".sqlite3_escape_string($slite3, $_SERVER['REMOTE_ADDR'])."');"); 
echo "{success:message};"; }
if(!isset($_SESSION['userid'])||!isset($_SESSION['username'])||!isset($_POST['message'])) { 
	echo "{error:message};"; } }
if($_GET['act']=="view") { 
if(isset($_SESSION['userid'])&&isset($_SESSION['username'])) { 
if(!isset($_GET['tsstart'])&&!isset($_GET['tsend'])) {
$getmessage = sqlite3_query($slite3, "SELECT * FROM \"".$sqlprefix."messages\" WHERE userid<>".$_SESSION['userid'].";"); }
if(isset($_GET['tsstart'])) {
$getmessage = sqlite3_query($slite3, "SELECT * FROM \"".$sqlprefix."messages\" WHERE timestamp>=".sqlite3_escape_string($slite3, $_GET['tsstart'])." AND userid<>".$_SESSION['userid'].";"); }
if(isset($_GET['tsend'])) {
$getmessage = sqlite3_query($slite3, "SELECT * FROM \"".$sqlprefix."messages\" WHERE timestamp<=".sqlite3_escape_string($slite3, $_GET['tsend'] - 3)." AND userid<>".$_SESSION['userid'].";"); }
if(isset($_GET['tsstart'])&&isset($_GET['tsend'])) {
$getmessage = sqlite3_query($slite3, "SELECT * FROM \"".$sqlprefix."messages\" WHERE timestamp>=".sqlite3_escape_string($slite3, $_GET['tsstart'])." AND timestamp<=".sqlite3_escape_string($slite3, $_GET['tsend'] - 3)." AND userid<>".$_SESSION['userid'].";"); }
while ($messageinfo = sql_fetch_assoc($getmessage)) {
echo $messageinfo['timestamp'].", ".$messageinfo['userid'].", \"".$messageinfo['username']."\", \"".$messageinfo['message']."\";\n"; } }
if(!isset($_SESSION['userid'])||!isset($_SESSION['username'])) { echo "{error:message};"; } }
?>