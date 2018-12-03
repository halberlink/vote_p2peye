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


	}

	public static function queryMysql($query){
		$sql = $query;
		$res = mysql_query($sql);
		if (!$res) {
		    die("could get the res:\n" . mysql_error());
		}

		$isInsert = strstr($query, 'INSERT');

		if(!$isInsert && $res){

			while ($row = mysql_fetch_assoc($res)) {
			    $arr[]=$row;
			}
		}else{
			$arr = $res;
		}

		if(!isset($arr)){
			$arr = array("0"=>"");
		}
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
			case 'vote' :
				$jsonCallback = self::_vote($data['data'],'vote');
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
		$res = self::queryMysql("UPDATE peoples SET status='2' where name = '".$res['name']."'");

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
			$res = self::queryMysql("select * from poll where from_id ='".$data['from_id']."' AND to_id = '".$data['to_id']."'");
			return self::formartCode($interfaceType,200,$res,"success!");	
		}else{
			return self::formartCode($interfaceType,4002,$res,"error!");	
		}


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

		$ing = self::queryMysql("select * from peoples where status ='2'");

		if($ing && !empty($ing)){
			$ing = $ing[0];
		}



		foreach ($data as $key => $item) {

			$uid = $data[$key]['name'];


			if($uid > 0){

				$ret = self::queryMysql("select * from users where id ='".$uid."'");

				if(!empty($ing)){
					$ing = self::queryMysql("select * from poll where from_id ='".$uid."'")[0];

					if($ing){
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

	    	foreach($res['ing'] as $key => $item){


				$res['ing'][$key]['job'] = self::resetJob($item['job']);

			}

			$res['history'] = self::queryMysql("select * from poll");

			foreach($res['history'] as $key => $item){

				$target = self::queryMysql("select * from peoples where id = '".$item['to_id']."'");
				$target = $target[0];

				$target['job'] = self::resetJob($target['job']);


				$res['history'][$key]['to_info'] = $target;

			}


	    }

		return self::formartCode($interfaceType,200,$res,"success!");

		
	}
	//注册接口
	public static function _register($data,$interfaceType){
		$uname = $data['name'];
		if(!$uname){
			return self::formartCode($interfaceType,4001,"","名字不能为空!");
		}
		$res = self::queryMysql("select * from users where uname ='".$uname."'");
		if(!empty($res)){
			return self::formartCode($interfaceType,200,$res[0],"success!");
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


/**
* 
*/




