<?php
    //test文件，压入数据的时候可以参考
    use \Workerman\Autoloader;
    require_once  '../../../Workerman/Autoloader.php';
	Autoloader::setRootPath("../../");
	//消息队列，向队列中rpush
	//require 'Redis.php';
	//use \Vendors\RedisQuene\Redisq;
    use Vendors\Redis\RedisModel;
    //RedisModel::set('webChat', 'aaa', 'bbbbgggggg');
//     RedisModel::hashSet('webChat', 'aaa', 'bbbbgggggg', 'dddd');
//      RedisModel::hashSet('webChat', 'USER_ONLINE_LIST', 'bbbbggg', 'dddd');
// 	$res = RedisModel::hashGet('webChat', 'aaa');
// 	var_dump($res);die;

	
	$res = RedisModel::hashDel('webChat', 'wangjx:unread:msg', 'bbb');
	var_dump($res);die;
	if(null === $res){
	    var_dump($res);
	}else{
	    var_dump(2222222);
	}
	die;
	
	$a = substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm"),0,4);
	$b = substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm"),0,4);
	$messageVal = array(
	    'fromuser'    => 'cuihb',
	    'touser'      => 'xieyx',
	    'message' => $a.$b,
	    'time'    => time(),
	);
	Redisq::rpush(array(
            'serverName'    => 'webChat', #服务器名，参照见Redisa的定义 ResysQ
            'key'      => 'chat:message:quene',  #队列名
            'value'    => serialize($messageVal),  #插入队列的数据
        ));
        $long = Redisq::getSize(array(
            'serverName'    => 'webChat', #服务器名，参照见Redisa的定义 ResysQ
            'key'      => 'chat:message:quene',  #队列名
        ));
        var_dump($long);
        $arr = Redisq::range(array(
            'serverName'  => 'webChat',     #服务器名，参照见Redisa的定义 ResysQ
            'key'         => 'chat:message:quene',  #队列名
            'offset'      => 0,      #开始索引值
            'len'         => -1,      #结束索引值
        ));
        
        var_dump($arr);
?>