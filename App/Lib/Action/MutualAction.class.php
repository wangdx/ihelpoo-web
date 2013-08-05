<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class MutualAction extends Action {

	protected function _initialize() {
		$userloginid = session('userloginid');
		if (!empty($userloginid)) {
			i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
		} else {
			redirect('/user/notlogin', 0, '你还没有登录呢...');
		}
		header("Content-Type:text/html; charset=utf-8");
	}

    public function index() {
        $this->display();
    }

    public function shield()
    {
    	$userloginid = session('userloginid');
    	$title = "我屏蔽的人";
        $this->assign('title',$title);
        $UserPriority = M("UserPriority");
        if (isset($_GET['list'])) {

            /**
             * show shielded users
             */
        	$page = i_page_get_num();
        	$count = 30;
            $offset = $page * $count;
            $userShielded = $UserPriority->where("i_user_priority.uid = $userloginid AND sid != ''")
            ->join('i_user_login ON i_user_priority.sid = i_user_login.uid')
            ->order("time DESC")->limit($offset, $count)->select();
            $this->assign('userShielded',$userShielded);

            /**
             * pageing link
             */
            $userShieldedNums = $UserPriority->where("uid = $userloginid AND sid != ''")->count();
            $totalPages = ceil($userShieldedNums / $count);
            $this->assign('userShieldedNums',$userShieldedNums);
            $this->assign('totalPages',$totalPages);

            /**
             * show shielded users nums
             */
            $userBeShielded = $UserPriority->where("sid = $userloginid")->count();
            $this->assign('beShieldedNums',$userBeShielded);
        } else {
        	$IUserLogin = D("IUserLogin");

        	/**
             * operational part ; insert & delete shield data
             */
            $shieldUid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
            if (empty($shieldUid)) {
                redirect('/stream/u', 3, 'shield uid arguement is NULL');
            }
            $userLogin = $IUserLogin->userExists($shieldUid);
            if (empty($userLogin)) {
        	    redirect('/stream/u', 3, 'user is not exist');
            }
            if ($userloginid == $shieldUid) {
            	redirect('/stream/u', 3, '自己不能屏蔽自己噢...3秒后页面跳转');
            }


        	/**
             * delete shield data
             */
       	    if (isset($_GET['del'])) {
       	        $isDelShieldData = $UserPriority->where("uid = $userloginid AND sid = $shieldUid")->delete();
       	        if ($isDelShieldData) {
       	            redirect('/stream/u/'.$shieldUid, 3, '取消屏蔽成功，3秒后页面跳转');
       	        } else {
       	            redirect('/stream/u', 3, 'Delete shield data failed');
       	        }
       	    }

            /**
             * insert record into i_user_priority
             * make sure record is not already exist;
             * make sure user is not priority
             */
            $isShieldExist = $UserPriority->where("uid = $userloginid AND sid = $shieldUid")->find();
            if ($isShieldExist) {
                redirect('/stream/u/'.$shieldUid, 3, '已经屏蔽他了');
            }
            $isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $shieldUid")->find();
            if ($isPriorityExist) {
                redirect('/stream/u/'.$shieldUid, 3, '已经圈了他，取消圈后才能屏蔽');
            }

            /**
             * begin insert priority data
             */
            $shieldInsertData = array(
                'id' => '',
                'uid' => $userloginid,
                'sid' => $shieldUid,
                'time' => time(),
            );
            $isShieldDataInserted = $UserPriority->add($shieldInsertData);
            if (!$isShieldDataInserted) {
                exit("Shield data insert failed");
            }

            /**
             * send system message to prioritied user
             */
            $MsgSystem = M("MsgSystem");
            $msgPriorityContent = "有人屏蔽了你，请注意言行，被扣除5活跃  (每天最多扣5活跃)";
            $msgPriorityData = array(
                'id' => NULL,
                'uid' => $shieldUid,
                'type' => 'mutual/shield',
                'from_uid' => '',
                'content' => $msgPriorityContent,
                'time' => time(),
                'deliver' => 0,
            );
            $isMsgPriorityDataInserted = $MsgSystem->add($msgPriorityData);

            /**
             * msg active
             */
            $MsgActive = M("MsgActive");
            $alreadyMinActive = $MsgActive->where("uid = $shieldUid AND way = 'min'")->order('time DESC')->find();
            if (empty($alreadyMinActive['id']) || (time() - $alreadyMinActive['time'] > 86400)) {
	            $msgActiveArray = array(
					'id' => '',
					'uid' => $shieldUid,
					'total' => $userLogin['active'],
					'change' => 5,
					'way' => 'min',
					'reason' => '被人屏蔽了，请注意言行，被扣除5活跃 (每天最多扣5活跃)',
					'time' => time(),
					'deliver' => 0,
	            );
	            $MsgActive->add($msgActiveArray);
            }
            redirect('/stream/u/'.$shieldUid, 3, '屏蔽成功，3秒后页面跳转');
        }
        $this->display();
    }

    public function priority()
    {
    	$userloginid = session('userloginid');
    	$title = "我圈的";
        $this->assign('title',$title);

        $IUserLogin = D("IUserLogin");
        $UserPriority = M("UserPriority");

        $UserInfo = M("UserInfo");

        /**
         * operational part ; insert & delete priority data
         */
        $priorityUid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        if (empty($priorityUid)) {
        	redirect('/stream/u', 3, 'shield uid arguement is NULL');
        }
        $userLogin = $IUserLogin->userExists($priorityUid);
        if (empty($userLogin)) {
        	redirect('/stream/u', 3, '你要圈的 用户不存在');
        }
        if ($userloginid == $priorityUid) {
        	redirect('/stream/u', 3, '自己不能圈自己噢...3秒后页面跳转');
        }

        /**
         * delete priority data
         */
        if (isset($_GET['del'])) {
        	$isDelPriorityData = $UserPriority->where("uid = $userloginid AND pid = $priorityUid")->delete();
        	if ($isDelPriorityData) {

        		/**
		         * update i_user_info follow fans nums
		         */
		        $userInfoPriority = $UserInfo->find($userloginid);
		        $userInfoPrioritied = $UserInfo->find($priorityUid);
		        $newUserInfoPriorityData = array(
		        	'uid' => $userloginid,
		        	'follow' => $userInfoPriority['follow'] - 1,
		        );
		        $UserInfo->save($newUserInfoPriorityData);
		        $newUserInfoPrioritiedData = array(
		        	'uid' => $priorityUid,
		        	'fans' => $userInfoPrioritied['fans'] - 1,
		        );
		        $UserInfo->save($newUserInfoPrioritiedData);
        		redirect('/stream/u/'.$priorityUid, 3, '成功取消圈了，3秒后页面跳转');
        	} else {
        		exit("Delete priority data failed");
        	}
        }

        /**
         * insert record into i_user_priority
         * make sure record is not already exist;
         * make sure user is not shield;
         */
        $isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $priorityUid")->find();
        if ($isPriorityExist) {
        	redirect('/stream/u/'.$priorityUid, 3, '你已经圈了他');
        }
        $isShieldExist = $UserPriority->where("uid = $userloginid AND sid = $priorityUid")->find();
        if ($isShieldExist) {
        	redirect('/stream/u/'.$priorityUid, 3, '你已经屏蔽了他，取消屏蔽后才能圈');
        }

        /**
         * begin insert priority data
         */
        $priorityInsertData = array(
            'id' => '',
            'uid' => $userloginid,
            'pid' => $priorityUid,
        	'pid_type' => $userLogin['type'],
            'time' => time(),
        );
        $isPriorityDataInserted = $UserPriority->add($priorityInsertData);
        if (!$isPriorityDataInserted) {
        	exit("Priority data insert failed");
        }

        /**
         * send system message to prioritied user
         */
        $MsgSystem = M("MsgSystem");
        $msgPriorityContent = "圈了你; 越来越有影响力啦, 加油啊!";
        $msgPriorityData = array(
                'id' => NULL,
                'uid' => $priorityUid,
                'type' => 'mutual/priority',
                'from_uid' => $userloginid,
                'content' => $msgPriorityContent,
                'time' => time(),
                'deliver' => 0,
        );
        $isMsgPriorityDataInserted = $MsgSystem->add($msgPriorityData);
        if (!$isMsgPriorityDataInserted) {
        	exit("Msg priority data insert failed");
        }

        /**
         * send system message to prioriti user for user type 2
         */
        if ($userLogin['type'] == 2) {
	        $msgPriorityUserType2Content = "你加入了 ".$userLogin['nickname']." 组织; 默认接收我们组织推送的消息, 信息会越来越灵通:)";
	        $msgPriorityUserType2Data = array(
	                'id' => NULL,
	                'uid' => $userloginid,
	                'type' => 'mutual/priority_usertype2',
	                'from_uid' => $priorityUid,
	                'content' => $msgPriorityUserType2Content,
	                'time' => time(),
	                'deliver' => 0,
	        );
	        $MsgSystem->add($msgPriorityUserType2Data);
        }

        /**
         * update i_user_info follow fans nums
         */
        $userInfoPriority = $UserInfo->find($userloginid);
        $userInfoPrioritied = $UserInfo->find($priorityUid);
        $newUserInfoPriorityData = array(
        	'uid' => $userloginid,
        	'follow' => $userInfoPriority['follow'] + 1,
        );
        $UserInfo->save($newUserInfoPriorityData);
        $newUserInfoPrioritiedData = array(
        	'uid' => $priorityUid,
        	'fans' => $userInfoPrioritied['fans'] + 1,
        );
        $UserInfo->save($newUserInfoPrioritiedData);
        redirect('/stream/u/'.$priorityUid, 3, '成功圈了他，3秒后页面跳转...');
    }

    /**
     *
     * rc = real connect info
     */
    public function rc()
    {
    	$userloginid = session('userloginid');
    	$title = "互换实名联系方式";
        $this->assign('title',$title);
        $UserChangeinfo = M("UserChangeinfo");
        $withUserId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        $IUserLogin = D("IUserLogin");
        $userLogin = $IUserLogin->userExists($withUserId);
        if (!$userLogin) {
        	exit("user is not exits");
        }
        $this->assign('userLogin',$userLogin);
        $recordUserChangeinfo = $UserChangeinfo->where("uid = $withUserId AND withid = $userloginid")->find();
        if ($recordUserChangeinfo) {
        	$UserInfo = M("UserInfo");
            $userInfoconn = $UserInfo->find($withUserId);
            if (!$userInfoconn) {
        	    exit("user connect info is empty");
            }
            $this->assign('userInfoconn',$userInfoconn);
            if (!empty($userInfoconn['dormitory_op'])) {
            	$OpDormitory = M("OpDormitory");
                $dormitory = $OpDormitory->where("id = $userInfoconn[dormitory_op]")->find();
                $this->assign('dormitory',$dormitory['name']);
            }
            if (!empty($userInfoconn['province_op'])) {
            	$OpProvince = M("OpProvince");
                $province = $OpProvince->where("id = $userInfoconn[province_op]")->find();
                $this->assign('province',$province['name']);
            }
            if (!empty($userInfoconn['city_op'])) {
            	$OpCity = M("OpCity");
                $city = $OpCity->where("id = $userInfoconn[city_op]")->find();
                $this->assign('city',$city['name']);
            }
        }

        /**
         * info system message
         */
        $MsgSystem = M("MsgSystem");
        if (isset($_GET['require'])) {
            $realConnectInfo = "希望知道你的真实联系方式";
            $msgRealConnectInfo = array(
                'id' => NULL,
                'uid' => $withUserId,
                'type' => 'mutual/rc-para:?please',
                'url_id' => $userloginid,
                'from_uid' => $userloginid,
                'content' => $realConnectInfo,
                'time' => time(),
                'deliver' => 0,
            );
            $affetcedMsgRealConnectInfo = $MsgSystem->add($msgRealConnectInfo);
            if (empty($affetcedMsgRealConnectInfo)) {
                exit("message_system_require info connect insert failed");
            }
        }

        /**
         * accept show real connect info
         */
        if ($this->isPost()) {
            $postWithid = (int)htmlspecialchars(trim($_POST["withid"]));
            $postYesorno = trim(addslashes(htmlspecialchars(strip_tags($_POST["yesorno"]))));
            if ("yes" == $_POST["option"]) {
            	/**
            	 * single changeinfo
            	 */
            	$existRecordUserChangeinfo = $UserChangeinfo->where("uid = $userloginid AND withid = $postWithid")->find();
            	if (!empty($existRecordUserChangeinfo['id'])) {
            		redirect('/mutual/rc/'.$postWithid, 3, '您已经拥有查看权限:) 3秒后页面跳转...');
            	}
            	$dataChangeinfo = array(
            	    'id' => '',
            	    'uid' => $userloginid,
            	    'withid' => $postWithid,
            	    'time' => time(),
            	);
            	$affetcedChangeinfo = $UserChangeinfo->add($dataChangeinfo);
            	if (!$affetcedChangeinfo) {
            		exit("insert i_user_changeinfo 1 failed");
            	}

            	/**
            	 * single changeinfo message
            	 */
            	$mutualChangeInfo = "表示很高兴认识你, 同意给你真实联系方式";
            	$msgMutualChangeInfo = array(
                    'id' => NULL,
                    'uid' => $postWithid,
                    'type' => 'mutual/rc',
                    'url_id' => $userloginid,
                    'from_uid' => $userloginid,
                    'content' => $mutualChangeInfo,
                    'time' => time(),
                    'deliver' => 0,
            	);
            	$affetcedMsgMutualChangeInfo = $MsgSystem->add($msgMutualChangeInfo);
            	if (empty($affetcedMsgMutualChangeInfo)) {
            		exit("message_single_Change info insert failed");
            	}
            } else if ("no" == $_POST["option"]) {
            	$mutualChangeInfo = "婉拒了你的请求, 暂时不想给你真实联系方式";
            	$msgMutualChangeInfo = array(
                    'id' => NULL,
                    'uid' => $postWithid,
                    'type' => 'mutual/rc',
                    'url_id' => $userloginid,
                    'from_uid' => $userloginid,
                    'content' => $mutualChangeInfo,
                    'time' => time(),
                    'deliver' => 0,
            	);
            	$affetcedMsgMutualChangeInfo = $MsgSystem->add($msgMutualChangeInfo);
            	if (empty($affetcedMsgMutualChangeInfo)) {
            		exit("message_single_Change info insert failed");
            	}
            }
        }
        $this->display();
    }

    /**
     *
     * find other people
     */
    public function find()
    {
        $userloginid = session('userloginid');
        $this->assign('title','找人 找校友');
        if (preg_match("/username/iUs", $_SERVER["REQUEST_URI"])) {
        	$username = trim(addslashes(htmlspecialchars(strip_tags($_GET["username"]))));
		    $p = (int)htmlspecialchars(trim($_GET["p"]));
		    $p = $p <= 0 ? 1 : $p;
            if (!empty($username) && is_int($p)) {
            	$UserLogin = M("UserLogin");
                $searchname = $username;

                /**
                 * Pageing
                 */
                $page = $p - 1;
                $count = 15;
                $offset = $page * $count;

                $searchResult = $UserLogin->where("`nickname` LIKE '%".$searchname."%'")->join("i_school_info ON i_user_login.school = i_school_info.id")->limit($offset, $count)->select();
                if ($searchResult) {
                    $searchNameNums = $UserLogin->where("`nickname` LIKE '%".$searchname."%'")->count();
                    $this->assign('searchNameNums',$searchNameNums);
                    $totalPages = ceil($searchNameNums / $count);
                    $this->assign('totalPages',$totalPages);
                    $this->assign('searchResult',$searchResult);
                }
            }
        }
        $this->display();
    }

    /**
     * invite
     */
    public function invite()
    {
    	$userloginid = session('userloginid');
        $this->assign('title','我邀请的朋友');
        $UserInvite = M("UserInvite");
        $totalInviteUserNums = $UserInvite->where("uid = $userloginid")->count();
        $this->assign('totalInviteUserNums',$totalInviteUserNums);
        if (!empty($totalInviteUserNums)) {
        	$recordsUserInvite = $UserInvite->where("i_user_invite.uid = $userloginid")
        	->join('i_user_login ON i_user_invite.inviteuid = i_user_login.uid')
        	->order('i_user_invite.time DESC')
        	->select();
        	$this->assign('recordsUserInvite',$recordsUserInvite);
        }
    	$this->display();
    }

}

?>