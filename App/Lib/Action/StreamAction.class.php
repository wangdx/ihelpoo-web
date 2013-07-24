<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class StreamAction extends Action {

    protected function _initialize()
    {
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$IUserLogin = D("IUserLogin");
    		$userloginedrecord = $IUserLogin->userExists($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	} else {
        	redirect('/user/notlogin', 0, '你还没有登录呢...');
        }
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index()
    {
    	$userloginid = session('userloginid');
    	$recordSchoolInfo = i_school_domain();
    	$title = "信息流 个人中心 - ".$recordSchoolInfo['school'];
    	
        $this->assign('title',$title);
        $this->assign('thisschoolid',$recordSchoolInfo['id']);
        
		$RecordSay = M("RecordSay");
		$UserLogin = M("UserLogin");
		$UserPriority = M("UserPriority");
		$UserStatus = M("UserStatus");
        $recordUserStatus = $UserStatus->find($userloginid);
        $this->assign('recordUserStatus',$recordUserStatus);
        
        $ISysParameter = D("ISysParameter");
        
        /**
         * post publish
         */
        if ($this->isPost()) {
        	$validate = array(
	        	array('textareacontent', 'require', '内容不能为空'),
	        	array('videourl', 'url', '视频地址格式错误',2),
	        	array('help_is', 'number', '信息类型错误'),
	        	array('authority', 'number', 'authority错误'),
	        	array('reward_coins', 'number', 'reward_coins格式错误'),
	        	array('verificationcode', 'number', '验证码格式错误'),
	        	array('schoolpublishid', 'number', '验证码格式错误'),
        	);
        	$RecordSay->setProperty("_validate", $validate);
        	$result = $RecordSay->create();
        	if (!$result) {
        		$publishError = $RecordSay->getError();
        		$this->ajaxReturn(0,$publishError,'error');
        	} else {
        		$content = i_makechickableLinks(trim(addslashes(strip_tags($_POST["textareacontent"],"<a>"))));
        		$imageurls = htmlspecialchars(strtolower(trim($_POST["imageurls"])));
        		$videourl = trim($_POST["videourl"]);
        		$atusers = htmlspecialchars(addslashes(strtolower(trim($_POST["atusers"]))));
        		$help_is = (int)htmlspecialchars(strtolower(trim($_POST["help_is"])));
        		$schoolpublishid = (int)htmlspecialchars(strtolower(trim($_POST["schoolpublishid"])));
        		$authority = (int)htmlspecialchars(strtolower(trim($_POST["authority"])));
        		$reward_coins = (int)htmlspecialchars(strtolower(trim($_POST["reward_coins"])));
        		$verificationcode = (int)htmlspecialchars(strtolower(trim($_POST["verificationcode"])));
        		$quietlyreleased = htmlspecialchars(strtolower(trim($_POST["quietlyreleased"])));
        		$groupmsgpush_system = htmlspecialchars(strtolower(trim($_POST["groupmsgpush_system"])));
        		$groupmsgpush_mail = htmlspecialchars(strtolower(trim($_POST["groupmsgpush_mail"])));
        		
        		$recordUserLogin = $UserLogin->where("uid = $userloginid")->field('uid,logintime,nickname,coins,active')->find();
        		if ($verificationcode == 999) {
        			
        			/**
        			 * show verificaation code
        			 * 999 is a mark number , when show this num, I defined that the system shut verification off;
        			 */
        			$verificationTimeRule = $ISysParameter->getParam("record_verifi_time");
        			 
        			/**
        			 * show verification code ; time/second low;
        			 */
        			$lastTwoRecord = $RecordSay->where("uid = $userloginid")->field("uid,sid,time,content")->order("time DESC")->limit(2)->select();
        			$timediffer = $lastTwoRecord[0]['time'] - $lastTwoRecord[1]['time'];
        			if ($timediffer < $verificationTimeRule['value']) {
        				/**
        				 * create virification code
        				 */
        				Vendor('Ihelpoo.Verifi');
        				$verifi = new Verifi();
        				$verifiString = $verifi->value_rand();
        				$_SESSION['verificationcode'] =  $verifiString['formula'];
        				$_SESSION['verificationresult'] =  $verifiString['result'];
        				$verificationTimeRuleAjaxReturn = array(
        					'last1Record' => $lastTwoRecord[0]['time'].$lastTwoRecord[0]['content'].$lastTwoRecord[0]['sid'],
        					'last2Record' => $lastTwoRecord[1]['time'].$lastTwoRecord[1]['content'].$lastTwoRecord[1]['sid'],
        					'timediffer' => $timediffer,
        					'verification Time' => $verificationTimeRule['value'],
        					'message' => '最后发布的两条信息时间间隔小于'.$verificationTimeRule['value'],
        				);
        				$this->ajaxReturn($verificationTimeRuleAjaxReturn,"请输入验证码",'verifi');
        			}
        			 
        			/**
        			 * show verification code ; nums/times low;
        			 */
        			$verificationNumsRule = $ISysParameter->getParam("record_verifi_nums");
        			$userInsertAll = $RecordSay->where("uid = $userloginid AND time > $recordUserLogin[logintime]")->order("time DESC")->count();
        			if ($userInsertAll >= $verificationNumsRule['value']) {

        				/**
        				 * create virification code
        				 */
        				Vendor('Ihelpoo.Verifi');
        				$verifi = new Verifi();
        				$verifiString = $verifi->value_rand();
        				$_SESSION['verificationcode'] =  $verifiString['formula'];
        				$_SESSION['verificationresult'] =  $verifiString['result'];
        				$verificationNumsRuleAjaxReturn = array(
        					'今天发布总数' => $userInsertAll,
        					'上线' => $verificationNumsRule['value'],
        					'message' => '一天发布总数超过上线'.$verificationNumsRule['value'],
        				);
        				$this->ajaxReturn($verificationNumsRuleAjaxReturn,"请输入验证码",'verifi');
        			}
        		} else {
        			if ($verificationcode != $_SESSION['verificationresult']) {
                        $this->ajaxReturn(0,"验证码错误",'error');
            	    }
        		}
        		
        		/**
        		 * record publish limit nums 6 per 12 hours
        		 */
        		$time12hour = time() - 43200;
        		$userInsertTime12hourAll = $RecordSay->where("uid = $userloginid AND time > $time12hour AND (say_type = 0 OR say_type = 1)")->order("time DESC")->count();
        		if ($userInsertTime12hourAll >= $recordUserStatus['record_limit']) {
        			$this->ajaxReturn(0,"发布信息太多，休息休息再来吧:)",'error');
        		}
        		
        		/**
        		 * handle coin nums
        		 */
        		if (($recordUserLogin['active'] - $reward_coins) < 0 && $help_is == 1) {
                    $this->ajaxReturn(0,"活跃不够了",'error');
                }
                
        		/**
        		 * insert data into database
        		 * image string handle
        		 */
        		$RecordOutimg = M("RecordOutimg");
        		$tempImageUrls = $imageurls;
        		$tempImageUrlsArray = explode("---", $tempImageUrls);
        		$tempImageUrlsArray = array_unique($tempImageUrlsArray);
        		$imageUrlString = '';
        		foreach ($tempImageUrlsArray as $tempImageUrlString) {
        			if (preg_match("/undefined/", $tempImageUrlString)) {
        				$imageUrlString;
        			} else if (preg_match("/ihelpooupload/", $tempImageUrlString)) {
        				$imageUrlString .= $tempImageUrlString.";";
        			} else {
        				$imageOutNewData = array(
                            'id' => '',
                            'uid' => $userloginid,
                            'rpath' => $tempImageUrlString,
                            'time' => time(),
        				);
        				$imageOutLastInsertId = $RecordOutimg->add($imageOutNewData);
        				$imageUrlString .= $imageOutLastInsertId.";";
        			}
        		}
        		if (!empty($imageUrlString)) {
        			$imageHandled = substr($imageUrlString, 0, -1);
        		} else {
        			$imageHandled = '';
        		}
        		
        		/**
        		 * get Browser Info
        		 */
        		Vendor('Ihelpoo.Browser');
        		$browserObj = new Browser();
        		$fromBrowser = $browserObj->getPlatform()." ".$browserObj->getBrowser()." ".$browserObj->getVersion();
        		$sayType = $help_is;
        		if ($quietlyreleased == 'on') {
        			$fromBrowser = '悄悄发布';
        			$sayType = '9';
        		}
        		
        		if ($groupmsgpush_system == 'on' || $groupmsgpush_mail == 'on') {
        			$fromBrowser = '组织推送';
        		}
        		
        		$dataRecordSay = array(
                    'sid' => NULL,
                    'uid' => $userloginid,
                    'say_type' => $sayType,
                    'content' => $content,
                    'image' => $imageHandled,
                    'url' => $videourl,
                    'authority' => $authority,
                    'time' => time(),
                    'last_comment_ti' => time(),
                    'from' => $fromBrowser,
                    'school_id' => $schoolpublishid
                );
                $sayLastInsertId = $RecordSay->add($dataRecordSay);
                
                /**
                 * send mail
                 */
				Vendor('Ihelpoo.Email');
				$emailObj = new Email();
				
                /**
                 * push group msg
                 */
                if ($groupmsgpush_system == 'on' || $groupmsgpush_mail == 'on') {
                	$userGroupPrioritied = $UserPriority->where("pid = $userloginid")->select();
                    if (!empty($userGroupPrioritied)) {
                    	$MsgSystem = M("MsgSystem");
                        foreach ($userGroupPrioritied as $userPrio) {
                        	if ($groupmsgpush_system == 'on') {
	                            $msgSystemType = 'stream/i-para:groupmsgpush';
	                            $contentMsgSystem = "组织有新的消息通知你!";
	        	    	        $pushMsgData = array(
	        	    	            'id' => '',
	        	    	            'uid' => $userPrio['uid'],
	        	    	            'type' => $msgSystemType,
	        	    	            'url_id' => $sayLastInsertId,
	        	    	            'from_uid' => $userloginid,
	        	    	            'content' => $contentMsgSystem,
	        	    	            'time' => time(),
	        	    	            'deliver' => 0,
	        	    	        );
	        	    	        $MsgSystem->add($pushMsgData);
                        	}
                        	if ($groupmsgpush_mail == 'on') {
                        		$groupPushEmailUser = $UserLogin->find($userPrio['uid']);
                        		
                        		/**
				    			 * send emil
				    			 */
                        		if (!empty($groupPushEmailUser['email'])) {
				    				$emailObj->groupMessagePush($groupPushEmailUser['email'], $groupPushEmailUser['nickname'], $recordUserLogin['nickname'], $content);
                        		}
                        	}
        		        }
                    }
                }
                
        		/**
        		 * calculate active
        		 */
                if ($recordUserStatus['active_s_limit'] < 3) {
                	$recordUserLoginActive = $recordUserLogin['active'] + 3;
                	$recordUserLogin['active'] = $recordUserLogin['active'] == NULL ? 0 : $recordUserLogin['active'];
                	
                	/**
	                 * msg active
	                 */
	                $MsgActive = M("MsgActive");
	                $msgActiveArray = array(
		            	'id' => '',
		            	'uid' => $userloginid,
		            	'total' => $recordUserLogin['active'],
		            	'change' => 3,
		            	'way' => 'add',
		            	'reason' => '写新记录或求助 (每天最多加3次)',
		            	'time' => time(),
		            	'deliver' => 0,
		            );
		            $MsgActive->add($msgActiveArray);
                } else {
                	$recordUserLoginActive = $recordUserLogin['active'];
                }
                
                /**
                 * update add active limit
                 */
                $updateUserStatusInfo = array(
                    'uid' => $userloginid,
                    'active_s_limit' => $recordUserStatus['active_s_limit'] + 1,
                );
                $UserStatus->save($updateUserStatusInfo);
                
                /**
                 * insert into i_record_help 
                 */
                if ($help_is) {
                	$RecordHelp = M("RecordHelp");
                	
                	/**
                     * if is help insert system_msg to fans
                     */
                	$userPrioritied = $UserPriority->where("pid = $userloginid")->select();
                    if (!empty($userPrioritied)) {
                    	$MsgSystem = M("MsgSystem");
                        foreach ($userPrioritied as $userPrio) {
                            $msgSystemType = 'stream/ih-para:needhelp';
                            $contentMsgSystem = "有困难了，需要你的帮助";
        	    	        $needHelpData = array(
        	    	            'id' => '',
        	    	            'uid' => $userPrio['uid'],
        	    	            'type' => $msgSystemType,
        	    	            'url_id' => $sayLastInsertId,
        	    	            'from_uid' => $userloginid,
        	    	            'content' => $contentMsgSystem,
        	    	            'time' => time(),
        	    	            'deliver' => 0,
        	    	        );
        	    	        $MsgSystem->add($needHelpData);
        	    	        
        	    	        /**
				    		 * send emil
				    		 */
           //              	$userPrioritiedMail = $UserLogin->find($userPrio['uid']);
           //              	if (!empty($userPrioritiedMail['email'])) {
				    			// $emailObj->helpstatusNeed($userPrioritiedMail['email'], $userPrioritiedMail['nickname'], $recordUserLogin['nickname'], $content);
           //              	}
        		        }
                    }
                    
                    $helpData = array(
                        'hid' => '',
                        'sid' => $sayLastInsertId,
                        'reward_coins' => $reward_coins,
                        'status' => 1,
                    );
                    $helpLastInsertId = $RecordHelp->add($helpData);
                    
                    /**
                     * 1.Add coin records into i_user_coins
                     * 2.Calculate coins use left
                     * 3.Encryption records
                     */
                    if (!empty($reward_coins)) {
                    	$MsgActive = M("MsgActive");
			            $msgActiveArray = array(
							'id' => '',
							'uid' => $userloginid,
							'total' => $recordUserLoginActive,
							'change' => $reward_coins,
							'way' => 'min',
							'reason' => '求帮助使用',
							'time' => time(),
							'deliver' => 0,
			            );
			            $MsgActive->add($msgActiveArray);
                    }
                    
                    /**
                     *
                     */
                    $updateUserLoginInfo = array(
                    	'uid' => $userloginid,
                    	'active' => $recordUserLoginActive - $reward_coins,
                    );
                } else {
                	$updateUserLoginInfo = array(
                    	'uid' => $userloginid,
                    	'active' => $recordUserLoginActive,
                    );
                }
                $UserLogin->save($updateUserLoginInfo);
                
        		/**
        		 * at user info
        		 * insert into i_msg_at
        		 */
                if (!empty($atusers)) {
        			$MsgAt = M("MsgAt");
        			$atUserTempArray = explode(",", $atusers);
        			foreach ($atUserTempArray as $atUser) {
        				$atUser = substr($atUser, 1);
        				$atUserRecord = $UserLogin->where("nickname = '$atUser'")->field('uid,nickname')->find();
        				$newAtMsgData = array(
			       			'id' => '',
			       			'touid' => $atUserRecord['uid'],
			       			'fromuid' => $userloginid,
		       				'sid' => $sayLastInsertId,
			       			'hid' => $helpLastInsertId,
			       			'time' => time(),
			       			'deliver' => 0
        				);
        				$MsgAt->add($newAtMsgData);
        			}
                }
        		$this->ajaxReturn(0,"发布成功",'ok');
        	}
        }
        
        if (preg_match("/priority/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "priority";
        } else if (preg_match("/shield/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "shield";
        } else if (preg_match("/time/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "time";
        } else if (preg_match("/index\/help/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "help";
        } else if (preg_match("/index\/group/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "group";
        } else if (preg_match("/index\/specialty/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "specialty";
        } else {
            $requestWay = "default";
        }
        
        /**
         * 
         * show $count records every page
         * $count int
         * $offect int Equal current page * count
         */
        $page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
        
        /**
         * 
         * priority set; rules by i_user_priority
         */
        $pidString = NULL;
        $allIdString = NULL;
        $pidGroupArray = array();
        $isSetPriority = $UserPriority->where("uid = $userloginid")->select();
        if (!empty($isSetPriority)) {
            foreach ($isSetPriority as $priorityRecord) {
                if (!empty($priorityRecord['pid'])) {
                    $pidString .= $priorityRecord['pid'].",";
                    $allIdString .= $priorityRecord['pid'].",";
                    
                    /**
                     * is set group priority
                     */
                    if ($priorityRecord['pid_type'] == 2) {
                    	$pidGroupArray[] = $UserLogin->where("uid = $priorityRecord[pid]")->field('uid,nickname')->find();
                    }
                } else if (!empty($priorityRecord['sid'])) {
                    $sidString .= $priorityRecord['sid'].",";
                    $allIdString .= $priorityRecord['sid'].",";
                }
            }
        }
        
        $select = $RecordSay;
        if ($requestWay == "priority") {
            if (!empty($pidString)) {
                $pidString = substr($pidString, 0, -1);
                $select->where("i_record_say.uid IN ($pidString) AND say_type != '9'");
        	} else {
        		$select->where("say_type != '9'");
        	}
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "priority";
        } else if ($requestWay == "shield") {
            if (!empty($sidString)) {
        	    $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid IN ($sidString) AND say_type != '9'");
        	} else {
        		$select->where("say_type != '9'");
        	}
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "shield";
        } else if ($requestWay == "time") {
            if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
        	} else {
        		$select->where("say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
        	}
        	$select->order('i_record_say.time DESC');
        	$streamway = "time";
        } else if ($requestWay == "help") {
            if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type = '1' AND i_record_say.school_id = $recordSchoolInfo[id]");
        	} else {
        	    $select->where("say_type = '1' AND i_record_say.school_id = $recordSchoolInfo[id]");
        	}
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "help";
        } else if ($requestWay == "group") {
        	$groupUid = (int)trim($_GET["_URL_"][3]);
        	$isSetGroupListPriority = $UserPriority->where("pid = $groupUid")->select();
        	$pidGroupString = NULL;
        	$pidGroupNums = 0;
	        if (!empty($isSetGroupListPriority)) {
	            foreach ($isSetGroupListPriority as $priorityRecord) {
	                if (!empty($priorityRecord['uid'])) {
	                    $pidGroupString .= $priorityRecord['uid'].",";
	                    $pidGroupNums++;
	                }
	            }
	        } else {
	        	redirect('/stream', 3, '组织成员为空 3秒后页面跳转...');
	        }
	        $pidGroupString = substr($pidGroupString, 0, -1);
        	$select->where("i_record_say.uid IN ($pidGroupString) AND say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "group";
        	$this->assign('groupUserNums',$pidGroupNums);
        	$groupUserRecord = $UserLogin->find($groupUid);
        	$this->assign('groupUserRecord',$groupUserRecord);
        } else if ($requestWay == "specialty") {
        	$specialtyId = (int)trim($_GET["_URL_"][3]);
        	//$isSetGroupListPriority = $UserPriority->where("pid = $groupUid")->select();
        	$UserInfo = M("UserInfo");
        	$allSameSpecialtyUsers = $UserInfo->where("specialty_op = $specialtyId")->select();
        	$allUserString = NULL;
        	$allUserNums = 0;
	        if (!empty($allSameSpecialtyUsers)) {
	            foreach ($allSameSpecialtyUsers as $specialtyUsers) {
	                if (!empty($specialtyUsers['uid'])) {
	                    $allUserString .= $specialtyUsers['uid'].",";
	                    $allUserNums++;
	                }
	            }
	        } else {
	        	redirect('/stream', 3, '还没有就读改专业的同学 3秒后页面跳转...');
	        }
	        $allUserString = substr($allUserString, 0, -1);
        	$select->where("i_record_say.uid IN ($allUserString) AND say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "specialty";
        	$this->assign('groupUserNums',$allUserNums);
        	$OpSpecialty = M("OpSpecialty");
        	$recordOpSpecialty = $OpSpecialty->where("id = $specialtyId")->find();
        	$this->assign('specialtyName',$recordOpSpecialty['name']);
        	$this->assign('specialtyId',$specialtyId);
        	$this->assign('academyId',$recordOpSpecialty['academy']);
        } else {
        	if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
        	} else {
        		$select->where("say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
        	}
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "default";
        }
        $recordSay = $select->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->join('i_user_info ON i_record_say.uid = i_user_info.uid')
        ->join('i_op_specialty ON i_user_info.specialty_op = i_op_specialty.id')
        ->join('i_school_info ON i_user_login.school = i_school_info.id')
        ->join("i_user_remark ON i_record_say.uid = i_user_remark.ruid AND $userloginid = i_user_remark.uid")
		->field('sid,i_user_login.uid,say_type,content,image,url,i_user_login.school,comment_co,diffusion_co,hit_co,i_record_say.time,from,last_comment_ti,school_id,nickname,sex,birthday,enteryear,type,online,active,icon_url,i_user_info.specialty_op,i_op_specialty.name,i_op_specialty.academy,i_school_info.id,i_school_info.school as schoolname,i_school_info.domain,i_school_info.domain_main,i_user_remark.remark')
		->limit($offset,$count)->select();
		$userRecordSayUidBefore = NULL;
		foreach ($recordSay as $record) {
			$schooldomain = $record['domain_main'] == NULL ? $record['domain'] : $record['domain_main'];
			if ($userRecordSayUidBefore == $record['uid']) {
				$recordSayArray[] = array(
					'sid' => $record['sid'],
					'uid' => $record['uid'],
					'say_type' => $record['say_type'],
					'content' => stripslashes($record['content']),
					'image' => $record['image'],
					'url' => $record['url'],
					'school' => $record['school'],
					'school_record' => $record['school_id'],
					'comment_co' => $record['comment_co'],
					'diffusion_co' => $record['diffusion_co'],
					'hit_co' => $record['hit_co'],
					'time' => $record['time'],
					'from' => $record['from'],
					'last_comment_ti' => $record['last_comment_ti'],
					'nickname' => $record['nickname'],
					'sex' => $record['sex'],
					'birthday' => $record['birthday'],
					'enteryear' => $record['enteryear'],
					'type' => $record['type'],
					'online' => $record['online'],
					'active' => $record['active'],
					'icon_url' => $record['icon_url'],
					'specialty_op' => $record['specialty_op'],
					'name' => $record['name'],
					'number' => $record['number'],
					'academy' => $record['academy'],
					'schoolname' => $record['schoolname'],
					'domain' => $schooldomain,
					'remark' => $record['remark'],
					'repatenums' => 1,
				);
			} else {
				$recordSayArray[] = array(
					'sid' => $record['sid'],
					'uid' => $record['uid'],
					'say_type' => $record['say_type'],
					'content' => stripslashes($record['content']),
					'image' => $record['image'],
					'url' => $record['url'],
					'school' => $record['school'],
					'school_record' => $record['school_id'],
					'comment_co' => $record['comment_co'],
					'diffusion_co' => $record['diffusion_co'],
					'hit_co' => $record['hit_co'],
					'time' => $record['time'],
					'from' => $record['from'],
					'last_comment_ti' => $record['last_comment_ti'],
					'nickname' => $record['nickname'],
					'sex' => $record['sex'],
					'birthday' => $record['birthday'],
					'enteryear' => $record['enteryear'],
					'type' => $record['type'],
					'online' => $record['online'],
					'active' => $record['active'],
					'icon_url' => $record['icon_url'],
					'specialty_op' => $record['specialty_op'],
					'name' => $record['name'],
					'number' => $record['number'],
					'academy' => $record['academy'],
					'schoolname' => $record['schoolname'],
					'domain' => $schooldomain,
					'remark' => $record['remark'],
					'repatenums' => 0
				);
			}
			$userRecordSayUidBefore = $record['uid'];
		}
		
        $this->assign('streamway',$streamway);
		$this->assign('recordSay',$recordSayArray);
		
		/**
		 * show new active nums
		 */
		$MsgActive = M("MsgActive");
		$msgActiveNewNums = $MsgActive->where("uid = $userloginid AND deliver = 0")->count();
		$this->assign('msgActiveNewNums',$msgActiveNewNums);
        
        /**
         * show user info
         */
		$UserInfo = M("UserInfo");
        $recordUserInfo = $UserInfo->find($userloginid);
        $this->assign('recordUserInfo',$recordUserInfo);
        
        /**
         * show online user nums
         */
        $IWebStatus = D("IWebStatus");
        $recordOnlineUserNums = $IWebStatus->paraExists('online_user_nums');
        $this->assign('onlineUserNums',$recordOnlineUserNums['valuechar']);
        
        /**
         * show user honor nums
         */
        $UserHonor = M("UserHonor");
        $totalUserHonorNums = $UserHonor->where("uid = $userloginid")->count();
        $this->assign('totalUserHonorNums',$totalUserHonorNums);
        
        /**
         * user group view
         */
        if (!empty($pidGroupArray)) {
        	$this->assign('pidGroupArray', $pidGroupArray);
        }
        
        /**
         * weibo
         */
        $UserLoginWb = M("UserLoginWb");
        $recordUserLoginWb = $UserLoginWb->where("uid = $userloginid")->find();
    	if (!empty($recordUserLoginWb['uid'])) {
            $this->assign('isAlreadyBind',$recordUserLoginWb);
        }
        
        /**
         * calculate online user numbers. calculate per 30 second
         */
        $IWebStatus = D("IWebStatus");
        $recordOnlineUserNums = $IWebStatus->paraExists('online_user_nums');
        $userOnlineObject = $UserLogin->where("online != 0")->join('i_user_status ON i_user_status.uid = i_user_login.uid')->join('i_user_info ON i_user_info.uid = i_user_login.uid')->select();
        if (15 < (time() - $recordOnlineUserNums['valueint'])) {
            foreach ($userOnlineObject as $userOnlineOne) {
                if (900 < (time() - $userOnlineOne['last_active_ti'])) {
                    $updateUserOnlineStatusData = array(
                    	'uid' => $userOnlineOne['uid'],
            	        'online' => 0,
            	    );
                    $UserLogin->save($updateUserOnlineStatusData);
                }
            }
        }
        $userOnlineNums = $UserLogin->where("online != 0")->count();
        $updateUserOnlineNums = array(
            'parameter' => $recordOnlineUserNums['parameter'],
            'valuechar' => $userOnlineNums,
            'valueint' => time(),
        );
        $IWebStatus->save($updateUserOnlineNums);
        
        /**
         * index_spread_info
         */
        $SchoolSystem = M("SchoolSystem");
        $recordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->find();
        $indexSpreadInfoVaule = $recordSchoolSystem['index_spread_info'];
        $this->assign('indexSpreadInfoVaule',$indexSpreadInfoVaule);
        
        $this->display();
    }
    
    /**
     *
     * u = user , user info
     */
    public function u()
    {
        $userloginid = session('userloginid');
        $recordSchoolInfo = i_school_domain();
        $uidView = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        if ($uidView <= 0 && !empty($userloginid)) {
        	$uidView = $userloginid;
        } else if ($uidView < 0) {
        	redirect('/stream', 3, '用户不存在呢 :(...');
        }
        $IUserLogin = D("IUserLogin");
        $UserInfo = M("UserInfo");
        $OpAcademy = M("OpAcademy");
        $OpSpecialty = M("OpSpecialty");
        
        $userView = $IUserLogin->userExists($uidView);
        $userViewUid = (int)$userView['uid'];
        if (empty($userViewUid)) {
            redirect('/stream', 3, '用户不存在呢 :(...');
        }
        $this->assign('title',$userView['nickname'].'的一些信息 '.$recordSchoolInfo['school']);
        $this->assign('userView', $userView);
        $SchoolInfo = M("SchoolInfo");
        $recordUserViewSchoolInfo = $SchoolInfo->find($userView['school']);
        $this->assign('thisschoolid', $recordSchoolInfo['id']);
        $this->assign('recordUserViewSchoolInfo', $recordUserViewSchoolInfo);
        
        /**
         * change skin 
         */
        $this->assign('changeskin', $userView['skin']);
        
        $userInfo = $UserInfo->find($userViewUid);
        $this->assign('userInfo', $userInfo);
        if (!empty($userInfo['academy_op'])) {
            $userAcademy = $OpAcademy->where("id = $userInfo[academy_op]")->find();
            $this->assign('userAcademy', $userAcademy);
        }
        if (!empty($userInfo['specialty_op'])) {
            $userSpecialty = $OpSpecialty->where("id = $userInfo[specialty_op]")->find();
            $this->assign('userSpecialty', $userSpecialty);
        }
        
        /**
         * priority & shield
         */
        $UserPriority = M("UserPriority");
        $isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $userViewUid")->find();
        if ($isPriorityExist) {
            $this->assign('priorityExist', 1);
        }
        $isShieldExist = $UserPriority->where("uid = $userloginid AND sid = $userViewUid")->find();
        if ($isShieldExist) {
            $this->assign('shieldExist', 1);
        }
        
        /**
         * user changeinfo
         */
        $UserChangeinfo = M("UserChangeinfo");
        $isUserChangeinfo = $UserChangeinfo->where("uid = $userViewUid AND withid = $userloginid");
        if ($isUserChangeinfo) {
            $this->assign('isChangeinfo', 1);
        }
        
        /**
         * user album
         */
        $UserAlbum = D("UserAlbum");
        $userIconAlbum = $UserAlbum->where("uid = $userViewUid AND type = 1 AND url != ''")->order("time DESC")->limit(6)->select();
        $this->assign('userIconAlbum',$userIconAlbum);
        
        /**
         * show user honor nums
         */
        $UserHonor = M("UserHonor");
        $totalUserHonorNums = $UserHonor->where("uid = $userViewUid")->count();
        $this->assign('totalUserHonorNums',$totalUserHonorNums);
        $this->display();
    }
    
    public function fav()
    {
    	$userloginid = session('userloginid');
        $this->assign('title','我的收藏');
        
        $page = i_page_get_num();
        $RecordFavourites = M("RecordFavourites");
        $count = 20;
        $this->assign('pageCount',$count);
        $offset = $page * $count;
        $recordFavouriteArray = $RecordFavourites->where("i_record_favourites.uid = $userloginid")
        ->join('i_record_say ON i_record_favourites.sid = i_record_say.sid')
        ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->field('id,i_user_login.uid,i_record_favourites.sid,i_record_favourites.time,nickname,sex,birthday,enteryear,type,online,active,icon_url,i_record_say.content')
        ->order("time DESC")->limit($offset,$count)->select();
        $this->assign('recordFavourites',$recordFavouriteArray);
        
        /**
         * page link
         */
        $totalFavouritesNums = $RecordFavourites->where("uid = $userloginid")->count();
        $this->assign('totalFavouritesNums',$totalFavouritesNums);
        $totalPages = ceil($totalFavouritesNums / $count);
        $this->assign('totalPages',$totalPages);
        if ($this->isPost()) {
        	$recordSayId = (int)htmlspecialchars(trim($_POST["sid"]));
        	if ($recordSayId) {
        		$favData = array(
            	    'id' => NULL,
            	    'uid' => $userloginid,
            	    'sid' => $recordSayId,
            	    'time' => time(),
        		);
        		$isFavourite = $RecordFavourites->where("sid = $recordSayId AND uid = $userloginid")->find();
        		if (!empty($isFavourite['id'])) {
        			$this->ajaxReturn(0,"您已经收藏过这条信息了",'info');
        		} else {
        			$affected = $RecordFavourites->add($favData);
        			if ($affected) {
        				$this->ajaxReturn(1,"收藏成功",'yes');
        			} else {
        				$this->ajaxReturn(0,"收藏失败",'wrong');
        			}
        		}
        	}
        }
        
        /**
         * need change to ajax
         */
        if (!empty($_GET['del'])) {
        	$sid = (int)$_GET['del'];
            $deleteaffected = $RecordFavourites->where("sid = $sid AND uid = $userloginid")->delete();
            if (!$deleteaffected) {
                redirect('/stream/fav', 2, '删除出错啦...');
            }
            redirect('/stream/fav', 1, '删除成功...');
        }
        $this->display();
    }
    
    public function ajax()
    {
        $userloginid = session('userloginid');
        if (!empty($_POST['diffusionSid'])) {
        	$RecordDiffusion = M("RecordDiffusion");
        	$diffusionSidArray = explode("-", $_POST['diffusionSid']);
        	$isDiffusion = $RecordDiffusion->where("uid = $userloginid AND $diffusionSidArray[1] = sid")->find();
        	if (!empty($isDiffusion['id'])) {
        		echo "你已经扩散了这条信息";
        	} else {
        		
        		/**
        		 * record diffusion limit nums 5 per 12 hours
        		 */
                //TODO for test temporarily
//        		$time12hour = time() - 43200;
//        		$userDiffusion12hourAll = $RecordDiffusion->where("uid = $userloginid AND time > $time12hour")->order("time DESC")->count();
//        		if ($userDiffusion12hourAll >= 3) {
//        			echo "你扩散了太多消息，休息休息再来吧 :)";
//        			echo "<br />每12小时最多扩散3条";
//        			exit();
//        		}
        		
        		/**
        		 * insert diffusion record
        		 */
        		$dataDiffusion = array(
	        	    'id' => '',
	        	    'uid' => $userloginid,
	        	    'sid' => $diffusionSidArray[1],
                    'assess_id' => 1,
	        	    'time' => time(),
        		);
        		$diffusionId = $RecordDiffusion->add($dataDiffusion);

                $port = 9998;
                $port_wr = 9999;
                $dbname = 'test';
                $table = 'user';
                //GET
                $hs = new HandlerSocket(C('MYSQL_SLAVE'), $port);
                if (!($hs->openIndex(1, $dbname, $table, HandlerSocket::PRIMARY, 'user_id,user_name,user_email,created')))
                {
                    echo $hs->getError(), PHP_EOL;
                    die();
                }

                $retval = $hs->executeSingle(1, '>=', array('0'), 10, 0);

                var_dump($retval);

                $retval = $hs->executeMulti(
                    array(array(1, '=', array('1'), 1, 0),
                        array(1, '=', array('2'), 1, 0)));

                var_dump($retval);

                unset($hs);

//                $hs = new HandlerSocket(C('MYSQL_MASTER'), C('HS_PORT_WR'));
//                if (!($hs->openIndex(3, 'test', 'user', '', 'a, b')))
//                {
//                    echo $hs->getError(), PHP_EOL;
//                    die();
//                }
//                if ($hs->executeInsert(3, array('a1111','b1111')) === false)
//                {
//                    echo $hs->getError(), PHP_EOL;
//                }
//                unset($hs);


                /**
        		 * update diffusion_co nums
        		 */
        		$RecordSay = M("RecordSay");
        		$resuleRecordSay = $RecordSay->find($diffusionSidArray[1]);
        		
        		/**
                 * if user > level2 update last_comment_ti
                 */
                $UserLogin = M("UserLogin");
                $recordUserLogin = $UserLogin->find($userloginid);
                $userLevel = i_degree($recordUserLogin['active']);
                if ($userLevel >= 2) {
                    $recordSaySet = array(
                    	'sid' => $diffusionSidArray['1'],
        		        'diffusion_co' => $resuleRecordSay['diffusion_co'] + 1,
        		        'last_comment_ti' => time(),
        		    );
                } else {
                	$recordSaySet = array(
                		'sid' => $diffusionSidArray['1'],
        		        'diffusion_co' => $resuleRecordSay['diffusion_co'] + 1,
        		    );
                }
        		$RecordSay->save($recordSaySet);
        		
        		/**
        		 * message to owner
        		 */
        		$MsgSystem = M("MsgSystem");
        	    if ($diffusionSidArray['0'] == "ih") {
                    $msgSystemType = 'stream/ih-para:diffusiontoowner';
                } else {
                    $msgSystemType = 'stream/i-para:diffusiontoowner';
                }
                $contentToOwnerMsgSystem = "扩散了你的这条消息";
        	    $diffusionToOwnerData = array(
        	        'id' => '',
        	        'uid' => $resuleRecordSay['uid'],
        	    	'type' => $msgSystemType,
        	    	'url_id' => $diffusionSidArray['1'],
        	    	'from_uid' => $userloginid,
        	    	'content' => $contentToOwnerMsgSystem,
        	    	'time' => time(),
        	    	'deliver' => 0,
        	    );
        	    $MsgSystem->add($diffusionToOwnerData);

                $redismq = new Redis();
                $redismq->connect(C('REDIS_HOST'), C('REDIS_PORT'));


                /**
        		 * diffusion
        		 */
        	    $UserPriority = M("UserPriority");
        	    $userPriorityObj = $UserPriority->where("pid = $userloginid")->join("i_user_login ON i_user_priority.uid = i_user_login.uid")->select();
        	    $userPriorityNums = sizeof($userPriorityObj);


       	        echo $a."已经扩散给了 <a href='".__ROOT__."/mutual/priority?me'>你的圈子</a> 中的等<span class='f14 fb orange'>".$userPriorityNums."</span> 人...<br /><br />";
       	        if (!empty($userPriorityNums)) {
       	            $i = 0;
        	        foreach ($userPriorityObj as $userPriority) {
        	    	    if ($i < 10) {
        		            echo $userPriority['nickname']."<br />";
        		            $i++;
        	        	}

//                        $isReceivedDiffusionMsg = $redismq->hExists(C('R_MESSAGE').C('R_SYSTEM').$userPriority['uid'],"扩散了这条消息给你".":".$diffusionSidArray['1'].":"."0");
//                        $ids = $userloginid.",";
//                        if(!empty($isReceivedDiffusionMsg)){
//                            $ids.=explode(":", $redismq->hGet(C('R_MESSAGE').C('R_SYSTEM').$userPriority['uid'],"扩散了这条消息给你".":".$diffusionSidArray['1'].":"."0"))[0];
//                        }
//                        $redismq->hSet(C('R_MESSAGE').C('R_SYSTEM').$userPriority['uid'],"扩散了这条消息给你".":".$diffusionSidArray['1'].":"."0", $ids.":".time());

                        $redismq->hIncrBy(C('R_NOTICE').C('R_SYSTEM').substr($userPriority['uid'], 0, strlen($userPriority['uid']) - 3), substr($userPriority['uid'], -3), 1);
                        $redismq->hSet(C('R_ACCOUNT').$userPriority['uid'].C('R_MESSAGE'), $diffusionId, 0);

        	        	/**
       	                 * insert into sys_msg
       	                 */
//                        $diffusionsKey = "i_msg_system:diffusion:".$userPriority[uid].":0";
//        	        	$isReceivedDiffusionMsg = $redismq->hGet($diffusionsKey, "diffusionNum");//$MsgSystem->where("uid = $userPriority[uid] AND (type = 'stream/i-para:diffusion' OR type = 'stream/ih-para:diffusion') AND url_id = $diffusionSidArray[1] AND deliver = 0")->find();
//        	        	if (empty($isReceivedDiffusionMsg['id'])) {
//        	        		if ($diffusionSidArray['0'] == "ih") {
//        	        			$msgSystemType = 'stream/ih-para:diffusion';
//        	        		} else {
//        	        			$msgSystemType = 'stream/i-para:diffusion';
//        	        		}


//        	        		$contentMsgSystem = "扩散了这条消息给你";
//        	        		$diffusionData = array(
//	        	    	        'id' => '',
//	        	    	        'uid' => $userPriority['uid'],
//	        	    	        'type' => $msgSystemType,
//	        	    	        'url_id' => $diffusionSidArray['1'],
//	        	    	        'from_uid' => $userloginid,
//	        	    	        'content' => $contentMsgSystem,
//	        	    	        'time' => time(),
//	        	    	        'deliver' => 0,
//        	        		);

//                            $id = $redismq->incr("i_msg_system:id");
//                            $redismq->set("i_msg_system:".$id.":uid", $userPriority['uid']);
//                            $redismq->set("i_msg_system:".$id.":type", $msgSystemType);
//                            $redismq->set("i_msg_system:".$id.":url_id", $diffusionSidArray['1']);
//                            $redismq->set("i_msg_system:".$id.":from_uid", $userloginid);
//                            $redismq->set("i_msg_system:".$id.":content", $contentMsgSystem);
//                            $redismq->set("i_msg_system:".$id.":time", time());
//                            $redismq->set("i_msg_system:".$id.":deliver", 0);
//
//                            $redismq->hSet($diffusionsKey, "diffusionNum", 1);
//                            $redismq->hSet($diffusionsKey, "personsList", $userloginid);

//        	        		$MsgSystem->add($diffusionData);
//        	        	} else {
//                            $redismq->hIncrBy($diffusionsKey, "diffusionNum", 1);
//                            $persons = $redismq->hGet($diffusionsKey, "personsList");
//                            $redismq->hSet($diffusionsKey, "personsList", $persons.",".$userloginid);

//        	        		$dataMsgSystem = $isReceivedDiffusionMsg['from_uid'].','.$userloginid;
//        	        		$dataMsgSystemArray = explode(",", $dataMsgSystem);
//        	        		$dataMsgSystemNums = count($dataMsgSystemArray);
//        	        		$contentMsgSystem = "等 <span class='orange fb f14 msggetusers' value='"."' title='点击查看扩散详情'>".$redismq->hGet($diffusionsKey, "diffusionNum")."</span> 人扩散了这条消息给你";
//        	        		$diffusionData = array(
//	        	    	        'id' => $isReceivedDiffusionMsg['id'],
//	        	    	        'data' => $dataMsgSystem,
//	        	    	        'content' => $contentMsgSystem,
//        	        		);
//        	        		$MsgSystem->save($diffusionData);
//        	        	}
        	        }
        	        echo "...";
       	        }
        	}
            exit();
        }
    }

}

?>