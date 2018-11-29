<?php  
include 'mysql.php';

$res = conntentMysql("select * from peoples");

foreach($res as $key => $item){
    switch ($item['job']) {
        case 1:
            $res[$key]['job'] = '前端';
            break;
        case 2:
            $res[$key]['job'] = 'PHP';
            break;
        case 3:
            $res[$key]['job'] = 'JAVA';
            break;
        case 4:
            $res[$key]['job'] = 'IOS';
            break;
        case 5:
            $res[$key]['job'] = 'Android';
            break;
        case 6:
            $res[$key]['job'] = '测试';
            break;
        case 7:
            $res[$key]['job'] = '运维';
            break;
        default:
            break;
    }
}


echo json_encode(array("code"=>"200","data"=>$res,"message"=>"success"));exit;	
?>