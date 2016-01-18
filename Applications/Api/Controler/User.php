<?php 
    namespace Api\Controler;
    use Api\Model\Muser;
    
    /**
     * 聊天系统用户相关
     */
    class User extends Abstractex {
        public function doTest() {
            $chatDeptArr = explode(',', trim('技术部,互联网部,',','));
            $toUserList = array();
            foreach($chatDeptArr as $key=>$dept) {
                $userList = Muser::getUserinfo(array(
                    'fields' => array('accountid'),
                    'dept'   => $dept,
                ));
                $toUserList = array_merge($toUserList, $userList);
            }
            foreach($toUserList as $key=>$userval) {
                $toUserList[$key] = $userval['accountid'];
            }
            var_dump($toUserList);die;
            
            
            
            $a = array();
            $b = array('d','b', 'c','m');
            var_dump(array_intersect($a,$b));die;
            var_dump($this->doDeptUser());
        }
        
        /**
         * 登录
         * @return number
         */
        public function doLogin(){
            $username = $this->toStr('accounid');
            $password = $this->toStr('pwd');
            return Muser::userAuth($username, $password);
        }
        /**
         * 获取某个用户信息
         */
        public  function doOneUser(){
            $accountid = $this->toStr('accountid');
            $data = Muser::getUserinfo(array(
                'accountid' => $accountid,
            ));
            if($data) $this->_success($data);
            $this->_error('无用户信息');
        }
        /**
         * 获取某个部门用户账号信息
         */
        public function doDeptUser() {
            $dept = $this->toStr('dept');
            $data = Muser::getUserinfo(array(
                'dept'   => $dept,
                'fields' => array('accountid'),
            ));
            if($data) $this->_success($data);
            $this->_error('部门信息有误');
        }
        /**
         * 获取所有用户信息
         * 仅支持最高6级部门
         */
        public function doAllUsers(){
            $data = Muser::getUserinfo(array());
            $outArr = array();
            foreach((array)$data as $key=>$val) {
                $deptArr = explode(',', trim($val['deptDetail'], ','));
                $deep = count($deptArr);
                switch($deep) {
                    case 1: $outArr[$deptArr[0]][$val['accountid']] = $val['username']; 
                        break;
                    case 2:
                        $outArr[$deptArr[0]][$deptArr[1]][$val['accountid']] = $val['username'];
                        break;
                    case 3:
                        $outArr[$deptArr[0]][$deptArr[1]][$deptArr[2]][$val['accountid']] = $val['username'];
                        break;
                    case 4:
                        $outArr[$deptArr[0]][$deptArr[1]][$deptArr[2]][$deptArr[3]][$val['accountid']] = $val['username'];
                        break;
                    case 5:
                        $outArr[$deptArr[0]][$deptArr[1]][$deptArr[2]][$deptArr[3]][$deptArr[4]][$val['accountid']] = $val['username'];
                        break;
                    case 6:
                        $outArr[$deptArr[0]][$deptArr[1]][$deptArr[2]][$deptArr[3]][$deptArr[4]][$deptArr[5]][$val['accountid']] = $val['username'];
                        break;
                }
            }
            $this->_success($outArr);
        }
        /**
         * 获取最近联系人列表
         */
        public function doRecentContact() {
            $username = $this->toStr('accountid');
            $num      = $this->toInt('num');
        
            if(!$username) return false;
            $num = $num ? $num-1 : 19;
            $recentUsers = Muser::getRecentMembers($username, $num);
            $this->_success($recentUsers);
        }
        /**
         * 获取所有在线用户 账号列表
         */
        public function doOnlineUsers() {
            $clientList = Muser::getOnlineUsers();
            if(false === $clientList)
                return false;
            $this->_success($clientList);
        }
    }
?>