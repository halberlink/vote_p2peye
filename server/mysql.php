<?php

function conntentMysql($query,$sqlType){

	$mysql_conf = array(
	    'host'    => '192.168.2.10:3357', 
	    'db'      => 'andorcba', 
	    'db_user' => 'wdty', 
	    'db_pwd'  => '123456', 
	);

	$mysql_conn = @mysql_connect($mysql_conf['host'], $mysql_conf['db_user'], $mysql_conf['db_pwd']);

	if (!$mysql_conn) {
	    die("could not connect to the database:\n" . mysql_error());//诊断连接错误
	}
	mysql_query("set names 'utf8'");//编码转化
	$select_db = mysql_select_db($mysql_conf['db']);
	if (!$select_db) {
	    die("could not connect to the db:\n" .  mysql_error());
	}
	$sql = $query;
	$res = mysql_query($sql);
	if (!$res) {
	    die("could get the res:\n" . mysql_error());
	}
	if($sqlType != 'insert'){
		while ($row = mysql_fetch_assoc($res)) {
		    $arr[]=$row;
		}
	}else{
		$arr = $res;
	}
	mysql_close($mysql_conn); 

	return $arr;


}
?>