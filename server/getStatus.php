<?php  
include 'mysql.php';

$redis = new Redis();
$redis->connect('127.0.0.1', 16379);

$status = $redis->get("poll_status");

if(!$status){
	$redis->set("poll_status",1);
	$status = $redis->get("poll_status");
}


echo json_encode(array("code"=>"200","data"=>$status,"message"=>"success"));exit;	
?>