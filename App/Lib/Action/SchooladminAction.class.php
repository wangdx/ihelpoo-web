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
        		'userAlbumNums' => $userAlbumNums,
        		'userAlbumSize' => round($userAlbumSize/(1024*1024),2)."MB",
        		'userMsgSystemNums' => $userMsgSystemNums,
        		'userMsgCommentNums' => $userMsgCommentNums,
        		'userMsgActiveNums' => $userMsgActiveNums,
        		'userMsgAtNums' => $userMsgAtNums,
        		'userTalkNums' => $userTalkNums,
        	);
        	$this->assign('userOtherInfo',$userOtherInfo);

        	/**
        	 * user shop
        	 */
        	$UserShop = M("UserShop");
        	$recordUserShop = $UserShop->find($userId);
        	if (!empty($recordUserShop['uid'])) {
        		$this->assign('recordUserShop',$recordUserShop);
        	}
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