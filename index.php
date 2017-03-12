<?php

require("ez_sql/ez_sql_core.php");
require("ez_sql/ez_sql_pdo.php");
require("ez_sql_pdo_cluster.php");

$db = new ezSQL_pdo_cluster(array(
	array(
		"dsn" => "mysql:host=127.0.0.1;dbname=test",
		"user" => "root",
		"pwd" => "root"
	),
	array(
		"dsn" => "mysql:host=127.0.0.1;dbname=test2",
		"user" => "root",
		"pwd" => "root"
	),
	array(
		"dsn" => "mysql:host=127.0.0.1;dbname=test3",
		"user" => "root",
		"pwd" => "root"
	),
	array(
		"dsn" => "mysql:host=127.0.0.1;dbname=test4",
		"user" => "root",
		"pwd" => "root"
	)
));
$db->query("SET NAMES utf8");	

//$s = $db->query("insert into t_user (name, pwd) values('aaa', 'AAA');");
$s = $db->get_results("select * from t_user", ARRAY_A);

var_dump($s);
