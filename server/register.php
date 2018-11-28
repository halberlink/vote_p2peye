<?php  
include 'mysql.php';
$uname = $_POST['name'];
if(!$uname){
	echo json_encode(array("code"=>"4001","data"=>"","message"=>"名字不能为空!"));exit;
}
$res = conntentMysql("select * from users where uname ='".$uname."'");

if(!empty($res)){
	echo json_encode(array("code"=>"200","data"=>$res,"message"=>"success"));exit;	
}
$res = conntentMysql("INSERT INTO users (uname) VALUES ('".$uname."') ",'insert');

if($res){
	$res = conntentMysql("select * from users where uname ='".$uname."'");
	echo json_encode(array("code"=>"200","data"=>$res,"message"=>"success"));exit;	
}
?>