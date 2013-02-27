<?php

$dbHost = "localhost";
$dbUser = "root";
$dbPwd = "root";
$dbName = "outbound";

if(!$DBLink = mysql_connect($dbHost, $dbUser, $dbPwd, true)) {
	die("Connect error.");
}
if(!mysql_select_db($dbName, $DBLink)) {
	die("Connect error.");
}

require(dirname(__FILE__) . "/../classes/generator/Generator.php");
require(dirname(__FILE__) . "/../classes/generator/BaseTableQueryClassGenerator.php");
require(dirname(__FILE__) . "/../classes/generator/TableClassQueryGenerator.php");
require(dirname(__FILE__) . "/../classes/generator/BaseTableClassGenerator.php");
require(dirname(__FILE__) . "/../classes/generator/TableClassGenerator.php");
require(dirname(__FILE__) . "/../classes/generator/InitTableClassesGenerator.php");
require(dirname(__FILE__) . "/../classes/generator/DataBaseParser.php");
require(dirname(__FILE__) . "/../classes/generator/Table.php");
require(dirname(__FILE__) . "/../classes/generator/Row.php");