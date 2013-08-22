<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class UserAction extends Action {

    protected function _initialize()
    {

    	/**
         *
         * Grade active control
         * Update user contious login status; Update i_user_status.login_days_co i_user_status.total_active_ti && i_user_status.active;
         * 3days +5
         * 5days +10
         * 10days +25
         * 20days +50
         * 30days +75
         * @param int $uid
         * @param int $logintime
         * @param int $lastlogintime
         */
        function userUpdateStatus($uid, $logintime, $lastlogintime)
        {
            $UserLogin = M("UserLogin");
            $UserStatus = M("UserStatus");
            $MsgActive = M("MsgActive");
            $recordUserLogin = $UserLogin->find($uid);
            $recordUserStatus = $UserStatus->find($uid);
            $logintimeThis = date('z', $logintime);
            $logintimeLast = date('z', $lastlogintime);
            if (($logintimeLast + 1 == $logintimeThis) || ($logintimeLast - 365 == $logintimeThis)) {
            	$timeGap = 'followday';
            } else if ($logintimeLast == $logintimeThis) {
                $timeGap = 'sameday';
            } else {
            	$timeGap = 'morethanfollowday';
            }
            $newUserActive = $recordUserLogin['active'];
            if ($recordUserStatus['active_flag'] == '1' && $timeGap == 'sameday') {
            	return 'active added';
            }
            $activeFlag = 0;
            $hourRules = 0;
            $dayRules = 0;

            /**
             * 0.5 < hours < 2 +5
             * 2 < hours +10
             */
            $msgActiveArray = array(
            	'id' => '',
            	'uid' => $uid,
            	'total' => '',
            	//'change' => '',
            	'way' => 'add',
            	//'reason' => '',
            	'time' => time(),
            	'deliver' => 0,
            );
            if (!empty($recordUserStatus['total_active_ti'])) {
                if ($recordUserStatus['total_active_ti'] > 7200) {
                	$msgActiveArray['total'] = $newUserActive;
                    $msgActiveArray['change'] = 10;
                    $msgActiveArray['reason'] = '上次在线时长 '.floor($recordUserStatus['total_active_ti']/60).'min 大于两小时';
                    $newUserActive = $newUserActive + 10;
                    $activeFlag = 1;
                    $hourRules = 1;
                } else if ($recordUserStatus['total_active_ti'] > 1800) {
                	$msgActiveArray['total'] = $newUserActive;
                    $msgActiveArray['change'] = 5;
                    $msgActiveArray['reason'] = '上次在线时长 '.floor($recordUserStatus['total_active_ti']/60).'min 大于半小时';
                    $newUserActive = $newUserActive + 5;
                    $activeFlag = 1;
                    $hourRules = 1;
                }
            }

            if ($hourRules) {
	            $userLoginData = array(
	                'uid' => $uid,
	                'active' => $newUserActive,
	            );
	            $UserLogin->save($userLoginData);

	            /**
	             * ass msg active record
	             */
	            $MsgActive->add($msgActiveArray);
            }

            /**
             * login follow days count
             */
            if (($timeGap == 'followday') && !empty($recordUserLogin['login_days_co'])) {
            	if ($recordUserLogin['login_days_co'] == 3) {
            		$msgActiveArray['total'] = $newUserActive;
                    $msgActiveArray['change'] = 5;
                    $msgActiveArray['reason'] = '连续登录3天';
            		$newUserActive = $newUserActive + 5;
            		$activeFlag = 1;
            	} else if ($recordUserLogin['login_days_co'] == 5) {
            		$msgActiveArray['total'] = $newUserActive;
                    $msgActiveArray['change'] = 10;
                    $msgActiveArray['reason'] = '连续登录5天';
            		$newUserActive = $newUserActive + 10;
            		$activeFlag = 1;
            	} else if ($recordUserLogin['login_days_co'] == 10) {
            		$msgActiveArray['total'] = $newUserActive;
                    $msgActiveArray['change'] = 25;
                    $msgActiveArray['reason'] = '连续登录10天';
            		$newUserActive = $newUserActive + 25;
            		$activeFlag = 1;
            	} else if ($recordUserLogin['login_days_co'] == 20) {
            		$msgActiveArray['total'] = $newUserActive;
                    $msgActiveArray['change'] = 50;
                    $msgActiveArray['reason'] = '连续登录20天';
            		$newUserActive = $newUserActive + 50;
            		$activeFlag = 1;
            	} else if (($recordUserLogin['login_days_co'] % 10) == 0) {
            		$msgActiveArray['total'] = $newUserActive;
                    $msgActiveArray['change'] = 75;
                    $msgActiveArray['reason'] = '连续登录'.$recordUserLogin['login_days_co'].'天';
            		$newUserActive = $newUserActive + 75;
            		$activeFlag = 1;
            	} else {
            		$msgActiveArray['total'] = $newUserActive;
                    $msgActiveArray['change'] = 2;
                    $msgActiveArray['reason'] = '连续登录'.$recordUserLogin['login_days_co'].'天';
            		$newUserActive = $newUserActive + 2;
            		$activeFlag = 1;
            	}
            	$userLoginData = array(
                    'uid' => $uid,
                    'active' => $newUserActive,
                    'login_days_co' => $recordUserLogin['login_days_co'] + 1,
            	);
            	$userStatusData = array(
                    'uid' => $uid,
                    'total_active_ti' => '0',
                    'active_s_limit' => '0',
                    'active_c_limit' => '0',
                    'active_flag' => $activeFlag,
            	);
            	$dayRules = 1;
            } else if ($timeGap == 'morethanfollowday') {
            	$userLoginData = array(
                  	'uid' => $uid,
                    'active' => $newUserActive,
                    'login_days_co' => 1,
            	);
            	$userStatusData = array(
                    'uid' => $uid,
                    'total_active_ti' => '0',
                    'active_s_limit' => '0',
                    'active_c_limit' => '0',
                    'active_flag' => $activeFlag,
            	);
            } else {
            	$userLoginData = array(
                    'uid' => $uid,
                	'active' => $newUserActive,
                    'login_days_co' => $recordUserLogin['login_days_co'],
            	);
            	$userStatusData = array(
                    'uid' => $uid,
                    'login_days_co' => $recordUserLogin['login_days_co'],
                    'active_flag' => $activeFlag,
            	);
            }
            $UserLogin->save($userLoginData);
            $UserStatus->save($userStatusData);

            /**
             * ass msg active record
             */
            if (empty($msgActiveArray['total'])) {
            	$msgActiveArray['total'] = 0;
            }
            if ($dayRules) {
            	$MsgActive->add($msgActiveArray);
            }
            return 'update status ok';
        }

        $userloginid = session('userloginid');
        if (!empty($userloginid)) {
        	i_db_update_activetime($userloginid);
    		$IUserLogin = D("IUserLogin");
    		$userloginedrecord = $IUserLogin->userExists($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
        }
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
	    redirect('/index', 0, '');
    }
    
    public function login()
    {
    	$userloginid = session('userloginid');
        if ($userloginid) {
        	redirect('/stream', 1, '已经登录...');
        }
    	if ($this->isPost()) {
	    	$UserLogin = M("UserLogin");
	        $validate = array(
	            array('email', 'email', '邮箱格式不对'),
	            array('password', 'require', '密码不能为空'),
	        );
	        $UserLogin->setProperty("_validate", $validate);
	        $result = $UserLogin->create();
	        if (!$result) {
	            $loginerror = $UserLogin->getError();
	            exit($loginerror);
	        } else {
	            $email = trim(addslashes(htmlspecialchars(strip_tags($_POST["email"]))));
	            $password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));//strip_tags
	            $loginstatus = trim(strip_tags($_POST["login_status"]));
	            $rememberpassword = trim(strip_tags($_POST["remember_password"]));
	            $password = md5($password);
	            $IUserLogin = D("IUserLogin");
	            if ($loginstatus != 'on') {
	            	$dbUser = $IUserLogin->userVerification($email, $password, 1);
	            } else {
	            	$dbUser = $IUserLogin->userVerification($email, $password, 2);
	            }
	            if (is_array($dbUser)) {
	            	userUpdateStatus($dbUser['uid'], $dbUser['logintime'], $dbUser['lastlogintime']);
                    session('userloginid',$dbUser['uid']);
                    if ($rememberpassword == 'on') {
	                    setcookie('userEmail', $dbUser['email'], time() + 3600 * 24 *30, '/');
	                    setcookie('userPassword', $dbUser['password'], time() + 3600 * 24 *30, '/');
	                    setcookie('userLoginstatus', $loginstatus, time() + 3600 * 24 *30, '/');
                    }

                    /**
                     * first time login
                     * guide to file personal infomation
                     */
                    $UserInfo = M("UserInfo");
                    $recordUserInfo = $UserInfo->find($dbUser['uid']);
                    if (empty($recordUserInfo['realname_re'])) {
                    	redirect('/setting/realfirst?step=1', 0, '填充个人真实信息...');
                    }
                    
                    /**
                     * select school
                     */
                    $recordSchoolInfo = i_school_domain();
                    if ($recordSchoolInfo['id'] != $dbUser['school']) {
                    	$schoolDomain = $recordSchoolInfo['domain_main'] == NULL ? $recordSchoolInfo['domain'] : $recordSchoolInfo['domain_main'];
                    	$schoolDomain = "http://".$schoolDomain;
                    	redirect($schoolDomain.'/stream', 3, '登录成功, 正在串校进入'.$recordSchoolInfo['school'].'...');
                    } else {
                    	redirect('/stream', 0, '登录成功...');
                    }
	            } else {
	            	redirect('/', 3, $dbUser.'，请重新登录，3秒后页面跳转...');
	            }
	        }
    	}
    }

    public function loginfast()
    {
    	$this->assign('title','快速登录');
    	$userloginid = session('userloginid');
    	if ($userloginid) {
    		redirect('/stream', 0, '已经登录...');
    	}
    	$IUserLogin = D("IUserLogin");
    	if (!empty($_COOKIE['userEmail']) && !empty($_COOKIE['userPassword'])) {
    		$email = $_COOKIE['userEmail'];
    		$password = $_COOKIE['userPassword'];
    		$loginstatus = $_COOKIE['userLoginstatus'];
    		if ($loginstatus == '1') {
    			$dbUser = $IUserLogin->userVerification($email, $password, 1);
    		} else {
    			$dbUser = $IUserLogin->userVerification($email, $password, 2);
    		}
    		if (is_array($dbUser)) {
    			userUpdateStatus($dbUser['uid'], $dbUser['logintime'], $dbUser['lastlogintime']);
    			session('userloginid',$dbUser['uid']);
    			
    			/**
                 * select school
                 */
    			$recordSchoolInfo = i_school_domain();
                if ($recordSchoolInfo['id'] != $dbUser['school']) {
                 	$schoolDomain = $recordSchoolInfo['domain_main'] == NULL ? $recordSchoolInfo['domain'] : $recordSchoolInfo['domain_main'];
                    $schoolDomain = "http://".$schoolDomain;
                    redirect($schoolDomain.'/stream', 3, '快速登录成功, 正在串校进入'.$recordSchoolInfo['school'].'...');
                } else {
                    redirect('/stream', 0, '快速登录成功...');
                }
    		}
    	}
    	redirect('/', 3, '上次已经点击退出 or 账号密码错误, 快速登录失败...');
    }
    
    
    /**
     * need recode , add qq renren
     */
    public function loginweibo()
    {
        $userloginid = session('userloginid');
        if ($userloginid) {
            $this->ajaxReturn(0,'已经登录','exist');
    	}
    	$recordSchoolInfo = i_school_domain();
        $UserLogin = M("UserLogin");
        $UserLoginWb = M("UserLoginWb");
        if (!empty($_POST['i_w_user_id'])) {
            $isWeiboExist = $UserLoginWb->where("weibo_uid = $_POST[i_w_user_id]")->find();
            if (empty($isWeiboExist['uid'])) {
            	$email = $_POST['i_w_user_id']."@loginweibo.com";
            	$password = rand(10000000, 99999999);;
            	$nickname = $_POST['i_w_user_name'];
            	if ($_POST['i_w_user_sex'] == 'm') {
            		$sex = 1;
            	} else {
            		$sex = 2;
            	}
            	$dateInfo = getdate();
            	$enteryear = $dateInfo['year'];
            	$introduction = $_POST['i_w_user_description'];
            	
                /**
                 * insert new user
                 */
            	$newUserData = array(
            		'uid' => '',
            		'status' => 1,
            		'email' => $email,
            		'password' => $password,
            		'nickname' => $nickname,
            		'sex' => $sex,
            		'enteryear' => $enteryear,
            		'type' => '1',
            		'priority' => '4',
            		'logintime' => time(),
            		'creat_ti' => time(),
            		'school' => $recordSchoolInfo['id'],
            		'online' => 1,
            		'coins' => 0,
            		'active' => 0,
            		'icon_fl' => 0,
            	);
            	$lastInsertUid = $UserLogin->add($newUserData);
            	
            	/**
            	 * user login weibo
            	 */
            	$dataUserLoginWb = array(
            	    'uid' => $lastInsertUid,
                    'weibo_uid' => $_POST['i_w_user_id'],
            		'switch' => 1,
            	);
            	$UserLoginWb->add($dataUserLoginWb);
            	
            	/**
            	 * userinfo base
            	 */
                $UserInfo = M("UserInfo");
                $rowUserInfo = array (
                    'uid' => $lastInsertUid,
                    'introduction' => $introduction,
                   	'weibo' => 'http://weibo.com/'.$_POST['i_w_user_id'],
                   	'dynamic' => 1,
                   	'fans' => 0,
                   	'follow' => 0,
                );
                $UserInfo->add($rowUserInfo);
            	
                /**
                 * Add IUserStatus
                 */
                $UserStatus = M("UserStatus");
                $dataUserStatus = array(
                    'uid' => $lastInsertUid,
                    'record_limit' => 6,
                );
                $UserStatus->add($dataUserStatus);
                
                /**TODO
                 * send system message.
                $MsgSystem = M("MsgSystem");
                $msgRegisterContent = "欢迎".$nickname."通过微博来到".$recordSchoolInfo['school']."我帮圈圈:) 故事开始啦!";
                $msgRegisterData = array(
                    'id' => NULL,
                    'uid' => $lastInsertUid,
                    'type' => 'system',
                    'content' => $msgRegisterContent,
                    'time' => time(),
                    'deliver' => 0,
                );
                $MsgSystem->add($msgRegisterData);
                i_savenotice('10000', $newUserId, 'system/welcome', '');
                 */
                
                /**
	             * add default dynamic record.
	             */
	            $recordDynamicContent = "我刚刚通微博登录加入了".$recordSchoolInfo['school']."我帮圈圈:)";
	            $RecordSay = M("RecordSay");
	            $RecordDynamic = M("RecordDynamic");
	            $newRecordSayData = array(
	            	'uid' => $lastInsertUid,
	            	'say_type' => 2,
	            	'content' => $recordDynamicContent,
	            	'time' => time(),
	            	'from' => '动态'
	            );
	            $newRecordSayId = $RecordSay->add($newRecordSayData);
	            
	            $newRecordDynamicData = array(
	            	'sid' => $newRecordSayId,
	            	'type' => 'join',
	            );
	            $RecordDynamic->add($newRecordDynamicData);
                
	            /**
	             * fillaccount
	             */
	            session('userloginid',$lastInsertUid);
	            $this->ajaxReturn('stream','微博注册登录成功...','step');
            } else {
                $weiboUserLogin = $UserLogin->find($isWeiboExist['uid']);
                if (empty($weiboUserLogin['email']) || empty($weiboUserLogin['password'])) {
                	session('userloginid', $isWeiboExist['uid']);
                	$this->ajaxReturn('setting/fillaccount','登录成功，请完善账号资料...','step');
                }
                
                $IUserLogin = D("IUserLogin");
	           	$dbUser = $IUserLogin->userVerification($weiboUserLogin['email'], $weiboUserLogin['password'], 1);
	            if (is_array($dbUser)) {
	            	userUpdateStatus($dbUser['uid'], $dbUser['logintime'], $dbUser['lastlogintime']);
                    session('userloginid',$dbUser['uid']);
                    setcookie('userEmail', $dbUser['email'], time() + 3600 * 24 *30, '/');
                    setcookie('userPassword', $dbUser['password'], time() + 3600 * 24 *30, '/');
                    $this->ajaxReturn(0,'登录成功','ok');
	            }
            }
        }
        $this->ajaxReturn(0,'登录失败','wrong');
    }

    public function quit()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
        $userloginid = session('userloginid');
        $updateUserOnlineData = array(
        	'uid' => $userloginid,
            'online' => 0
        );
        $UserLogin = M("UserLogin");
        $UserLogin->save($updateUserOnlineData);
        session('userloginid', null);
        setcookie('userEmail', NULL, time() - 1, '/');
        setcookie('userPassword', NULL, time() - 1, '/');
        setcookie('userLoginstatus', NULL, time() - 1, '/');
        $this->assign('title','安全退出');
        if (empty($_GET['way'])) {
        	redirect('/user/quit?way=already', 0, 'quit...');
        }
        $this->display();
    }

    public function notlogin()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
        if ($userloginid) {
        	redirect('/stream', 1, '已经登录...');
        }
        $this->assign('title','您还没有登录呢!');
        $this->display();
    }
    
    /**
     *
     * register
     */
    public function register()
    {
    	
    	$userloginid = session('userloginid');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$this->assign('title','用户注册 '.$recordSchoolInfo['school']);
        $OpAcademy = M("OpAcademy");
        $listOpAcademy = $OpAcademy->where("school = $recordSchoolInfo[id]")->select();
        $this->assign('listOpAcademy',$listOpAcademy);
        $this->assign('recordSchoolInfo',$recordSchoolInfo);
        
        if (!empty($_GET['school'])) {
        	$getSchoolId = $_GET['school'];
        	$SchoolInfo = M("SchoolInfo");
        	$selectSchoolInfo = $SchoolInfo->find($getSchoolId);
        	$selectSchoolDoamin = $selectSchoolInfo['domain_main'] == NULL ? $selectSchoolInfo['domain'] : $selectSchoolInfo['domain_main'];
        	$selectSchoolDoamin = "http://".$selectSchoolDoamin;
        	if (!empty($selectSchoolDoamin)) {
        		redirect($selectSchoolDoamin.'/user/register', 0, '跳转学校...');
        	}
        }
        
        if (!empty($_POST['getschoollist'])) {
        	$SchoolInfo = M("SchoolInfo");
        	$resultsSchoolInfo = $SchoolInfo->select();
        	echo '<div class="setting_school_list_div"><a class="gray f12" id="setting_school_close_span"><span class="close_x" title="关闭">×</span></a><ul>';
        	foreach ($resultsSchoolInfo as $schoolInfo) {
        		echo "<li><a href='".__ROOT__."/user/register?school=$schoolInfo[id]'>$schoolInfo[school]</a></li>";
        	}
        	echo '</ul></div>';
        	exit();
        }

        /**
         * inviter user info
         */
        $UserLogin = M("UserLogin");
        $userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        if (!empty($userId) && $userId > 9999) {
        	$inviteUserRecord = $UserLogin->where("uid = $userId")->find();
        	if (!empty($inviteUserRecord['uid'])) {
        		$this->assign('inviteUser',$inviteUserRecord);

        		/**
        		 * more user info
        		 */
        		$UserInfo = M("UserInfo");
        		$OpAcademy = M("OpAcademy");
        		$OpSpecialty = M("OpSpecialty");
        		$userInfo = $UserInfo->find($userId);
        		$this->assign('userInfo', $userInfo);
        		if (!empty($userInfo['academy_op'])) {
        			$userAcademy = $OpAcademy->where("id = $userInfo[academy_op]")->find();
        			$this->assign('userAcademy', $userAcademy);
        		}
        		if (!empty($userInfo['specialty_op'])) {
        			$userSpecialty = $OpSpecialty->where("id = $userInfo[specialty_op]")->find();
        			$this->assign('userSpecialty', $userSpecialty);
        		}
        	}
        }

        /**
         * post
         */
        if ($this->isPost()) {
	        $validate = array(
	            array('email', 'email', '邮箱格式不对'),
	            array('email','','邮箱已经存在！',0,'unique',1),
	            array('password', 'require', '密码不能为空'),
	            array('passwordrepeat','password','两次密码不一致',0,'confirm'),
	            array('nickname', 'require', '昵称不能为空'),
	            array('sex', 'number', 'sex格式错误'),
	            array('enteryear', 'number', 'enteryear格式错误'),
	            array('academy', 'number', 'academy格式错误'),
	            array('school', 'number', 'school格式错误'),
	            array('usertype', 'require', 'usertype不能为空'),
	        );
	        $UserLogin->setProperty("_validate", $validate);
	        $result = $UserLogin->create();
	        if (!$result) {
	            $errorRegister = $UserLogin->getError();
	            redirect('/user/register/'.$userId, 3, $errorRegister.' 3秒后跳转');
	        } else {
	            $email = htmlspecialchars(strtolower(trim($_POST["email"])));
	            $password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));
	            $password = md5($password);
	            $nickname = trim(addslashes(htmlspecialchars(strip_tags($_POST["nickname"]))));
	            $usertype = trim(addslashes(htmlspecialchars(strip_tags($_POST["usertype"]))));
	            $sex = htmlspecialchars(strtolower(trim($_POST["sex"])));
	            $enteryear = htmlspecialchars(strtolower(trim($_POST["enteryear"])));
	            $academy = htmlspecialchars(strtolower(trim($_POST["academy"])));
	            $school = htmlspecialchars(strtolower(trim($_POST["school"])));
	            $nickname = str_ireplace(' ','',$nickname);
	            
	            /**
	             * 1 for default student
	             * 2 for group
	             * 3 for business
	             * 4 for teacher
	             * 5 for postgraduate
	             * 6 for senior
	             */
	            $type = 1;
	            if ($usertype == 'default') {
	            	$type = 1;
	            } else if ($usertype == 'teacher') {
	            	$type = 4;
	            } else if ($usertype == 'postgraduate') {
	            	$type = 5;
	            } else if ($usertype == 'senior') {
	            	$type = 6;
	            }
	            $newUserlogignData = array(
	            	'uid' => '',
	            	'status' => '1',
					'email' => $email,
	            	'password' => $password,
	            	'nickname' => $nickname,
	            	'sex' => $sex,
	            	'enteryear' => $enteryear,
	            	'type' => $type,
	            	'priority' => '4',
	            	'creat_ti' => time(),
	            	'icon_fl' => 0,
	            	'school' => $school
	            );
	            $newUserId = $UserLogin->add($newUserlogignData);

	            /**
	             * Add IUserInfo
	             */
	            $newUserInfoData = array(
	            	'uid' => $newUserId,
	            	'academy_op' => $academy,
	            	'dynamic' => 1,
	            	'fans' => 0,
	            	'follow' => 0,
	            );
	            $UserInfo = M("UserInfo");
	            $UserInfo->add($newUserInfoData);

	            /**
	             * Add IUserStatus
	             */
	            $newUserStatusData = array(
	            	'uid' => $newUserId,
	            	'record_limit' => 6,
	            );
	            $UserStatus = M("UserStatus");
	            $UserStatus->add($newUserStatusData);

	            /**
	             * send system message.
	             */
//	            $MsgSystem = M("MsgSystem");
//	            $msgRegisterContent = "欢迎".$nickname."来到我帮圈圈:) 故事开始啦!";
//	            $msgRegisterData = array(
//                	'id' => NULL,
//                	'uid' => $newUserId,
//                 	'type' => 'system',
//              		'content' => $msgRegisterContent,
//                	'time' => time(),
//                	'deliver' => 0,
//	            );
//	            $MsgSystem->add($msgRegisterData);
                i_savenotice('10000', $newUserId, 'system/welcome', '');

	            /**
	             * add default dynamic record.
	             */
	            $recordDynamicContent = "我刚刚加入了我帮圈圈:)";
	            $RecordSay = M("RecordSay");
	            $RecordDynamic = M("RecordDynamic");
	            $newRecordSayData = array(
	            	'uid' => $newUserId,
	            	'say_type' => 2,
	            	'content' => $recordDynamicContent,
	            	'time' => time(),
	            	'from' => '动态'
	            );
	            $newRecordSayId = $RecordSay->add($newRecordSayData);
	            $newRecordDynamicData = array(
	            	'sid' => $newRecordSayId,
	            	'type' => 'join',
	            );
	            $RecordDynamic->add($newRecordDynamicData);

	            /**
	             * send welcome email, do not throw exception
	             */
	            Vendor('Ihelpoo.Email');
                $emailObj = new Email();
	            $emailObj->welcome($email, $nickname);

	            /**
	             * send emailAffirm
	             */
	            $uhash = md5($email);
	            $emailObj->emailAffirm($newUserId, $uhash, $email, $nickname);

	            /**
	             * add invite info
	             */
	            if (!empty($userId)) {
		            $UserInvite = M("UserInvite");
		            $newInviteInfo = array(
		            	'id' => '',
		            	'uid' => $userId,
		            	'inviteuid' => $newUserId,
		            	'award' => 0,
		            	'time' => time(),
		            );
		            $UserInvite->add($newInviteInfo);

		            /**
		             * add msg system
		             */
		            $msgInviteRegisterContent = "您邀请的 ".$nickname." 加入了我帮圈圈:) 系统认定注册有效后，会奖励你50活跃";
	            	$msgRegisterData = array(
                		'id' => NULL,
                		'uid' => $userId,
                 		'type' => 'system',
              			'content' => $msgInviteRegisterContent,
                		'time' => time(),
                		'deliver' => 0,
	            	);
	            	$MsgSystem->add($msgRegisterData);

	            	/**
	            	 * begin insert priority data
	            	 */
	            	$UserPriority = M("UserPriority");
	            	$priorityInsertData = array(
			            'id' => '',
			            'uid' => $newUserId,
			            'pid' => $userId,
			            'time' => time(),
	            	);
	            	$UserPriority->add($priorityInsertData);

	            	/**
	            	 * quan
	            	 * send system message to prioritied user
	            	 */
	            	$msgPriorityContent = "圈了你; 越来越有影响力啦, 加油啊!";
	            	$msgPriorityData = array(
		                'id' => NULL,
		                'uid' => $userId,
		                'type' => 'mutual/priority',
		                'from_uid' => $newUserId,
		                'content' => $msgPriorityContent,
		                'time' => time(),
		                'deliver' => 0,
	            	);
	            	$MsgSystem->add($msgPriorityData);
	            	
	            	/**
	            	 * update i_user_info follow fans nums
	            	 */
	            	$newUserInfoPriorityData = array(
		        		'uid' => $newUserId,
		        		'follow' => 1,
	            	);
	            	$UserInfo->save($newUserInfoPriorityData);
	            	$userInfoPrioritied = $UserInfo->find($userId);
	            	$newUserInfoPrioritiedData = array(
			        	'uid' => $userId,
			        	'fans' => $userInfoPrioritied['fans'] + 1,
	            	);
	            	$UserInfo->save($newUserInfoPrioritiedData);
	            }
	            redirect('/index', 3, '注册成功啦...3秒后跳转到登录页面');
	        }
        }
        $this->display();
    }

    /**
     * i_user_login.status explain
     * if affirmed set i_user_login.status = 2
     * if i_user_login.status = 1 ; just ok
     * if i_user_login.status = 0 ; do not allow user to do anything
     */
    public function emailaffirm()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$this->assign('title','邮箱验证');
    	$userloginid = session('userloginid');
		$uid = (int)htmlspecialchars(trim($_GET["_URL_"][3]));
		$emailhash = trim(addslashes(htmlspecialchars(strip_tags($_GET["_URL_"][5]))));
		$IUserLogin = D("IUserLogin");
	    $userLogin = $IUserLogin->userExists($uid);
	    if ($_GET['new'] == 'mail') {

	    	/**
	    	 * send emailAffirm
	    	 */
	    	Vendor('Ihelpoo.Email');
	    	$emailObj = new Email();
	    	$userLoginNew = $IUserLogin->userExists($userloginid);
	    	$uhash = md5($userLoginNew['email']);
	    	$emailObj->emailAffirm($userloginid, $uhash, $userLoginNew['email'], $userLoginNew['nickname']);
	    	$emailaffirmInfo = "已经发送验证邮件到你的注册邮箱 ".$userLoginNew['email']." , 请查收点击验证... <a href='/user/emailaffirm?new=mail' class='f12'>重新发送</a>";
	    } else if (md5($userLogin['email']) == $emailhash) {
	    	$dataSet = array(
				'uid' => $uid,
				'status' => '2'
			);
			$isEmailAffirm = $IUserLogin->save($dataSet);

					/**
					 * send system message.
					 */
//					$MsgSystem = M("MsgSystem");
//					$msgContent = "您的邮箱验证成功了!";
//					$msgData = array(
//	                    'id' => NULL,
//	                    'uid' => $uid,
//	                    'type' => 'system',
//	                    'content' => $msgContent,
//	                    'time' => time(),
//	                    'deliver' => 0,
//					);
//					$MsgSystem->add($msgData);
                    //TODO add bounced notice in case user is online
                    //TODO i did not receive this msg
            i_savenotice('10000', $uid, 'system/mailverify', '');
			redirect('/', 3, '邮箱验证成功啦 :) 3秒后跳转到登录页面...');
		} else {
		    $emailaffirmInfo = "邮箱验证失败 :(";
		}
		$this->assign('emailaffirmInfo',$emailaffirmInfo);
		$this->display();
    }

    /**
     *
     * reset password
     */
    public function resetpw()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$this->assign('title','找回密码');
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
        	redirect('/stream/', 3, '你已经登录了，请退出后再找回密码 :) 3秒后页面跳转...');
        }
    	if ($this->isPost()) {
    		$IUserLogin = D("IUserLogin");
	        $validate = array(
	            array('email', 'email', '邮箱格式不对'),
	            array('nickname', 'require', '昵称不能为空'),
	        );
	        $IUserLogin->setProperty("_validate", $validate);
	        $result = $IUserLogin->create();
	        if (!$result) {
	            exit($IUserLogin->getError());
	        } else {
	        	$email = trim(addslashes(htmlspecialchars(strip_tags($_POST["email"]))));
	            $nickname = trim(addslashes(htmlspecialchars(strip_tags($_POST["nickname"]))));

	        	if (!empty($email)) {
	        		$userLogin = $IUserLogin->userExists($email);
	        		if (!empty($userLogin['uid'])) {
	        			$this->assign('userLogin', $userLogin);
	        		} else {
                		redirect('/user/resetpw', 3, "出错啦，邮箱不存在，请重新填写。3秒后跳转...");
                	}
	        	}

	        	if (!empty($nickname)) {
	        		$userLoginNickname = $IUserLogin->where("nickname = '$nickname'")->find();
	        		if (!empty($userLoginNickname['uid'])) {
	        			$this->assign('userLoginNickname', $userLoginNickname);
	        		} else {
                		redirect('/user/resetpw', 3, "出错啦，用户昵称不存在，请重新填写。3秒后跳转...");
               		}
	        	}

	        	if ($userLogin['nickname'] == $nickname) {

                    /**
                     * send email, do not throw exception
                     */
                    Vendor('Ihelpoo.Email');
                    $emailObj = new Email();
                    $toEmail = $email;
                    $toUid = $userLogin['uid'];
                    $toUhash = md5($userLogin['uid']*3);
                    $emailObj->resetpasswordask($toEmail, $userLogin['nickname'], $toUid, $toUhash);
                    $this->assign('resetsuccess', "您将要找回密码，确认连接已经发送到了您的邮箱 ".$email."");
                }
	        }
        }
        $this->display();
    }

    public function resetpwsure()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$this->assign('title','找回密码 确定');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$userHash = htmlspecialchars(trim($_GET["_URL_"][3]));
    	if (md5($userId*3) != $userHash) {
    		redirect('/', 3, '找回密码连接密钥错误 3秒后页面跳转 :(...');
    	}
    	$UserLogin = M("UserLogin");
    	$recordUserLogin = $UserLogin->find($userId);
    	if (empty($recordUserLogin['uid'])) {
    		redirect('/', 3, "出错啦，用户不存在。3秒后跳转...");
    	}
    	$this->assign('recordUserLogin', $recordUserLogin);
    	if ($_GET['action'] == 'sure') {
    		$newpassword = rand(10000000, 99999999);
    		$password = md5($newpassword);
    		$pwSet = array(
                'uid' => $recordUserLogin['uid'],
                'password' => $password
    		);
    		$UserLogin->save($pwSet);

    		/**
    		 * send email, do not throw exception
    		 */
    		Vendor('Ihelpoo.Email');
    		$emailObj = new Email();
    		$toEmail = $recordUserLogin['email'];
    		$emailObj->resetpassword($newpassword, $toEmail, $recordUserLogin['nickname']);

    		/**
    		 * send system message.
    		 */
//    		$MsgSystem = M("MsgSystem");
//    		$msgContent = "您的密码已经初始化, 请及时修改!";
//    		$msgData = array(
//                'id' => NULL,
//                'uid' => $recordUserLogin['uid'],
//                'type' => 'system',
//                'content' => $msgContent,
//            	'time' => time(),
//            	'deliver' => 0,
//    		);
//    		$MsgSystem->add($msgData);
            //TODO bounced notice in case user is online
            i_savenotice('10000', $recordUserLogin['uid'], 'system/initpwd', '');
    		$this->assign('resetsuccess', "新密码已经发送到您的邮箱".$toEmail.", 请及时查询，登录后按需要修改密码。");
    	}
    	$this->display();
    }

    /**
     *
     * realname modify
     */
    public function realnamemf()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
    	if (empty($userloginid)) {
        	redirect('/index', 0, '页面跳转...');
        }
        $AdminRealnamemf = M("AdminRealnamemf");
        $data = array(
            'id' => '',
            'uid' => $userloginid,
            'allow' => 0,
            'time' => time()
        );
        $isSetRealnamemf = $AdminRealnamemf->add($data);
        if ($isSetRealnamemf) {
            redirect('/stream', 3, '已经成功申请修改名字,我们查看后会邮件通知你修改...3秒后页面跳转');
        }
    }

    /**
     *
     * Ajax function return json
     */
    public function ajaxcheckemail()
    {
        if ($this->isPost()) {
        	$IUserLogin = D("IUserLogin");
        	$validate = array(
        		array('email', 'email', '邮箱格式不对'),
        	);
        	$IUserLogin->setProperty("_validate", $validate);
        	$result = $IUserLogin->create();
        	if ($result) {
        		$email = htmlspecialchars(strtolower(trim($_POST["email"])));
        		$userExist = $IUserLogin->userExists($email);
        		$status = "ok";
        		if (!empty($userExist['uid'])) {
        			$status = "exist";
        			$info = "邮箱已经存在";
        		}
        	} else {
        		$info = $IUserLogin->getError();
        		$status = "wrong";
        	}
            $this->ajaxReturn(0,$info,$status);
        }
    }
    
    public function ajaxchecknickname()
    {
        if ($this->isPost()) {
        	$nickname = trim(addslashes(htmlspecialchars(strip_tags($_POST["nickname"]))));
        	$nickname = str_ireplace(' ','',$nickname);
        	$UserLogin = M("UserLogin");
        	$recordUserLogin = $UserLogin->where("nickname = '$nickname'")->find();
        	if (!empty($recordUserLogin['uid'])) {
        		$this->ajaxReturn(0,'昵称已经存在','exist');
        	} else {
        		$this->ajaxReturn(0,'昵称可以注册','ok');
        	}
        }
    }
    
    public function ajaxcheckpassword()
    {
        if ($this->isPost()) {
        	$email = trim(addslashes(htmlspecialchars(strip_tags($_POST["email"]))));
        	$password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));
        	$password = md5($password);
        	$UserLogin = M("UserLogin");
        	$recordUserLogin = $UserLogin->where("email = '$email' AND password = '$password'")->find();
        	if (!empty($recordUserLogin['uid'])) {
        		$this->ajaxReturn(0,'可以登录','ok');
        	} else {
        		$this->ajaxReturn(0,'账号或密码错误','error');
        	}
        }
    }
}

?>