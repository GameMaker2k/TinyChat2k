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

    $FileInfo: api.php - Last Update: 12/25/2012 Ver. 1.0.0 - Author: cooldude2k $
*/

ob_start();
@ini_set("html_errors", false);
@ini_set("track_errors", false);
@ini_set("display_errors", false);
@ini_set("report_memleaks", false);
@ini_set("display_startup_errors", false);
if(!defined("E_DEPRECATED")) { define("E_DEPRECATED", 0); }
@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
@ini_set("date.timezone","UTC"); 
@ini_set("ignore_user_abort", 1);
@ini_set("url_rewriter.tags",""); 
@ini_set('zend.ze1_compatibility_mode', 0);
@ini_set("session.use_cookies", true);
@ini_set("session.use_only_cookies", true);
@ini_set("zlib.output_compression", false);
@ini_set("zlib.output_compression_level", -1);
if(function_exists("date_default_timezone_set")) { 
	@date_default_timezone_set("UTC"); }
header("Content-Type: text/plain; charset=UTF-8");
$chatproverinfo = array("TinyChat2k", 1, 0, 0, null);
$sqlite_busy_timeout = 2000;
if(!isset($_GET['room'])) { $_GET['room'] = ""; }
$_GET['room'] = preg_replace("/[^a-z0-9]/", "", strtolower($_GET['room']));
$roomname = $_GET['room'];
if($roomname=="") { echo "{error:room};"; exit(); }
if(!file_exists("./sessions/")) { mkdir("./sessions/"); }
if(!file_exists("./sessions/".$roomname."/")) { mkdir("./sessions/".$roomname."/"); }
session_save_path("./sessions/".$roomname."/");
session_name($roomname);
session_start();
$sqlprefix = $roomname."_";
if(!isset($_GET['act'])) { $_GET['act'] = "view"; }
require("./sqlite.php");
if($_GET['act']=="login") { 
$findmember = sqlite3_query($sqlite_tinychat, "SELECT COUNT(*) AS count FROM \"".$sqlprefix."members\" WHERE \"name\"='".sqlite3_escape_string($sqlite_tinychat, $_POST['username'])."';"); 
$nummember = sqlite3_fetch_assoc($findmember);
$numrows = $nummember['count'];
if($numrows<=0) { echo "{warning:newuser};"; exit(); }
if($numrows>0) {
$findmember = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."members\" WHERE \"name\"='".sqlite3_escape_string($sqlite_tinychat, $_POST['username'])."';"); 
$memberinfo = sqlite3_fetch_assoc($findmember); 
if($_POST['userpass']==$memberinfo['password']) {
$prenummsgs = sqlite3_query($sqlite_tinychat, "SELECT COUNT(*) AS count FROM \"".$sqlprefix."messages\" ORDER BY \"id\" DESC LIMIT 1;"); 
$nummsgsasoc = sqlite3_fetch_assoc($prenummsgs);
$nummsgs = $nummsgsasoc['count'];
if($nummsgs>=1) {
$getlastmsg = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."messages\" ORDER BY \"id\" DESC LIMIT 1;");
$getlastmsgid = sqlite3_fetch_assoc($getlastmsg); }
if($nummsgs<=0) { $getlastmsgid['id'] = "0"; }
sqlite3_query($sqlite_tinychat, "UPDATE \"".$sqlprefix."members\" SET \"lastactive\"='".sqlite3_escape_string($sqlite_tinychat, time())."', \"lastmessageid\"=".sqlite3_escape_string($sqlite_tinychat, $getlastmsgid['id'])." WHERE \"id\"=".$memberinfo['id'].";");
$_SESSION['userid'] = $memberinfo['id'];
$_SESSION['username'] = $memberinfo['name'];
echo "{success:loginuser};"; exit(); }
if($_POST['userpass']!=$memberinfo['password']) {
setcookie(session_name(), "", time() - 42000);
setcookie(session_id(), "", time() - 42000);
session_destroy();
echo "{error:loginuser};"; exit(); } } }
if($_GET['act']=="signup") {
$findmember = sqlite3_query($sqlite_tinychat, "SELECT COUNT(*) AS count FROM \"".$sqlprefix."members\" WHERE \"name\"='".sqlite3_escape_string($sqlite_tinychat, $_POST['username'])."';"); 
$nummember = sqlite3_fetch_assoc($findmember);
$numrows = $nummember['count'];
if($numrows<=0) {
$prenummsgs = sqlite3_query($sqlite_tinychat, "SELECT COUNT(*) AS count FROM \"".$sqlprefix."messages\" ORDER BY \"id\" DESC LIMIT 1;"); 
$nummsgsasoc = sqlite3_fetch_assoc($prenummsgs);
$nummsgs = $nummsgsasoc['count'];
if($nummsgs>=1) {
$getlastmsg = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."messages\" ORDER BY \"id\" DESC LIMIT 1;");
$getlastmsgid = sqlite3_fetch_assoc($getlastmsg); }
if($nummsgs<=0) { $getlastmsgid['id'] = "0"; }
sqlite3_query($sqlite_tinychat, "INSERT INTO \"".$sqlprefix."members\" (\"name\", \"password\", \"joined\", \"lastactive\", \"lastmessageid\", \"validated\", \"bantime\", \"admin\", \"ip\") VALUES ('".sqlite3_escape_string($sqlite_tinychat, $_POST['username'])."', '".sqlite3_escape_string($sqlite_tinychat, hash("sha512", $_POST['userpass']))."', '".sqlite3_escape_string($sqlite_tinychat, time())."', '".sqlite3_escape_string($sqlite_tinychat, time())."', ".sqlite3_escape_string($sqlite_tinychat, $getlastmsgid['id']).", 'yes', 0, 'no', '".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."');"); 
$usersid = sqlite3_last_insert_rowid($sqlite_tinychat); 
$findmember = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."members\" WHERE \"name\"='".sqlite3_escape_string($sqlite_tinychat, $_POST['username'])."';"); 
$memberinfo = sqlite3_fetch_assoc($findmember); 
echo "{success:loginuser};"; exit(); }
if($numrows>0) { echo "{error:loginuser};"; exit(); } }
if($_GET['act']=="logout") {
if(file_exists("./sessions/".$roomname."/sess_".session_id())) {
unlink("./sessions/".$roomname."/sess_".session_id()); } 
setcookie(session_name(), "", time() - 42000);
setcookie(session_id(), "", time() - 42000);
session_destroy();
$_SESSION = array();
echo "{success:logoutuser};";
exit(); }
if($_GET['act']=="message") { 
if(isset($_SESSION['userid'])&&isset($_SESSION['username'])&&isset($_POST['message'])) {
sqlite3_query($sqlite_tinychat, "INSERT INTO \"".$sqlprefix."messages\" (\"userid\", \"username\", \"timestamp\", \"message\", \"ip\") VALUES ('".sqlite3_escape_string($sqlite_tinychat, $_SESSION['userid'])."', '".sqlite3_escape_string($sqlite_tinychat, $_SESSION['username'])."', '".sqlite3_escape_string($sqlite_tinychat, time())."', '".sqlite3_escape_string($sqlite_tinychat, $_POST['message'])."', '".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."');"); 
echo "{success:message};"; exit(); }
if(!isset($_SESSION['userid'])||!isset($_SESSION['username'])||!isset($_POST['message'])) { 
	echo "{error:message};"; exit(); } }
if($_GET['act']=="view") { 
if(isset($_SESSION['userid'])&&isset($_SESSION['username'])) { 
$findmember = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."members\" WHERE \"id\"=".sqlite3_escape_string($sqlite_tinychat, $_SESSION['userid']).";"); 
$memberinfo = sqlite3_fetch_assoc($findmember); 
$getmessage = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."messages\" WHERE \"id\">".sqlite3_escape_string($sqlite_tinychat, $memberinfo['lastmessageid'])." AND userid<>".$_SESSION['userid'].";");
$cmessageid = null;
while ($messageinfo = sqlite3_fetch_assoc($getmessage)) {
$cmessageid = $messageinfo['id'];
echo $messageinfo['timestamp'].", ".$messageinfo['userid'].", \"".$messageinfo['username']."\", \"".base64_encode($messageinfo['message'])."\";\n"; } 
if(isset($cmessageid)&&$cmessageid!=null) {
sqlite3_query($sqlite_tinychat, "UPDATE \"".$sqlprefix."members\" SET \"lastactive\"='".sqlite3_escape_string($sqlite_tinychat, time())."', \"lastmessageid\"=".sqlite3_escape_string($sqlite_tinychat, $cmessageid)." WHERE \"id\"=".$_SESSION['userid'].";"); } }
if(!isset($_SESSION['userid'])||!isset($_SESSION['username'])) { echo "{error:message};"; exit(); } }
sqlite3_close($sqlite_tinychat);
?>