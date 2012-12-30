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

    $FileInfo: sqlite.php - Last Update: 12/30/2012 Ver. 0.0.1 - Author: cooldude2k $
*/

$ScriptFileName = basename($_SERVER['SCRIPT_NAME']);
if ($ScriptFileName=="sqlite.php"||$ScriptFileName=="/sqlite.php") {
	ob_start();
	header("Content-Type: text/plain; charset=UTF-8");
	require("./index.php");
	echo "{error:exit};";
	exit(); }

$sqlite_version = 3;
if(extension_loaded("sqlite3")&&$sqlite_version==3) {
function sqlite3_open($filename, $mode = 0666) {
   global $site_encryption_key;
   if($site_encryption_key===null) {
   $handle = new SQLite3($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE); }
   if($site_encryption_key!==null) {
   $handle = new SQLite3($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $site_encryption_key); }
   return $handle; }
function sqlite3_close($dbhandle) {
   $dbhandle->close();
   return true; }
function sqlite3_busy_timeout($dbhandle, $milliseconds) {
   $dbhandle->busyTimeout($milliseconds);
   return true; }
function sqlite3_escape_string($dbhandle, $string) {
   $string = $dbhandle->escapeString($string);
   return $string; }
function sqlite3_query($dbhandle, $query) {
   $results = $dbhandle->query($query);
   return $results; }
function sqlite3_fetch_array($result, $result_type=SQLITE3_BOTH) {
	$row = $result->fetchArray($result_type);
	return $row; }
function sqlite3_fetch_assoc($result) {
	$row = $result->fetchArray(SQLITE3_ASSOC);
	return $row; }
function sqlite3_last_insert_rowid($dbhandle) {
	$rowid = $dbhandle->lastInsertRowID();
	return $rowid; }
function sqlite3_libversion($dbhandle) {
	$dbversion = $dbhandle->version();
	return $dbversion['versionString']; } }
if(extension_loaded("sqlite")&&$sqlite_version==2) {
function sqlite3_open($filename, $mode = 0666) {
   $handle = sqlite_open($filename, $mode);
   return $handle; }
function sqlite3_close($dbhandle) {
   $dbhandle = sqlite_close($dbhandle);
   return true; }
function sqlite3_busy_timeout($dbhandle, $milliseconds) {
   sqlite_escape_string($dbhandle, $milliseconds);
   return true; }
function sqlite3_escape_string($dbhandle, $string) {
   $string = sqlite_escape_string($string);
   return $string; }
function sqlite3_query($dbhandle, $query) {
   $results = sqlite_query($dbhandle, $query);
   return $results; }
function sqlite3_fetch_array($result, $result_type=SQLITE_BOTH) {
	$row = sqlite_fetch_array($result, $result_type=SQLITE_BOTH);
	return $row; }
function sqlite3_fetch_assoc($result) {
	$row = sqlite_fetch_array($result, SQLITE_ASSOC);
	return $row; }
function sqlite3_last_insert_rowid($dbhandle) {
	$rowid = sqlite_last_insert_rowid($dbhandle);
	return $rowid; }
function sqlite3_libversion($dbhandle) {
	$dbversion = sqlite_libversion();
	return $dbversion; } }
function get_microtime() {
	return array_sum(explode(" ", microtime())); }
function log_fix_quotes($logtxt) {
	$logtxt = str_replace("\"", "\\\"", $logtxt);
	$logtxt = str_replace("'", "", $logtxt);
	return $logtxt; }
function get_server_values($matches) {
	$return_text = "-";
	if(isset($_SERVER[$matches[1]])) { $return_text = $_SERVER[$matches[1]]; }
	if(!isset($_SERVER[$matches[1]])) { $return_text = "-"; }
	return $return_text; }
function get_cookie_values($matches) {
	$return_text = null;
	if(isset($_COOKIE[$matches[1]])) { $return_text = $_COOKIE[$matches[1]]; }
	if(!isset($_COOKIE[$matches[1]])) { $return_text = null; }
	return $return_text; }
function get_env_values($matches) {
	$return_text = getenv($matches[1]);
	if(!isset($return_text)) { $return_text = "-"; }
	return $return_text; }
function get_setting_values($matches) {
	global $Settings;
	$return_text = null;
	$matches[1] = str_replace("sqlpass", "sqluser", $matches[1]);
	if(isset($Settings[$matches[1]])) { $return_text = $Settings[$matches[1]]; }
	if(!isset($Settings[$matches[1]])) { $return_text = null; }
	return $return_text; }
function log_fix_get_server_values($matches) {
	return log_fix_quotes(get_server_values($matches)); }
function log_fix_get_cookie_values($matches) {
	return log_fix_quotes(get_cookie_values($matches)); }
function log_fix_get_env_values($matches) {
	return log_fix_quotes(get_env_values($matches)); }
function log_fix_get_setting_values($matches) {
	return log_fix_quotes(get_setting_values($matches)); }
function get_time($matches) {
	return date(convert_strftime($matches[1])); }
function convert_strftime($strftime) {
$strftime = str_replace("%%", "{percent\}p", $strftime);
$strftime = str_replace("%a", "D", $strftime);
$strftime = str_replace("%A", "l", $strftime);
$strftime = str_replace("%d", "d", $strftime);
$strftime = str_replace("%e", "j", $strftime);
$strftime = str_replace("%j", "z", $strftime);
$strftime = str_replace("%u", "w", $strftime);
$strftime = str_replace("%w", "w", $strftime);
$strftime = str_replace("%U", "W", $strftime);
$strftime = str_replace("%V", "W", $strftime);
$strftime = str_replace("%W", "W", $strftime);
$strftime = str_replace("%b", "M", $strftime);
$strftime = str_replace("%B", "F", $strftime);
$strftime = str_replace("%h", "M", $strftime);
$strftime = str_replace("%m", "m", $strftime);
$strftime = str_replace("%g", "y", $strftime);
$strftime = str_replace("%G", "Y", $strftime);
$strftime = str_replace("%y", "y", $strftime);
$strftime = str_replace("%Y", "Y", $strftime);
$strftime = str_replace("%H", "H", $strftime);
$strftime = str_replace("%I", "h", $strftime);
$strftime = str_replace("%l", "g", $strftime);
$strftime = str_replace("%M", "i", $strftime);
$strftime = str_replace("%p", "A", $strftime);
$strftime = str_replace("%P", "a", $strftime);
$strftime = str_replace("%r", "h:i:s A", $strftime);
$strftime = str_replace("%R", "H:i", $strftime);
$strftime = str_replace("%S", "s", $strftime);
$strftime = str_replace("%T", "H:i:s", $strftime);
$strftime = str_replace("%X", "H:i:s", $strftime);
$strftime = str_replace("%z", "O", $strftime);
$strftime = str_replace("%Z", "O", $strftime);
$strftime = str_replace("%c", "D M j H:i:s Y", $strftime);
$strftime = str_replace("%D", "m/d/y", $strftime);
$strftime = str_replace("%F", "Y-m-d", $strftime);
$strftime = str_replace("%x", "m/d/y", $strftime);
$strftime = str_replace("%n", "\n", $strftime);
$strftime = str_replace("%t", "\t", $strftime);
$strftime = preg_replace("/\{percent\}p/s", "%", $strftime);
return $strftime; }
if($_GET['act']=="login"&&(!isset($_POST['username'])||!isset($_POST['userpass']))) { 
	echo "{error:loginuser};"; exit(); }
if($_GET['act']=="signup"&&(!isset($_POST['username'])||!isset($_POST['userpass']))) { 
	echo "{error:loginuser};"; exit(); }
if($_GET['act']=="message"&&(!isset($_SESSION['userid'])||!isset($_SESSION['username'])||!isset($roomname))) {
	echo "{error:message};"; exit(); }
if($_GET['act']=="welcome"&&(!isset($_SESSION['userid'])||!isset($_SESSION['username'])||!isset($roomname))) {
	echo "{error:welcome};"; exit(); }
if($_GET['act']=="view"&&(!isset($_SESSION['userid'])||!isset($_SESSION['username'])||!isset($roomname))) { 
	echo "{error:room};"; exit(); }
if(!file_exists("./".$databasedir."/".$roomname.".sdb")) { 
	$sqlite_fp = fopen("./".$databasedir."/".$roomname.".sdb", "w+");
	fwrite($sqlite_fp, null);
	fclose($sqlite_fp);
	chmod("./".$databasedir."/".$roomname.".sdb", 0766); }
$sqlite_tinychat = sqlite3_open("./".$databasedir."/".$roomname.".sdb", 0766);
if(!isset($sqlite_busy_timeout)) { $sqlite_busy_timeout = 2000; }
sqlite3_busy_timeout($sqlite_tinychat, $sqlite_busy_timeout);
$tablecheck1 = @sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."members\""); 
if($tablecheck1===false) {
sqlite3_query($sqlite_tinychat, "PRAGMA auto_vacuum = 1;");
sqlite3_query($sqlite_tinychat, "PRAGMA encoding = \"UTF-8\";");
$query = "CREATE TABLE \"".$sqlprefix."members\" (\n".
"  \"id\" INTEGER PRIMARY KEY NOT NULL,\n".
"  \"name\" VARCHAR(150) UNIQUE NOT NULL default '',\n".
"  \"password\" VARCHAR(250) NOT NULL default '',\n".
"  \"joined\" INTEGER NOT NULL default '0',\n".
"  \"lastactive\" INTEGER NOT NULL default '0',\n".
"  \"lastmessageid\" INTEGER NOT NULL default '0',\n".
"  \"validated\" VARCHAR(20) NOT NULL default '',\n".
"  \"bantime\" INTEGER NOT NULL default '0',\n".
"  \"admin\" VARCHAR(20) NOT NULL default '',\n".
"  \"ip\" VARCHAR(50) NOT NULL default ''\n".
");";
sqlite3_query($sqlite_tinychat, $query); }
$tablecheck2 = @sqlite3_query($sqlite_tinychat, "SELECT * FROM \"".$sqlprefix."messages\""); 
if($tablecheck2===false) {
sqlite3_query($sqlite_tinychat, "PRAGMA auto_vacuum = 1;");
sqlite3_query($sqlite_tinychat, "PRAGMA encoding = \"UTF-8\";");
$query = "CREATE TABLE \"".$sqlprefix."messages\" (\n".
"  \"id\" INTEGER PRIMARY KEY NOT NULL,\n".
"  \"userid\" INTEGER NOT NULL default '0',\n".
"  \"username\" VARCHAR(150) NOT NULL default '',\n".
"  \"timestamp\" FLOAT NOT NULL default '0',\n".
"  \"message\" TEXT NOT NULL,\n".
"  \"ip\" VARCHAR(50) NOT NULL default ''\n".
");";
sqlite3_query($sqlite_tinychat, $query); }
?>