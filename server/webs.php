<?php  


$type = $_GET['type'];

if($type == 'begin'){

	$redis = new Redis();
	$redis->connect('127.0.0.1', 16379);

	$redis->set("poll_status",2);
	$status = $redis->get("poll_status");

	if($status == 2){
		echo json_encode(array("code"=>"200","data"=>$status,"message"=>"success"));exit;
	}else{
		echo json_encode(array("code"=>"400","data"=>$status,"message"=>"felid"));exit;
	}
		
}


?>