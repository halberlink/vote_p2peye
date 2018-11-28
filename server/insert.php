<?php

include 'mysql.php';

$name = $_POST['name'];
$reason = $_POST['reason'];
$job = $_POST['job'];

if(!$name || $reason || $job){
	echo json_encode(array("code"=>"4008","data"=>'',"message"=>"缺少必要参数"));exit;	
}

$res = conntentMysql("select * from peoples where name ='".$name."'");

if(!empty($res)){
	echo json_encode(array("code"=>"4010","data"=>$res,"message"=>"已存在!"));exit;	
}

$res = conntentMysql("INSERT INTO peoples (name,job,reason) VALUES ('".$name."','".$job."','".$reason."') ",'insert');

if($res){
	$res = conntentMysql("select * from peoples where name ='".$name."'");
	echo json_encode(array("code"=>"200","data"=>$res,"message"=>"success"));exit;	
}

?>