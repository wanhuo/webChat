<?php 
    namespace Api\Model;
    /**
     * @author cuihb
     * // dbobj 中的几个方法 lastInsertId()、single($query = '',$params = null)、
     * row($query)、column($query)、query($query)
     */
    class Muser extends Abstractex{
        public static $usertable = 'webchat_user';
        /**
         * 获取多个用户或者某个用户信息
         * @param unknown $paramArr
         */
        public static function getUserinfo($paramArr) {
            $options = array(
                'fields'      => array(),//要查询的字段或者以 英文'，'分开
                'accountid'   => 0, //若有，则查该用户信息
                'dept'        => '', //若有，则查该部门用户信息
                'isCount'     => 0, //是否是查询总数
            );
            if (is_array($paramArr))$options = array_merge($options, $paramArr);
            extract($options);
            $formatData = self::setSelectField($fields);
            
            $where = '';
            if($accountid)
                $where = " where accountid='{$accountid}' ";
            if(!$accountid && $dept)
                $where = " where dept='{$dept}' ";
            if($isCount) {
                $sql = " select count(*) from ".self::$usertable;
                return self::dbobj()->single($sql);
            }
            
            $sql = "select {$formatData} from ".self::$usertable." {$where}";
            return self::dbobj()->query($sql);
        }
        /**
         * 用户验证判断
         */
        public static function userAuth($accountid, $pwd=false) {
            if(!$accountid) return 0;
            $where = " where accountid='{$accountid}'";
            
            if(false !== $pwd) {
                $pwd = Mcommon::encryptPwd($pwd);
                $where .= " and pwd='{$pwd}' ";
            }
            
            $sql = "select uid from ".self::$usertable.$where;
            return self::dbobj()->single($sql) ? 1 : 0;
        }
        /****************************************
         *                redis操作                                   *
         ****************************************/
        /**
         * 获取最近的n个联系人
         */
        public static function getRecentMembers ($username, $num = 19) {
            return \Vendors\Redis\RedisModel::zrevrange(self::$redisServer, $username.\Config\St\Storekey::RECENT_MEMBERS, 0, $num);
        }
        /**
         * 删除一个最近联系人
         */
        public static function delRecentMembers($username, $chatid) {
            if(!$username || !$chatid) return false;
            return \Vendors\Redis\RedisModel::zRem(self::$redisServer, $username.\Config\St\Storekey::RECENT_MEMBERS, $chatid);
        }
        /**
         * 获取所有在线用户列表 clientid=>name
         */
        public static function getOnlineUsers () {
            $key = \Config\St\Storekey::USER_ONLINE_LIST;
            $tryCount = 3;
            while ($tryCount--) {
                $clientList = \Vendors\Redis\RedisModel::hashGet(self::$redisServer, $key);
                if (false === $clientList) {
                    $clientList = array();
                } else {
                    return $clientList;
                }
            }
            return $clientList;
        }
        /**
         * 根据chatid获取chatList
         */
        public static function getChatListFromChatid ($chatid) {
            $chatList = array();
            if(strpos($chatid, '--') > -1) {
                $chatList = explode('--', $chatid);
            } elseif (strpos($chatid, '-') > -1) {//非群组聊天
                $group = explode('-', $chatid);
                $chatList = Mgroup::getGroupMembers(array('master'=>$group[0], 'uuid'=>$group[1]));
            }
            return $chatList;
        }
    }
?>