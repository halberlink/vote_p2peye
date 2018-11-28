<?php  

include 'mysql.php';

$type = $_GET['type'];

if($type == 'begin'){

	$redis = new Redis();
	$redis->connect('127.0.0.1', 16379);

	$redis->set("poll_status",2);
	$status = $redis->get("poll_status");

	if($status == 2){

		$res = conntentMysql("select * from peoples where status ='0'");

		$res = current($res);

		$res = conntentMysql("UPDATE peoples SET status='1' where name = '".$res['name']."'");

		echo json_encode(array("code"=>"200","data"=>$res,"message"=>"success"));exit;
	}else{
		echo json_encode(array("code"=>"400","data"=>$status,"message"=>"felid"));exit;
	}
		
}

if($type == 'next'){

	//明天继续写

	$res_now = conntentMysql("select * from peoples where status ='1'");

	$res_now_name = $res_now[0]['name'];

	$res = conntentMysql("UPDATE peoples SET status='2' where name = '".$res_now_name."'");


	$res_will = conntentMysql("select * from peoples where status ='0'");

	$res_will = current($res_will);

	$res_will_name = $res_will['name'];

	$res = conntentMysql("UPDATE peoples SET status='1' where name = '".$res_will_name."'");

	if($res){
		$res = conntentMysql("select * from peoples where status ='1'");
	}

	echo json_encode(array("code"=>"200","data"=>$res,"message"=>"success"));exit;	

}

if($type == 'webs'){

	$redis = new Redis();
	$redis->connect('127.0.0.1', 16379);

	$status = $redis->get("poll_status");


	$res = conntentMysql("select * from peoples where status ='1'");

	

	if(empty($res) || $status == 1){
		echo json_encode(array("code"=>"4030","data"=>$res,"message"=>"活动未开始"));exit;

	}

	echo json_encode(array("code"=>"200","data"=>$res,"message"=>"success"));exit;	


}

?>