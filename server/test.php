<?php  
function queryMysql($query){

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
	$c = mysql_query("set names 'utf8'");//编码转化

	
	$select_db = mysql_select_db($mysql_conf['db']);
	if (!$select_db) {
	    die("could not connect to the db:\n" .  mysql_error());
	}

	$sql = $query;
	$res = mysql_query($sql);
	if (!$res) {
	    die("could get the res:\n" . mysql_error());
	}

	$isInsert = strstr($query, 'INSERT');

	$isUpdate = strstr($query, 'UPDATE');

	if(!$isInsert && !$isUpdate && $res){

		while ($row = mysql_fetch_assoc($res)) {
		    $arr[]=$row;
		}
	}else{
		$arr = $res;
	}

	if(!isset($arr)){
		$arr = array("0"=>"");
	}

	mysql_close($mysql_conn); 


	return $arr;
}



$res = queryMysql("select * from peoples where status ='0'");


$randNumber = rand(0,count($res) -1);



$res = $res[$randNumber];

var_dump($res);

?>