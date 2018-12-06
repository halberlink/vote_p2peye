<?php  
/**
* 
*/
class module
{
	public static $redis;
	public static $onlinePeoples;
	
	function __construct()
	{
		self::$redis = new Redis();
		self::$redis->connect('127.0.0.1', 16379);

	}

	public static function resetonlinePeople(){

	}



	public static function queryMysql($query){

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


	public static function socket($data){
		$data = json_decode($data,true);
		if(!isset($data['data'])){
			$data['data']='';
		}

		switch ($data['interface']) {
			case 'info':
				$data['data'] = json_decode(self::$onlinePeoples,true);
				$jsonCallback = self::_info($data['data'],'info');
				break;
			case 'register':
				$jsonCallback = self::_register($data['data'],'register');
				break;
			case 'insert' :
				$jsonCallback = self::_insert($data['data'],'insert');
				break;
			case 'getPeoples' :
				$jsonCallback = self::_getPeoples($data['data'],'getPeoples');
				break;
			case 'start' :
				$jsonCallback = self::_start($data['data'],'start');
				break;
			case 'startVote' :
				$jsonCallback = self::_startVote($data['data'],'startVote');
				break;
			case 'vote' :
				$jsonCallback = self::_vote($data['data'],'vote');
				break;
			case 'next' :
				$jsonCallback = self::_next($data['data'],'next');
				break;
		}

		return $jsonCallback;
	}

	public static function formartCode($interfaceType,$code,$data,$message){
		return json_encode(array("interface"=> $interfaceType,"code"=>$code,"data"=>$data,"message"=>$message));
	}

	public static function resetJob($type){

		$_reset = '';

		switch ($type) {
	        case 1:
	            $_reset = '前端';
	            break;
	        case 2:
	            $_reset = 'PHP';
	            break;
	        case 3:
	            $_reset = 'JAVA';
	            break;
	        case 4:
	            $_reset = 'IOS';
	            break;
	        case 5:
	            $_reset = 'Android';
	            break;
	        case 6:
	            $_reset = '测试';
	            break;
	        case 7:
	            $_reset = '运维';
	            break;
	        default:
	        	$_reset = '前端';
	            break;
	    }

	    return $_reset;
	}

	public static function _start($data,$interfaceType)
	{
		self::$redis->set("poll_status","2");
		$status = self::$redis->get("poll_status");

		$res = self::queryMysql("select * from peoples where status ='0'");

		$res = current($res);

		$res = self::queryMysql("UPDATE peoples SET status='1' where name = '".$res['name']."'");

		if($res){
			return self::formartCode($interfaceType,200,array("status"=>$status),"success!");
		}else{
			return self::formartCode($interfaceType,4001,array("status"=>$status),"error!");
		}

	}

	public static function _startVote($data,$interfaceType){

		$res = self::queryMysql("select * from peoples where status ='1'")[0];
		if($res){
			$res = self::queryMysql("UPDATE peoples SET status='2' where name = '".$res['name']."'");
		}else{
			return self::formartCode($interfaceType,4002,'',"error!");
		}

		if($res){
			return self::formartCode($interfaceType,200,array("status"=>$res),"success!");
		}else{
			return self::formartCode($interfaceType,4001,array(),"error!");
		}
	}

	public static function _vote($data,$interfaceType){

		$res = self::queryMysql("select * from poll where from_id ='".$data['from_id']."' AND to_id = '".$data['to_id']."'")[0];

		if(!empty($res)){
			return self::formartCode($interfaceType,4001,'',"投过了");
		}

		$res = self::queryMysql("INSERT INTO poll (from_id,to_id,count) VALUES ('".$data['from_id']."','".$data['to_id']."','".$data['count']."') ");

		if($res){

			$voteCallbackData = array("info"=> json_decode(self::_info(json_decode(self::$onlinePeoples,true),'vote')),"from_id" => $data['from_id']);

			return self::formartCode($interfaceType,200,$voteCallbackData,"success!");
		}else{
			return self::formartCode($interfaceType,4002,$res,"error!");	
		}


	}

	public static function _next($data,$interfaceType){

		$res_now = self::queryMysql("select * from peoples where status ='2'");

		

		$res_now_name = $res_now[0]['name'];

		self::queryMysql("UPDATE peoples SET status='3' where name = '".$res_now_name."'");

		$res_will = self::queryMysql("select * from peoples where status ='0'");

		if(empty($res_will[0])){
			return self::formartCode($interfaceType,4300,'',"success!");			
		}


		$res_will = current($res_will);

		$res_will_name = $res_will['name'];

		self::queryMysql("UPDATE peoples SET status='1' where name = '".$res_will_name."'");

		$res = self::queryMysql("select * from peoples where status ='1'");

		return self::formartCode($interfaceType,200,$res,"success!");

	}

	public static function _info($data,$interfaceType){

		$status = self::$redis->get("poll_status");

		if(!$status){
			self::$redis->set("poll_status",1);
			$status = self::$redis->get("poll_status");
		}

		$res = array();

		$res['status'] = $status;

		$res['online'] = array();

		$ing = self::queryMysql("select * from peoples where status ='1' OR status='2'");

		if($ing && !empty($ing)){
			$ing = $ing[0];
		}

		foreach ($data as $key => $item) {

			$uid = $data[$key]['name'];

			if($uid > 0 && $uid != 30){

				$ret = self::queryMysql("select * from users where id ='".$uid."'");


				if(!empty($ing)){
					$voteed = self::queryMysql("select * from poll where from_id ='".$uid."' AND to_id = '".$ing['id']."'")[0];


					if($voteed){
						$ret[0]['vote_status'] = 1;
					}else{
						$ret[0]['vote_status'] = 0;
					}

				}

				array_push($res['online'],$ret[0]);

			}

			
	    }

	    if($res['status'] == 2){

	    	$res['ing'] = self::queryMysql("select * from peoples where status ='1' OR status='2'");

	    	if(!empty($res['ing'][0])){

		    	foreach($res['ing'] as $key => $item){


					$res['ing'][$key]['job'] = self::resetJob($item['job']);

				}
			}

			$res['history'] = self::queryMysql("select * from poll");

			$peoplesVoteNumber = array();

			if(count($res['history']) == 1 && empty($res['history'][0])){

				
			}else{
				foreach($res['history'] as $key => $item){

					$target = self::queryMysql("select * from peoples where id = '".$item['to_id']."'");

					$target = $target[0];


					$peoplesVoteNumber[$item['to_id']][] = $item['count'];
					if(!empty($target)){

						$target['job'] = self::resetJob($target['job']);
					
						$res['history'][$key]['to_info'] = $target;

					}

				}
			}

			$averageRank = array();

			$votePeoples = self::queryMysql("select * from peoples");

			$_votePeoples = array();

			foreach ($votePeoples as $key => $item) {
				$_votePeoples[$item['id']] = $item;
				$_votePeoples[$item['id']]['job'] = self::resetJob($_votePeoples[$item['id']]['job']);
			}

			foreach ($peoplesVoteNumber as $key => $item) {
				
				if(count($item)>=3){
					$count = array_sum($item);
					$max = max($item);
					$min = min($item);
					$len = count($item) - 2;

					$average = ( $count - $max - $min ) / $len;

					$average = number_format($average, 1);

				}else{

					$count = array_sum($item);
					$len = count($item);
					$average = $count / $len;	
					$average = number_format($average, 1);

				}
				$averageRank[$key][] =  array(
					'count' => $average, 
					'info' => $_votePeoples[$key],
				);

			}

			arsort($averageRank);

			$res['rank'] = $averageRank;

	    }

		return self::formartCode($interfaceType,200,$res,"success!");

		
	}
	//注册接口
	public static function _register($data,$interfaceType){
		$uname = $data['name'];
		if(!$uname){
			return self::formartCode($interfaceType,4001,"","名字不能为空!");
		}
		$res = self::queryMysql("select * from users where uname ='".$uname."'")[0];

		if(!empty($res)){
			return self::formartCode($interfaceType,200,$res,"success!");
		}
		$res = self::queryMysql("INSERT INTO users (uname) VALUES ('".$uname."') ");

		if($res){
			$res = self::queryMysql("select * from users where uname ='".$uname."'");
			$res = $res[0];
			return self::formartCode($interfaceType,200,$res,"success!");
		}

	}
	//录入候选人
	public static function _insert($data,$interfaceType){
		$name = $data['name'];
		$reason = $data['reason'];
		$job = $data['job'];
		$type = $data['type'];
		$uid = $data['uid'];

		if(!$uid || $uid != 30){
			return self::formartCode($interfaceType,4003,'',"未登录或非管理员!");	
		}

		if(!$name || !$reason || !$job || !$type){
			return self::formartCode($interfaceType,4008,'',"未登录或非管理员!");	
		}

		$res = self::queryMysql("select * from peoples where name ='".$name."'");

		if(!empty($res)){
			return self::formartCode($interfaceType,4010,$res,"已存在");	
		}

		$res = self::queryMysql("INSERT INTO peoples (name,job,reason,type) VALUES ('".$name."','".$job."','".$reason."','".$type."') ",'insert');

		if($res){
			$res = self::queryMysql("select * from peoples where name ='".$name."'");
			return self::formartCode($interfaceType,200,$res,"success");	
		}
	}

	//获取已录入候选人
	public static function _getPeoples($data,$interfaceType){
		$res = self::queryMysql("select * from peoples");

		
		foreach($res as $key => $item){

			$res[$key]['job'] = self::resetJob($item['job']);

		}
		return self::formartCode($interfaceType,200,$res,"success!");
	}

}




