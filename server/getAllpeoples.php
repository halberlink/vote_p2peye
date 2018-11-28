<?php  
include 'mysql.php';

$res = conntentMysql("select * from peoples");

echo json_encode(array("code"=>"200","data"=>$res,"message"=>"success"));exit;	
?>