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

    $FileInfo: api.php - Last Update: 12/31/2012 Ver. 0.0.1 - Author: cooldude2k $
*/

@ob_start("ob_gzhandler");
@ini_set("html_errors", false);
@ini_set("track_errors", false);
@ini_set("display_errors", false);
@ini_set("report_memleaks", false);
@ini_set("display_startup_errors", false);
@ini_set("docref_ext", "");
@ini_set("docref_root", "http://php.net/");
if(!defined("E_DEPRECATED")) { define("E_DEPRECATED", 0); }
@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
@ini_set("date.timezone","UTC"); 
@ini_set("ignore_user_abort", 1);
@set_time_limit(0); @ignore_user_abort(true);
@ini_set("url_rewriter.tags",""); 
@ini_set('zend.ze1_compatibility_mode', 0);
@ini_set("session.use_cookies", true);
@ini_set("session.use_only_cookies", true);
//@ini_set("zlib.output_compression", false);
@ini_set("zlib.output_compression_level", -1);
@ini_set("session.hash_function", "sha512");
@ini_set("session.hash_bits_per_character", "6");
if(substr(php_uname("s"), 0, 7)!="Windows"&&
	file_exists("/dev/urandom")==true) {
	@ini_set("session.entropy_file", "/dev/urandom");
	//@ini_set("session.entropy_length", "512");
	@ini_set("session.entropy_length", "1024"); }
if(substr(php_uname("s"), 0, 7)!="Windows"&&
	file_exists("/dev/urandom")==false&&
	file_exists("/dev/random")==true) {
	@ini_set("session.entropy_file", "/dev/random");
	//@ini_set("session.entropy_length", "512");
	@ini_set("session.entropy_length", "1024"); }
if(function_exists("date_default_timezone_set")) { 
	@date_default_timezone_set("UTC"); }
$welcomemsg = array();
$welcomemsg['userid'] = 0;
$welcomemsg['username'] = "message";
$welcomemsg['message'] = "Hello %{UserName}m welcome to chat room: %{ChatRoom}m";
$hellobyemsg = array();
$hellobyemsg['hello'] = "%{UserName}m has joined chat room %{ChatRoom}m";
$hellobyemsg['signup'] = "%{UserName}m has signed up and joined chat room: %{ChatRoom}m";
$hellobyemsg['goodbye'] = "%{UserName}m has left chat room: %{ChatRoom}m";
$sesssavedir = "tcsess";
$databasedir = "tcdata";
$website_url = null;
if($website_url==null||$website_url=="") {
$website_url = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/"; }
$website_url_info = parse_url($website_url);
header("Content-Type: text/plain; charset=UTF-8");
header("Content-Language: en");
header("Vary: Accept-Encoding");
$chatproverinfo = array("TinyChat2k", 0, 0, 1, null);
$chatprofullname = "{program:\"".base64_encode($chatproverinfo[0])."\",major:".$chatproverinfo[1].",minor:".$chatproverinfo[2].",release:".$chatproverinfo[3]."};";
$sqlite_busy_timeout = 2000;
$tinyactcheck = array("view", "check", "version", "welcome", "status", "login", "signup", "logout", "message");
if(!isset($_GET['act'])) { $_GET['act'] = "view"; }
if(!in_array($_GET['act'], $tinyactcheck)) { $_GET['type'] = "welcome"; }
if($_GET['act']=="check") { echo "{success:tinychat};"; exit(); }
if(!isset($_GET['room'])) { $_GET['room'] = ""; }
$_GET['room'] = preg_replace("/[^a-z0-9]/", "", strtolower($_GET['room']));
$roomname = $_GET['room'];
if($roomname=="") { echo "{error:room};"; exit(); }
if(!file_exists("./.htaccess")) { 
	$htafile_sqlite_fp = fopen("./.htaccess", "w+");
	fwrite($htafile_sqlite_fp, "<Files \"sqlite.php\">\n   Order Deny,Allow\n   Deny from all\n   Satisfy All\n</Files>\n\n<Files \"*.sdb\">\n   Order Deny,Allow\n   Deny from all\n   Satisfy All\n</Files>\n");
	fclose($htafile_sqlite_fp);
	chmod("./.htaccess", 0766); }
if(!file_exists("./".$databasedir."/")) { 
	mkdir("./".$databasedir."/", 0766); 
	chmod("./".$databasedir."/", 0766); }
if(!file_exists("./".$databasedir."/.htaccess")) { 
	$htafile_sqlite_fp = fopen("./".$databasedir."/.htaccess", "w+");
	fwrite($htafile_sqlite_fp, "Order Deny,Allow\nDeny from all\n");
	fclose($htafile_sqlite_fp);
	chmod("./".$databasedir."/.htaccess", 0766); }
if(!file_exists("./".$sesssavedir."/")) { 
	mkdir("./".$sesssavedir."/", 0766); 
	chmod("./".$sesssavedir."/", 0766); }
if(!file_exists("./".$sesssavedir."/.htaccess")) { 
	$htafile_sqlite_fp = fopen("./".$sesssavedir."/.htaccess", "w+");
	fwrite($htafile_sqlite_fp, "Order Deny,Allow\nDeny from all\n");
	fclose($htafile_sqlite_fp);
	chmod("./".$sesssavedir."/.htaccess", 0766); }
if(!file_exists("./".$sesssavedir."/".$roomname."/")) { 
	mkdir("./".$sesssavedir."/".$roomname."/", 0766);
	chmod("./".$sesssavedir."/".$roomname."/", 0766); }
if(!file_exists("./".$sesssavedir."/".$roomname."/.htaccess")) { 
	$htafile_sqlite_fp = fopen("./".$sesssavedir."/".$roomname."/.htaccess", "w+");
	fwrite($htafile_sqlite_fp, "Order Deny,Allow\nDeny from all\n");
	fclose($htafile_sqlite_fp);
	chmod("./".$sesssavedir."/".$roomname."/.htaccess", 0766); }
session_cache_limiter("private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0");
header("Cache-Control: private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0");
header("Pragma: private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0");
header("Date: ".gmdate("D, d M Y H:i:s")." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");
session_save_path("./".$sesssavedir."/".$roomname."/");
session_set_cookie_params(0, $website_url_info['path'], $website_url_info['host']);
session_name($roomname);
session_start();
$sqlprefix = $roomname."_";
require("./sqlite.php");
if($_GET['act']=="version") { echo $chatprofullname; exit(); }
if($_GET['act']=="welcome"&&isset($_SESSION['userid'])&&isset($_SESSION['username'])&&isset($_GET['room'])) { 
	$_GET['type'] = "status"; $_GET['type'] = "welcome"; }
if($_GET['act']=="status"&&isset($_SESSION['userid'])&&isset($_SESSION['username'])&&isset($_GET['room'])) {
$tinystatuscheck = array("welcome", "hello", "signup", "goodbye");
if(!isset($_GET['type'])) { $_GET['type'] = "welcome"; }
if(!in_array($_GET['type'], $tinystatuscheck)) { $_GET['type'] = "welcome"; }
if($_GET['type']=="welcome") { 
echo "{timestamp:".get_microtime().",userid:".$welcomemsg['userid'].",username:\"".base64_encode($welcomemsg['username'])."\",message:\"".base64_encode(convert_message($welcomemsg['message']))."\"};"; exit(); }
if($_GET['type']=="hello") { 
sqlite3_query($sqlite_tinychat, "INSERT INTO \"".$sqlprefix."messages\" (\"userid\", \"username\", \"timestamp\", \"message\", \"ip\") VALUES ('".sqlite3_escape_string($sqlite_tinychat, $welcomemsg['userid'])."', '".sqlite3_escape_string($sqlite_tinychat, $welcomemsg['username'])."', '".sqlite3_escape_string($sqlite_tinychat, get_microtime())."', '".sqlite3_escape_string($sqlite_tinychat, convert_message($hellobyemsg['hello']))."', '".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."');"); 
sqlite3_query($sqlite_tinychat, "UPDATE \"".$sqlprefix."members\" SET \"lastactive\"='".sqlite3_escape_string($sqlite_tinychat, time())."', \"lastmessageid\"=".sqlite3_escape_string($sqlite_tinychat, sqlite3_last_insert_rowid($sqlite_tinychat)).", \"ip\"='".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."' WHERE \"id\"=".sqlite3_escape_string($sqlite_tinychat, $_SESSION['userid']).";"); exit(); }
if($_GET['type']=="signup") { 
sqlite3_query($sqlite_tinychat, "INSERT INTO \"".$sqlprefix."messages\" (\"userid\", \"username\", \"timestamp\", \"message\", \"ip\") VALUES ('".sqlite3_escape_string($sqlite_tinychat, $welcomemsg['userid'])."', '".sqlite3_escape_string($sqlite_tinychat, $welcomemsg['username'])."', '".sqlite3_escape_string($sqlite_tinychat, get_microtime())."', '".sqlite3_escape_string($sqlite_tinychat, convert_message($hellobyemsg['signup']))."', '".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."');"); 
sqlite3_query($sqlite_tinychat, "UPDATE \"".$sqlprefix."members\" SET \"lastactive\"='".sqlite3_escape_string($sqlite_tinychat, time())."', \"lastmessageid\"=".sqlite3_escape_string($sqlite_tinychat, sqlite3_last_insert_rowid($sqlite_tinychat)).", \"ip\"='".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."' WHERE \"id\"=".sqlite3_escape_string($sqlite_tinychat, $_SESSION['userid']).";"); exit(); }
if($_GET['type']=="goodbye") { 
sqlite3_query($sqlite_tinychat, "INSERT INTO \"".$sqlprefix."messages\" (\"userid\", \"username\", \"timestamp\", \"message\", \"ip\") VALUES ('".sqlite3_escape_string($sqlite_tinychat, $welcomemsg['userid'])."', '".sqlite3_escape_string($sqlite_tinychat, $welcomemsg['username'])."', '".sqlite3_escape_string($sqlite_tinychat, get_microtime())."', '".sqlite3_escape_string($sqlite_tinychat, convert_message($hellobyemsg['goodbye']))."', '".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."');"); 
sqlite3_query($sqlite_tinychat, "UPDATE \"".$sqlprefix."members\" SET \"lastactive\"='".sqlite3_escape_string($sqlite_tinychat, time())."', \"lastmessageid\"=".sqlite3_escape_string($sqlite_tinychat, sqlite3_last_insert_rowid($sqlite_tinychat)).", \"ip\"='".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."' WHERE \"id\"=".sqlite3_escape_string($sqlite_tinychat, $_SESSION['userid']).";"); exit(); } }
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
sqlite3_query($sqlite_tinychat, "UPDATE \"".$sqlprefix."members\" SET \"lastactive\"='".sqlite3_escape_string($sqlite_tinychat, time())."', \"lastmessageid\"=".sqlite3_escape_string($sqlite_tinychat, $getlastmsgid['id']).", \"ip\"='".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."' WHERE \"id\"=".sqlite3_escape_string($sqlite_tinychat, $memberinfo['id']).";");
$_SESSION['userid'] = $memberinfo['id'];
$_SESSION['username'] = $memberinfo['name'];
echo "{success:loginuser};"; exit(); }
if($_POST['userpass']!=$memberinfo['password']) {
$_SESSION = array();
setcookie(session_name(), "", time() - 42000, $website_url_info['path'], $website_url_info['host']);
setcookie(session_id(), "", time() - 42000, $website_url_info['path'], $website_url_info['host']);
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
sqlite3_query($sqlite_tinychat, "INSERT INTO \"".$sqlprefix."members\" (\"name\", \"password\", \"joined\", \"lastactive\", \"lastmessageid\", \"validated\", \"bantime\", \"admin\", \"ip\") VALUES ('".sqlite3_escape_string($sqlite_tinychat, $_POST['username'])."', '".sqlite3_escape_string($sqlite_tinychat, $_POST['userpass'])."', '".sqlite3_escape_string($sqlite_tinychat, time())."', '".sqlite3_escape_string($sqlite_tinychat, time())."', ".sqlite3_escape_string($sqlite_tinychat, $getlastmsgid['id']).", 'yes', 0, 'no', '".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."');"); 
$usersid = sqlite3_last_insert_rowid($sqlite_tinychat); 
$findmember = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."members\" WHERE \"name\"='".sqlite3_escape_string($sqlite_tinychat, $_POST['username'])."';"); 
$memberinfo = sqlite3_fetch_assoc($findmember); 
echo "{success:signupuser};"; exit(); }
if($numrows>0) { echo "{error:signupuser};"; exit(); } }
if($_GET['act']=="logout") {
if(!isset($_SESSION['userid'])||!isset($_SESSION['username'])) { 
	echo "{error:logoutuser};"; exit(); }
if(isset($_SESSION['userid'])&&isset($_SESSION['username'])) { 
if(file_exists("./sessions/".$roomname."/sess_".session_id())) {
unlink("./sessions/".$roomname."/sess_".session_id()); } 
$_SESSION = array();
setcookie(session_name(), "", time() - 42000, $website_url_info['path'], $website_url_info['host']);
setcookie(session_id(), "", time() - 42000, $website_url_info['path'], $website_url_info['host']);
session_destroy();
echo "{success:logoutuser};";
exit(); } }
if($_GET['act']=="message") { 
if(!isset($_SESSION['userid'])||!isset($_SESSION['username'])||
	!isset($_POST['message'])||!isset($_GET['room'])) { 
	echo "{error:message};"; exit(); }
if(isset($_SESSION['userid'])&&isset($_SESSION['username'])&&isset($_POST['message'])) {
sqlite3_query($sqlite_tinychat, "INSERT INTO \"".$sqlprefix."messages\" (\"userid\", \"username\", \"timestamp\", \"message\", \"ip\") VALUES ('".sqlite3_escape_string($sqlite_tinychat, $_SESSION['userid'])."', '".sqlite3_escape_string($sqlite_tinychat, $_SESSION['username'])."', '".sqlite3_escape_string($sqlite_tinychat, get_microtime())."', '".sqlite3_escape_string($sqlite_tinychat, $_POST['message'])."', '".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."');"); 
echo "{success:message};"; exit(); } }
if($_GET['act']=="view") { 
if(!isset($_SESSION['userid'])||!isset($_SESSION['username'])||!isset($_GET['room'])) { 
	echo "{error:message};"; exit(); }
if(isset($_SESSION['userid'])&&isset($_SESSION['username'])) { 
$findmember = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."members\" WHERE \"id\"=".sqlite3_escape_string($sqlite_tinychat, $_SESSION['userid']).";"); 
$memberinfo = sqlite3_fetch_assoc($findmember); 
$getmessage = sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."messages\" WHERE \"id\">".sqlite3_escape_string($sqlite_tinychat, $memberinfo['lastmessageid'])." AND userid<>".$_SESSION['userid']." ORDER BY \"timestamp\" ASC;");
$cmessageid = null;
while ($messageinfo = sqlite3_fetch_assoc($getmessage)) {
$cmessageid = $messageinfo['id'];
echo "{timestamp:".$messageinfo['timestamp'].",userid:".$messageinfo['userid'].",username:\"".base64_encode($messageinfo['username'])."\",message:\"".base64_encode($messageinfo['message'])."\"};"; } 
if(isset($cmessageid)&&$cmessageid!=null) {
sqlite3_query($sqlite_tinychat, "UPDATE \"".$sqlprefix."members\" SET \"lastactive\"='".sqlite3_escape_string($sqlite_tinychat, time())."', \"lastmessageid\"=".sqlite3_escape_string($sqlite_tinychat, $cmessageid).", \"ip\"='".sqlite3_escape_string($sqlite_tinychat, $_SERVER['REMOTE_ADDR'])."' WHERE \"id\"=".$_SESSION['userid'].";"); } } }
sqlite3_close($sqlite_tinychat);
?>