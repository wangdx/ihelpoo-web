<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class SchooladminAction extends Action {

    protected function _initialize() {
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	}
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('recordSchoolInfo',$recordSchoolInfo);
    	
    	$webmasterloginid = session('webmasterloginid');
    	if (!empty($webmasterloginid)) {
    		$webmasterloginname = session('webmasterloginname');
    		$this->assign('webmasterloginid',$webmasterloginid);
    		$this->assign('webmasterloginname',$webmasterloginname);
    	}
    	
    	function logincheck()
    	{
    		$webmasterloginid = session('webmasterloginid');
    		if (empty($webmasterloginid)) {
    			redirect('/schooladmin', 3, '还没有登录呢...');
    		} else {
    			$webmasterloginid = session('webmasterloginid');
    			$webmasterloginname = session('webmasterloginname');
    			return array(
    				'uid' => $webmasterloginid,
    				'nickname' => $webmasterloginname,
    			);
    		}
    	}
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index()
    {
    	$this->assign('title','校园管理后台');
    	$webmasterloginid = session('webmasterloginid');
    	if ($webmasterloginid) {
    		redirect('/schooladmin/main', 1, '已经登录...');
    	}
    	if ($this->isPost()) {
	    	$UserLogin = M("UserLogin");
	        $validate = array(
	            array('webmaster', 'email', 'webmaster email格式错误'),
	            array('password', 'require', '密码不能为空'),
	            array('cypher', 'require', '口令不能为空'),
	        );
	        $UserLogin->setProperty("_validate", $validate);
	        $result = $UserLogin->create();
	        if (!$result) {
	            exit($UserLogin->getError());
	        } else {
	            $webmaster = trim(addslashes(htmlspecialchars(strip_tags($_POST["webmaster"]))));
	            $password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));
	            $cypher = trim(addslashes(htmlspecialchars(strip_tags($_POST["cypher"]))));
	            $password = md5($password);
	            $cypher = md5($cypher);
	            $recordWebmasterUserLogin = $UserLogin->where("email = '$webmaster'")->find();
	            if (!empty($recordWebmasterUserLogin['uid'])) {
	            	if ($password != $recordWebmasterUserLogin['password']) {
                	    redirect('/schooladmin', 2, 'password wrong...');
                	}
                	$thisyear = getdate();
                    $cypherNum = "help".$thisyear['mon'].$thisyear['mday'];
                    if ($cypher != md5($cypherNum)) {
                	    redirect('/schooladmin', 2, 'cypher wrong...');
                	}
                	
                	$SchoolWebmaster = M("SchoolWebmaster");
                	$recordSchoolWebmaster = $SchoolWebmaster->where("uid = $recordWebmasterUserLogin[uid]")->find();
                	if (empty($recordSchoolWebmaster['id'])) {
                		redirect('/schooladmin', 2, '你不是站长，不能登录校园管理后台...');
                	}
                	$recordSchoolInfo = i_school_domain();
                	if ($recordSchoolInfo['id'] != $recordWebmasterUserLogin['school']) {
                		redirect('/schooladmin', 2, '不能登录其他学校的校园管理后台...');
                	}
                	session('webmasterloginid',$recordWebmasterUserLogin['uid']);
                	session('webmasterloginname',$recordWebmasterUserLogin['nickname']);
                	
                	/**
                	 * webmaster user operating record
                	 */
                	$SchoolRecord = M("SchoolRecord");
                	$newSchoolRecordData = array(
                		'id' => '',
                		'sys_id' => '',
                		'uid' => $recordWebmasterUserLogin['uid'],
                		'sid' => $recordSchoolInfo['id'],
                		'record' => '登录校园管理后台',
                		'time' => time()
                	
                	);
                	$SchoolRecord->add($newSchoolRecordData);
                	$callname = $recordSchoolWebmaster['position'] == 'm' ? '站长' : '副站长';
                    redirect('/schooladmin/main', 3, '登录成功! 欢迎您,'.$callname.' ...');
	            }
	        }
    	}
        $this->display();
    }
    
    public function quit()
    {
    	$this->assign('title','安全退出');
    	$webmasterloginid = session('webmasterloginid');
        session('webmasterloginid', null);
        session('webmasterloginname', null);
        
        /**
         * admin user operating record
         */
        if (!empty($webmasterloginid)) {
	        /**
	         * webmaster user operating record
	         */
	        $recordSchoolInfo = i_school_domain();
	        $SchoolRecord = M("SchoolRecord");
	        $newSchoolRecordData = array(
                'id' => '',
                'sys_id' => '',
                'uid' => $webmasterloginid,
                'sid' => $recordSchoolInfo['id'],
                'record' => '退出校园管理后台',
                'time' => time()
	         
	        );
	        $SchoolRecord->add($newSchoolRecordData);
        }
        $this->display();
    }
    
    public function main()
    {
    	$webmaster = logincheck();
    	$this->assign('title','校园管理后台');
    	$this->display();
    }
    
    public function parameter()
    {
    	$webmaster = logincheck();
    	$this->assign('title','校园参数配置');
    	$recordSchoolInfo = i_school_domain();
    	$SchoolSystem = M("SchoolSystem");
    	$recordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->select();
    	$this->assign('recordSchoolSystem',$recordSchoolSystem);
    	$lastrecordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->find();
    	$this->assign('lastrecordSchoolSystem',$lastrecordSchoolSystem);
    	$UserLogin = M("UserLogin");
    	$totalSchoolUsers = $UserLogin->where("school = $recordSchoolInfo[id]")->count();
    	
        /**
         * update system parameter
         */
        if ($this->isPost()) {
        	$index_user = trim(addslashes($_POST['index_user']));
        	$index_spread_info = trim(addslashes($_POST['index_spread_info']));
        	$image_index = trim(addslashes($_POST['image_index']));
        	$image_mobile = trim(addslashes($_POST['image_mobile']));
        	$about = trim(addslashes($_POST['about']));
        	
        	$newSchoolSystem = array(
        		'id' => '',
        		'sid' => $recordSchoolInfo['id'],
        		'total_users' => $totalSchoolUsers,
        		'index_user' => $index_user,
        		'index_spread_info' => $index_spread_info,
        		'about' => $about,
        		'image_index' => $image_index,
        		'image_mobile' => $image_mobile,
        		'time' => time()
        	);
        	$insertSchoolSystemId = $SchoolSystem->add($newSchoolSystem);
        	
        	/**
	         * webmaster user operating record
	         */
	        $SchoolRecord = M("SchoolRecord");
	        $newSchoolRecordData = array(
                'id' => '',
                'sys_id' => $insertSchoolSystemId,
                'uid' => $webmaster['uid'],
                'sid' => $recordSchoolInfo['id'],
                'record' => '修改配置参数',
                'time' => time()
	         
	        );
	        $SchoolRecord->add($newSchoolRecordData);
	        redirect('/schooladmin/parameter', 1, 'ok...');
        }
        $this->display();
    }
    
    public function indexbgimg()
    {
    	$webmaster = logincheck();
    	$this->assign('title','校园参数配置');
    	
    	$recordSchoolInfo = i_school_domain();
    	$schoolid = $recordSchoolInfo['id'];
    	$this->assign('schoolid',$schoolid);
    	
    	Vendor('Ihelpoo.Upyun');
        $upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
        $imageStorageUrl = image_storage_url();
    	if ($this->isPost()) {
    		if (!empty($_FILES)) {
    			if ($_FILES["uploadimage"]["error"] > 0) {
    				redirect('/schooladmin/indexbgimg', 3, 'file error...'.$_FILES["uploadimage"]["error"]);
    			} else {
    				$imageOldName = $_FILES["uploadimage"]["name"];
    				$imageType = $_FILES["uploadimage"]["type"];
    				$imageSize = $_FILES["uploadimage"]["size"];
    				$imageTmpName = $_FILES["uploadimage"]["tmp_name"];
    			}
    			
    			if ($imageSize > 800000) {
    				redirect('/schooladmin/indexbgimg', 3, 'error...上传图片太大, 最大能上传单张 3.5MB');
    			} else if ($imageType == 'image/jpeg') {
    				
    				/**
        			 * storage in upyun
        			 */
        			$fh = fopen($imageTmpName, 'rb');
        			$storageFilename = '/school/'.$schoolid.'/'.time().'.jpg';
        			$rsp = $upyun->writeFile($storageFilename, $fh, True);
        			fclose($fh);
        			$newfilepath = $imageStorageUrl.$storageFilename;
        			
        			/**
        			 * webmaster user operating record
        			 */
        			$SchoolRecord = M("SchoolRecord");
        			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '上传图片'.$newfilepath,
		                'time' => time()
        			);
        			$SchoolRecord->add($newSchoolRecordData);
        			redirect('/schooladmin/indexbgimg', 1, 'success...');
    			} else {
    				redirect('/schooladmin/indexbgimg', 3, 'error...上传图片格式错误, 目前仅支持.jpg');
    			}
    		}
    	}
    	
    	if (!empty($schoolid)) {
    		$imagestoragelist = $upyun->getList("/school/$schoolid/");
    		$this->assign('imagestoragelist',$imagestoragelist);
    		$this->assign('imagestorageurlfolder',$imageStorageUrl."/school/".$schoolid."/");
    	}
    	$this->display();
    }
    
    
    /**
     * user management
     */
    public function user()
    {
    	$webmaster = logincheck();
    	$this->assign('title', '用户管理');
        $UserLogin = M("UserLogin");
        $userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        $recordSchoolInfo = i_school_domain();
        
        /**
         * search
         */
        if (!empty($_POST['search'])) {
        	$searchWords = trim(addslashes(htmlspecialchars(strip_tags($_POST['search']))));
        	if (preg_match("/@/i", $searchWords)) {
        		$userLoginRecord = $UserLogin->where("email = '$searchWords'")->find();
        	} else if (preg_match("/[0-9]/", $searchWords) && $searchWords > 9999) {
        		$userLoginRecord = $UserLogin->where("uid = '$searchWords'")->find();
        	} else {
        		$userLoginRecord = $UserLogin->where("nickname like '%$searchWords%'")->find();
        	}
        	
        	/**
	         * webmaster user operating record
	         */
	        $SchoolRecord = M("SchoolRecord");
	        $newSchoolRecordData = array(
                'id' => '',
                'sys_id' => '',
                'uid' => $webmaster['uid'],
                'sid' => $recordSchoolInfo['id'],
                'record' => 'user search 搜索 searchWords:'.$searchWords,
                'time' => time()
	         
	        );
	        $SchoolRecord->add($newSchoolRecordData);
        	
        	if (!empty($userLoginRecord['uid'])) {
        		redirect('/schooladmin/user/'.$userLoginRecord['uid'], 0, 'ok...');
        	} else {
        		redirect('/schooladmin/user?uid=empty', 1, 'empty...');
        	}
        }

        if (!empty($_POST['password']) && !empty($_POST['uid'])) {
            $isUserExist = $UserLogin->find($_POST['uid']);
            if ($isUserExist['uid']) {
            	$setPassword = array(
            		'uid' => $isUserExist['uid'],
            	    'password' => md5($_POST['password']),
            	);
                $UserLogin->save($setPassword);
                
                /**
                 * webmaster user operating record
                 */
                $SchoolRecord = M("SchoolRecord");
                $newSchoolRecordData = array(
	                'id' => '',
	                'sys_id' => '',
	                'uid' => $webmaster['uid'],
	                'sid' => $recordSchoolInfo['id'],
	                'record' => 'user reset pw 重置密码. uid:'.$isUserExist['uid'].' password:'.md5($_POST['password']),
	                'time' => time()
                );
                $SchoolRecord->add($newSchoolRecordData);
                
                redirect('/schooladmin/user', 1, 'update user password ok...');
            }
        }

        if (!empty($_POST['recordlimit']) && !empty($_POST['uid'])) {
        	$UserStatus = M("UserStatus");
        	$newRecordLimit = (int)$_POST['recordlimit'];
            $isUserExist = $UserStatus->find($_POST['uid']);
            if ($isUserExist['uid']) {
            	$setRecordLimit = array(
            		'uid' => $isUserExist['uid'],
            	    'record_limit' => $newRecordLimit,
            	);
                $UserStatus->save($setRecordLimit);
                
                /**
                 * webmaster user operating record
                 */
                $SchoolRecord = M("SchoolRecord");
                $newSchoolRecordData = array(
	                'id' => '',
	                'sys_id' => '',
	                'uid' => $webmaster['uid'],
	                'sid' => $recordSchoolInfo['id'],
	                'record' => 'change user record limit. uid:'.$isUserExist['uid'].' record_limit:'.$newRecordLimit,
	                'time' => time()
                );
                $SchoolRecord->add($newSchoolRecordData);
                redirect('/schooladmin/user', 1, 'update user record limit ok...');
            }
        }
        
		if (!empty($_POST['type']) && !empty($_POST['uid'])) {
        	$newUserType = (int)$_POST['type'];
			$isUserExist = $UserLogin->find($_POST['uid']);
            if ($isUserExist['uid']) {
            	$setUserType = array(
            		'uid' => $isUserExist['uid'],
            	    'type' => $newUserType,
            	);
                $UserLogin->save($setUserType);
                
                /**
                 * webmaster user operating record
                 */
                $SchoolRecord = M("SchoolRecord");
                $newSchoolRecordData = array(
	                'id' => '',
	                'sys_id' => '',
	                'uid' => $webmaster['uid'],
	                'sid' => $recordSchoolInfo['id'],
	                'record' => 'change user type. uid:'.$isUserExist['uid'].' type:'.$newUserType,
	                'time' => time()
                );
                $SchoolRecord->add($newSchoolRecordData);
                redirect('/schooladmin/user', 1, 'update user type ok...');
            }
        }

        if (!empty($userId)) {
        	$recordUserLogin = $UserLogin->find($userId);
        	$this->assign('recordUserLogin',$recordUserLogin);
        	if ($recordUserLogin['school'] != $recordSchoolInfo['id']) {
        		redirect('/schooladmin/user', 1, '仅查询到其他学校用户，你无权管理...');
        	}
        	$UserInfo = M("UserInfo");
        	$recordUserInfo = $UserInfo->find($userId);
        	
        	/**
        	 * user album
        	 */
        	$UserAlbum = M("UserAlbum");
        	$userAlbumNums = $UserAlbum->where("uid = $userId")->count();
        	$userAlbumSize = $UserAlbum->where("uid = $userId")->sum('size');

        	/**
        	 * user msg
        	 */
        	$MsgSystem = M("MsgSystem");
        	$MsgComment = M("MsgComment");
        	$MsgActive = M("MsgActive");
        	$MsgAt = M("MsgAt");
        	$userMsgSystemNums = $MsgSystem->where("uid = $userId")->count();
        	$userMsgCommentNums = $MsgComment->where("uid = $userId")->count();
        	$userMsgActiveNums = $MsgActive->where("uid = $userId")->count();
        	$userMsgAtNums = $MsgAt->where("touid = $userId")->count();

        	/**
        	 * user talk
        	 */
        	$TalkContent = M("TalkContent");
        	$userTalkNums = $TalkContent->where("uid = $userId OR touid = $userId")->count();

        	/**
        	 * show
        	 */
        	$userOtherInfo = array(
        		'uid' => $recordUserLogin['uid'],
        		'nickname' => $recordUserLogin['nickname'],
        		'realname' => $recordUserInfo['realname'],
        		'userAlbumNums' => $userAlbumNums,
        		'userAlbumSize' => round($userAlbumSize/(1024*1024),2)."MB",
        		'userMsgSystemNums' => $userMsgSystemNums,
        		'userMsgCommentNums' => $userMsgCommentNums,
        		'userMsgActiveNums' => $userMsgActiveNums,
        		'userMsgAtNums' => $userMsgAtNums,
        		'userTalkNums' => $userTalkNums,
        	);
        	$this->assign('userOtherInfo',$userOtherInfo);
        }
        $this->display();
    }
    
    public function orderusericon()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','大家页面头像排序');
    	$UserLogin = M("UserLogin");
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
    	if ($this->isPost()) {
    		if (!empty($_POST['userid']) && !empty($_POST['way'])) {
    			$userid = $_POST['userid'];
    			$way = $_POST['way'];
    			$userLoginRecord = $UserLogin->find($userid);
    			$icon_fl = $userLoginRecord['icon_fl'];
    			if ($way == 'up') {
    				$icon_fl++;
    				if ($icon_fl > 5) {
    					$icon_fl = 5;
    				}
    				$newUserIconFlArray = array(
    					'uid' => $userLoginRecord['uid'],
    					'icon_fl' => $icon_fl,
    				);
    				$UserLogin->save($newUserIconFlArray);
    				
    				/**
        			 * webmaster user operating record
        			 */
        			$SchoolRecord = M("SchoolRecord");
        			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '修改头像排序 uid:'.$userLoginRecord['uid'].' +icon_fl:'.$icon_fl,
		                'time' => time()
        			);
        			$SchoolRecord->add($newSchoolRecordData);
    				$this->ajaxReturn(0,'排序提前('.$icon_fl.')','yes');
    			} else if ($way = 'down') {
    				$icon_fl--;
    				if ($icon_fl < 1) {
    					$icon_fl = 1;
    				}
    				$newUserIconFlArray = array(
    					'uid' => $userLoginRecord['uid'],
    					'icon_fl' => $icon_fl,
    				);
    				$UserLogin->save($newUserIconFlArray);
    				
    				/**
        			 * webmaster user operating record
        			 */
        			$SchoolRecord = M("SchoolRecord");
        			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '修改头像排序 uid:'.$userLoginRecord['uid'].' -icon_fl:'.$icon_fl,
		                'time' => time()
        			);
        			$SchoolRecord->add($newSchoolRecordData);
    				$this->ajaxReturn(0,'排序置后('.$icon_fl.')','yes');
    			}
    		}
    	}
    	$userLoginRecords = $UserLogin->where("school = $recordSchoolInfo[id]")->order("icon_fl DESC, logintime DESC")->limit($offset,$count)->select();
    	$this->assign('userLoginRecords',$userLoginRecords);
    	$totalusers = $UserLogin->where("school = $recordSchoolInfo[id]")->count();
    	$this->assign('totalusers',$totalusers);
    	$totalPages = ceil($totalusers / $count);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }
    
    public function realnameallowmf()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '用户实名修改');
        $AdminRealnamemf = M("AdminRealnamemf");
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        $recordAdminRealnamemf = $AdminRealnamemf->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_admin_realnamemf.uid")->order("time DESC")->limit($offset,$count)->select();
        $this->assign('adminRealnamemf',$recordAdminRealnamemf);

        /**
         * page link
         */
        $totalReocrdNums = $AdminRealnamemf->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_admin_realnamemf.uid")->count();
        $this->assign('totalRecordNums', $totalReocrdNums);
        $totalPages = ceil($totalReocrdNums / $count);
        $this->assign('totalPages', $totalPages);

        /**
         * post
         */
        if (!empty($_GET['sendmail'])) {
            $uid = $_GET['sendmail'];
            $UserLogin = M("UserLogin");
            $recordUserLogin = $UserLogin->find($uid);
            $toEmail = $recordUserLogin['email'];
            $toNickname = $recordUserLogin['nickname'];

            /**
             * send welcome email, do not throw exception
             */
            Vendor('Ihelpoo.Email');
            $emailObj = new Email();
            $isSend = $emailObj->realNameModifiedAllow($toEmail, $toNickname);
            if ($isSend) {

            	/**
                 * send system message.
                 */
                $MsgSystem = M("MsgSystem");
                $msgContent = "您的真实姓名可以修改了!";
                $msgData = array(
                    'id' => NULL,
                    'uid' => $uid,
                    'type' => 'setting/realfirst',
                    'content' => $msgContent,
                    'time' => time(),
                    'deliver' => 0,
                );
                $MsgSystem->add($msgData);

                /**
                 * update i_admin_realnalemf.allow
                 */
                $recordAdminRealnamemf = $AdminRealnamemf->where("uid = $uid AND allow != '1'")->find();
                $dataAf = array(
                	'id' => $recordAdminRealnamemf['id'],
                    'allow' => 1
                );
                $isUpdateAdminRealnamemf = $AdminRealnamemf->save($dataAf);

                /**
                 * update i_user_infoconn
                 */
                $UserInfo = M("UserInfo");
                $dataUserInfoconn = array(
                	'uid' => $uid,
                    'realname_re' => 0
                );
                $isUpdateUserInfo = $UserInfo->save($dataUserInfoconn);
                
                /**
                 * webmaster user operating record
                 */
                $SchoolRecord = M("SchoolRecord");
                $newSchoolRecordData = array(
		            'id' => '',
		            'sys_id' => '',
		            'uid' => $webmaster['uid'],
		            'sid' => $recordSchoolInfo['id'],
		            'record' => 'realnameallowmf 允许修改真实姓名. uid:'.$uid.' realname_re:0',
		            'time' => time()
                );
                $SchoolRecord->add($newSchoolRecordData);
                if ($isUpdateAdminRealnamemf && $isUpdateUserInfo) {
                	redirect('/schooladmin/realnameallowmf', 3, 'update user modify ok...');
                } else {
                	redirect('/schooladmin/realnameallowmf', 3, 'update info... isUpdateAdminRealnamemf:'.$isUpdateAdminRealnamemf.'; isUpdateUserInfo:'.$isUpdateUserInfo);
                }
            }
        }
        $this->display();
    }
    
    public function userhonor()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '授予荣誉奖励活跃');
        if (!empty($_POST['get_user_level'])) {
        	$UserLogin = M("UserLogin");
            $userAll = $UserLogin->where("school = $recordSchoolInfo[id]")->select();
            $userStrings = NULL;
            $i = 0;
            foreach ($userAll as $user) {
                $userLevel = i_degree($user['active']);
                if ($userLevel >= $_POST['get_user_level']) {
                	$userStrings .= $user['uid'].";";
                	$i ++;
                }
            }
            $userStrings = substr($userStrings, 0, -1);
            $this->assign('userStrings', $userStrings);
            $this->assign('totalUserNums', ">= level".$_POST['get_user_level']." 共".$i."人");
            
            /**
             * webmaster user operating record
             */
            $SchoolRecord = M("SchoolRecord");
            $newSchoolRecordData = array(
		        'id' => '',
		        'sys_id' => '',
		        'uid' => $webmaster['uid'],
		        'sid' => $recordSchoolInfo['id'],
		        'record' => 'userhonor get_user_level 查询. level:'.$_POST['get_user_level'].' '.$i.'人',
		        'time' => time()
            );
            $SchoolRecord->add($newSchoolRecordData);
        }

        if (!empty($_POST['get_user_info'])) {
        	$UserLogin = M("UserLogin");
        	$userArray = explode(";", $_POST['get_user_info']);
        	$userString = NULL;
        	foreach ($userArray as $user) {
        	    $userString .= $user.",";
        	}
        	$userString = substr($userString, 0, -1);

        	$userList = $UserLogin->where("i_user_login.uid in (".$userString.")")
        	->join('i_user_info ON i_user_info.uid = i_user_login.uid')
        	->join('i_op_academy ON i_user_info.academy_op = i_op_academy.id')
        	->join('i_op_specialty ON i_user_info.specialty_op = i_op_specialty.id')
        	->join('i_op_dormitory ON i_user_info.dormitory_op = i_op_dormitory.id')
        	->join('i_op_city ON i_user_info.city_op = i_op_city.id')
        	->field('i_user_login.uid,email,nickname,sex,birthday,enteryear,ip,logintime,realname,mobile,qq,weibo,i_op_academy.name as nameacademy,i_op_specialty.name as namespecialty,i_op_dormitory.name as namedormitory,i_op_city.name as namecity')
        	->select();
        	$this->assign('userList', $userList);
        	
        	/**
             * webmaster user operating record
             */
            $SchoolRecord = M("SchoolRecord");
            $newSchoolRecordData = array(
		        'id' => '',
		        'sys_id' => '',
		        'uid' => $webmaster['uid'],
		        'sid' => $recordSchoolInfo['id'],
		        'record' => 'userhonor get_user_info 查询用户详细. userString:'.$userString,
		        'time' => time()
            );
            $SchoolRecord->add($newSchoolRecordData);
        }

        if (!empty($_POST['honor_content'])) {
            $UserHonor = M("UserHonor");
            $userArray = explode(";", $_POST['user_ids']);

            /**
             * message to owner
        	 */
        	$MsgSystem = M("MsgSystem");
            $msgSystemType = 'helprooter/userhonor';
            $contentToOwnerMsgSystem = "你获得了我帮圈圈荣誉，快来看看吧";
            $i = 0;
            foreach ($userArray as $user) {
                $data = array(
                    'id' => '',
                    'uid' => $user,
                    'content' => $_POST['honor_content'],
                    'time' => time()
                );
                $UserHonor->add($data);

                /**
                 * insert into system message
                 */
                $diffusionToOwnerData = array(
        	        'id' => '',
        	        'uid' => $user,
        	        'type' => $msgSystemType,
        	        'url_id' => $user,
        	        'from_uid' => '',
        	        'content' => $contentToOwnerMsgSystem,
        	        'time' => time(),
        	        'deliver' => 0,
                );
        	    $MsgSystem->add($diffusionToOwnerData);
        	    $i++;
            }
        	
        	/**
             * webmaster user operating record
             */
            $SchoolRecord = M("SchoolRecord");
            $newSchoolRecordData = array(
		        'id' => '',
		        'sys_id' => '',
		        'uid' => $webmaster['uid'],
		        'sid' => $recordSchoolInfo['id'],
		        'record' => '授予我帮圈圈荣誉: 共'.$i.'人, content'.$_POST['honor_content'],
		        'time' => time()
            );
            $SchoolRecord->add($newSchoolRecordData);
            redirect('/schooladmin/userhonor', 3, '授予荣誉成功 共'.$i.'人...');
        }
        
        if (!empty($_POST['active_nums']) && !empty($_POST['active_reason'])) {
        	$activeNums = $_POST['active_nums'];
        	$activeReason = $_POST['active_reason'];
        	if ($activeNums > 200) {
        		redirect('/schooladmin/userhonor', 3, '“活跃”最高只能一次奖励200');
        	}
        	
        	/**
        	 * msg active
        	 */
        	$MsgActive = M("MsgActive");
        	$UserLogin = M("UserLogin");
            $userArray = explode(";", $_POST['user_ids']);

            /**
             * message to owner
        	 */
        	$MsgSystem = M("MsgSystem");
            $contentToOwnerMsgSystem = "你获得了我帮圈圈奖励的活跃 :)";
            $i = 0;
            foreach ($userArray as $user) {
            	$recordUserLogin = $UserLogin->find($user);
            	$recordUserLoginActive = $recordUserLogin['active'] == NULL ? 0 : $recordUserLogin['active'];
            	$userStatusData = array(
    				'uid' => $user,
	                'active' => $recordUserLoginActive + $activeNums,
    			);
    			$UserLogin->save($userStatusData);
            	
    			/**
                 * insert into msg active
                 */
            	$msgActiveArray = array(
					'id' => '',
					'uid' => $user,
					'total' => $recordUserLoginActive,
					'change' => $activeNums,
					'way' => 'add',
					'reason' => $activeReason,
					'time' => time(),
					'deliver' => 0,
            	);
            	$MsgActive->add($msgActiveArray);

                /**
                 * insert into system message
                 */
                $insertToOwnerData = array(
                    'id' => '',
                    'uid' => $user,
                    'type' => 'system',
                    'content' => $contentToOwnerMsgSystem,
                    'time' => time(),
                    'deliver' => 0,
                );
        	    $MsgSystem->add($insertToOwnerData);
        	    $i++;
            }
        	
        	/**
             * webmaster user operating record
             */
            $SchoolRecord = M("SchoolRecord");
            $newSchoolRecordData = array(
		        'id' => '',
		        'sys_id' => '',
		        'uid' => $webmaster['uid'],
		        'sid' => $recordSchoolInfo['id'],
		        'record' => '奖励我帮圈圈活跃: 共'.$i.'人, nums:'.$activeNums.', reason:'.$activeReason,
		        'time' => time()
            );
            $SchoolRecord->add($newSchoolRecordData);
            redirect('/schooladmin/userhonor', 3, '奖励我帮圈圈活跃 共'.$i.'人...');
        }
        $this->display();
    }
    
    public function userinvite()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '邀请用户 奖励');
    	$UserInvite = M("UserInvite");

    	if ($this->isPost()) {
    		$id = (int)$_POST['id'];
    		$award = (int)$_POST['award'];
    		$recordUserInvite = $UserInvite->find($id);
    		if (!empty($recordUserInvite['id'])) {
    			$updateInviteStatus = array(
    				'id' => $id,
    				'award' => $award,
    			);
    			$UserInvite->save($updateInviteStatus);

    			/**
    			 * send message system
    			 */
    			$MsgSystem = M("MsgSystem");
	            if ($award == 1) {
	            	$msgContent = "你邀请的用户被认定为有效，加活跃50";
	            	$UserLogin = M("UserLogin");
	            	$recordUserLogin = $UserLogin->find($recordUserInvite['uid']);

	            	/**
	                 * msg active
	                 */
	                $MsgActive = M("MsgActive");
	                $msgActiveArray = array(
		            	'id' => '',
		            	'uid' => $recordUserInvite['uid'],
		            	'total' => $recordUserLogin['active'],
		            	'change' => 50,
		            	'way' => 'add',
		            	'reason' => '成功邀请朋友加入我帮圈圈',
		            	'time' => time(),
		            	'deliver' => 0,
		            );
		            $MsgActive->add($msgActiveArray);
		            $updateUserLoginInfo = array(
                    	'uid' => $recordUserInvite['uid'],
                    	'active' => $recordUserLogin['active'] + 50,
                    );
                	$UserLogin->save($updateUserLoginInfo);
	            } else {
	            	$msgContent = "你邀请的用户无效:(，暂时不赠送活跃";
	            }
	            $msgData = array(
                	'id' => NULL,
                	'uid' => $recordUserInvite['uid'],
                 	'type' => 'rooter/userinvite',
              		'content' => $msgContent,
                	'time' => time(),
                	'deliver' => 0,
	            );
	            $MsgSystem->add($msgData);
	            
	            /**
	             * webmaster user operating record
	             */
	            $SchoolRecord = M("SchoolRecord");
	            $newSchoolRecordData = array(
			        'id' => '',
			        'sys_id' => '',
			        'uid' => $webmaster['uid'],
			        'sid' => $recordSchoolInfo['id'],
			        'record' => '邀请用户认定, uid:'.$recordUserInvite['uid'].', content:'.$msgContent,
			        'time' => time()
	            );
	            $SchoolRecord->add($newSchoolRecordData);
    			redirect('/schooladmin/userinvite', 1, 'ok...');
    		}
    	}

    	$page = i_page_get_num();
        $count = 20;
        $this->assign('count', $count);
        $offset = $page * $count;
        $resultsUserInvite = $UserInvite->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_user_invite.uid")->order("time DESC")->limit($offset,$count)->select();
        $this->assign('resultsUserInvite', $resultsUserInvite);

        $totalRecords = $UserInvite->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_user_invite.uid")->count();
    	$this->assign('totalRecords',$totalRecords);
    	$totalPages = ceil($totalRecords / $count);
        $this->assign('totalPages',$totalPages);

    	$this->display();
    }
    
    /**
     * record management
     */
    public function record()
    {
        $webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '信息管理');
        $RecordSay = M("RecordSay");
        if (!empty($_POST['recordid'])) {
        	$recordid = $_POST['recordid'];
        	$resultRecordSay = $RecordSay->where("sid = $recordid AND school_id = $recordSchoolInfo[id]")->find();
        	if (!empty($resultRecordSay[sid])) {
        		$this->assign('recordDelete', $resultRecordSay);
        	} else {
        		redirect('/schooladmin/record', 3, '没有找到相关记录或者你没有其他学校记录的删除权限...');
        	}
        	
        	/**
        	 * webmaster user operating record
        	 */
        	$SchoolRecord = M("SchoolRecord");
        	$newSchoolRecordData = array(
			    'id' => '',
			    'sys_id' => '',
			    'uid' => $webmaster['uid'],
			    'sid' => $recordSchoolInfo['id'],
			    'record' => '查询记录, sid:'.$_POST['recordid'].' content:'.$resultRecordSay['content'],
			    'time' => time()
        	);
        	$SchoolRecord->add($newSchoolRecordData);
        }
        if (!empty($_POST['sid'])) {
        	$sid = $_POST['sid'];
        	$RecordHelp = M("RecordHelp");
        	$RecordComment = M("RecordComment");
        	$RecordHelpreply = M("RecordHelpreply");
        	$RecordSay->where("sid = $sid")->delete();
        	$RecordComment->where("sid = $sid")->delete();
        	$RecordHelp->where("sid = $sid")->delete();
        	$RecordHelpreply->where("sid = $sid")->delete();
        	
        	/**
        	 * webmaster user operating record
        	 */
        	$SchoolRecord = M("SchoolRecord");
        	$newSchoolRecordData = array(
			    'id' => '',
			    'sys_id' => '',
			    'uid' => $webmaster['uid'],
			    'sid' => $recordSchoolInfo['id'],
			    'record' => '删除记录, sid:'.$sid,
			    'time' => time()
        	);
        	$SchoolRecord->add($newSchoolRecordData);
        	redirect('/schooladmin/record', 3, '删除记录成功 涉及4个表...');
        }
        $this->display();
    }

    
    /**
     * operating record
     */
    public function operatingrecord()
    {
    	$webmaster = logincheck();
    	$this->assign('title','站长操作记录');
    	$recordSchoolInfo = i_school_domain();
    	$SchoolRecord = M("SchoolRecord");
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
    	$recordsWebmasterUserrecord = $SchoolRecord->where("i_school_record.sid = $recordSchoolInfo[id]")->join('i_user_login ON i_user_login.uid = i_school_record.uid')->join('i_school_webmaster ON i_school_webmaster.uid = i_school_record.uid')->order("time DESC")->limit($offset,$count)->select();
    	$totalrecords = $SchoolRecord->where("sid = $recordSchoolInfo[id]")->count();
    	$this->assign('recordsWebmasterUserrecord',$recordsWebmasterUserrecord);
    	$this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
    	$this->display();
    }

}

?>