<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class AjaxAction extends Action {

    protected function _initialize()
    {
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    	} else {
    		$this->ajaxReturn(0,'没有登录呢',0);
    	}
        header("Content-Type:text/html; charset=utf-8");
    }

    public function updatestatus(){
        $userloginid = session('userloginid');
        i_db_update_activetime($userloginid);
    }

    public function getmessage()
    {
    	if ($this->isPost()) {
    		$userloginid = session('userloginid');
    		$userStatusRecord = i_ajax_msg($userloginid);
    		$MsgAt = M("MsgAt");
    		$MsgComment = M("MsgComment");
    		$TalkContent = M("TalkContent");
            Vendor('Ihelpoo.Redismq');
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
    		$messageAtNums = $MsgAt->where("touid = $userloginid AND deliver = 0")->count();
    		$messageCommentNums = $MsgComment->where("uid = $userloginid AND deliver = 0")->count();
    		$messageSystemNums = $redis->hGet(C('R_NOTICE').C('R_SYSTEM').substr($userloginid, 0, strlen($userloginid) - 3), substr($userloginid, -3));//$MsgSystem->where("uid = $userloginid AND deliver = 0")->count();
    		$messageTalkNums = $TalkContent->where("touid = $userloginid AND deliver = 0")->count();
    		if (!empty($messageTalkNums)) {
    			$lastTalkContent = $TalkContent->where("touid = $userloginid AND deliver = 0")
    			->join("i_user_login ON i_user_login.uid = i_talk_content.uid")
    			->field("id,i_talk_content.uid,touid,content,deliver,nickname,icon_url")
    			->order("time DESC")
    			->find();
    		} else {
    			$lastTalkContent = NULL;
    		}
    		$returnData = array(
    			'messageAtNums' => $messageAtNums,
    			'messageCommentNums' => $messageCommentNums,
    			'messageSystemNums' => $messageSystemNums,
    			'messageTalkNums' => $messageTalkNums,
    			'lastTalkContent' => $lastTalkContent['content'],
    			'lastTalkContentUserNickname' => $lastTalkContent['nickname'],
    			'lastTalkContentUserImg' => i_icon_check($lastTalkContent['uid'],$lastTalkContent['icon_url']),
    			'lastTalkContentUserId' => $lastTalkContent['uid'],
    			'acquireTimes' => $userStatusRecord['acquire_times'],
    			'acquireSeconds' => $userStatusRecord['acquire_seconds'],
    		);
    		$this->ajaxReturn($returnData,'message ajax','ok');
    	}
    }

    /**
     *
     * Get talk content & Message info
     */
	public function gettalkmessage()
    {
    	if ($this->isPost()) {
    		$userloginid = session('userloginid');
    		$userStatusRecord = i_ajax_msg($userloginid);
    		$MsgAt = M("MsgAt");
    		$MsgComment = M("MsgComment");
    		$messageAtNums = $MsgAt->where("touid = $userloginid AND deliver = 0")->count();
    		$messageCommentNums = $MsgComment->where("uid = $userloginid AND deliver = 0")->count();
    		if ($userStatusRecord['acquire_seconds'] <= 6000) {
    			$userStatusRecordAcquireSeconds = $userStatusRecord['acquire_seconds'] / 2;
    		} else {
    			$userStatusRecordAcquireSeconds = $userStatusRecord['acquire_seconds'];
    		}
    		$returnData = array(
    			'messageAtNums' => $messageAtNums,
    			'messageCommentNums' => $messageCommentNums,
    			'messageSystemNums' => $messageSystemNums,
    			'acquireTimes' => $userStatusRecord['acquire_times'],
    			'acquireSeconds' => $userStatusRecordAcquireSeconds,
    			'newmessage' => '',
    			'content' => '',
    			'image' => '',
    		);

    		/**
    		 * talk message
    		 */
    		$postUid = $_POST['uid'];
    		$postTouid = $_POST['touid'];
    		if (!empty($postUid) && !empty($postTouid)) {
    			$UserLogin = M("UserLogin");
    			$recordUserLogin = $UserLogin->where("$postUid = uid")->find();
    			$toRecordUserLogin = $UserLogin->where("$postTouid = uid")->find();
    			if (empty($recordUserLogin['uid']) || empty($toRecordUserLogin['uid'])) {
    				$this->ajaxReturn($returnData,'message ajax, talk user is not exist','wrong');
    			}

    			/**
    			 * status_require_message
    			 */
    			$TalkContent = M("TalkContent");
    			$TalkInputstatus = M("TalkInputstatus");
    			if($_POST['way'] == "status_require_message") {
    				$freshTalkContent = $TalkContent->where("uid = $postUid AND touid = $postTouid AND deliver = 0")->order("time DESC")->find();
    				$otherNewTalkContent = $TalkContent->where("touid = $postTouid AND deliver = 0")->order("time DESC")->find();
    				$inputStatus = $TalkInputstatus->where("uid = $postUid AND touid = $postTouid")->find();

    				/**
    				 * update input status flag
    				 */
    				if ((time() - $inputStatus['time']) > 2) {

    					/**
    					 * update user input status
    					 * @param int $uid
    					 * @param int $touid
    					 * @param int $status 1 for input now; 0 for default
    					 */
    					function updateInputStatus($uid, $touid, $status)
    					{
    						$TalkInputstatus = M("TalkInputstatus");
    						$userInputStatus = $TalkInputstatus->where("uid = $uid AND touid = $touid")->find();
    						if ($userInputStatus) {
    							$data = array(
			    					'id' => $userInputStatus['id'],
					        	    'status' => $status,
					        	    'time' => time(),
    							);
    							$isOk = $TalkInputstatus->save($data);
    						} else {
    							$data = array(
					        	    'id' => '',
					        	    'uid' => $uid,
					        	    'touid' => $touid,
					        	    'status' => $status,
    							);
    							$isOk = $TalkInputstatus->add($data);
    						}
    						return $isOk;
    					}
    					updateInputStatus($postUid, $postTouid, '0');
    					$returnData['status_input'] == 'status0';
    					//$this->ajaxReturn($returnData,'message ajax, update input status 0','ok');
    				}

    				if (!empty($freshTalkContent['id'])) {

    					/**
    					 * update deliver flag
    					 */
    					$dataSend = array(
	                     	'id' => $freshTalkContent['id'],
	                        'deliver' => 1,
    					);
    					$TalkContent->save($dataSend);

    					/**
    					 * trans emotion
    					 */
    					Vendor('Ihelpoo.Emotion');
    					$emotionObj = new Emotion();
    					$returnData['nickname'] = $recordUserLogin['nickname'];
    					$returnData['time'] = date("H:i:s", $freshTalkContent['time']);
    					$returnData['content'] = $emotionObj->transEmotion($freshTalkContent['content']);
    					$returnData['image'] = $freshTalkContent['image'];
    					$returnData['imagethumb'] = i_image_thumbnail($freshTalkContent['image']);
    					$returnData['isinput'] = $inputStatus['status'];
    					$returnData['newmessage'] = '';
    					$this->ajaxReturn($returnData,'message ajax, return talk data : content','ok');
    				} else if (!empty($otherNewTalkContent['id'])) {

    					/**
    					 * require other's new message
    					 */
    					$returnData['content'] = '';
    					$returnData['newmessage'] = $otherNewTalkContent['uid'];
    					$this->ajaxReturn($returnData,'message ajax, return talk data : other new message','ok');
    				} else {
    					$returnData['content'] = '';
    					$returnData['newmessage'] = '';
    					$returnData['isinput'] = $inputStatus['status'];
    					$this->ajaxReturn($returnData,'message ajax, return talk data : input_status','ok');
    				}
    			}
    		}
    		$this->ajaxReturn($returnData,'message ajax','ok');
    	}
    }

    public function saveskin()
    {
    	if ($this->isPost()) {
    		$userloginid = session('userloginid');
    		if (empty($userloginid)) {
    			$this->ajaxReturn(0,"没有登录呢",'wrong');
    		} else {
    			$skinValue = (int)$_POST['skin_value'];
    			$UserLogin = M("UserLogin");
    			$recordUserLogin = $UserLogin->find($userloginid);
    			$userDegree = i_degree($recordUserLogin['active']);
    			if ($userDegree < 2 && $skinValue > 0) {
    				$this->ajaxReturn(0, "你的等级还不够哦，加油升级吧", "wrong");
    			} else if ($userDegree < 3 && $skinValue > 1) {
    				$this->ajaxReturn(0, "你的等级还不够哦，加油升级吧", "wrong");
    			} else if ($userDegree < 4 && $skinValue > 3) {
    				$this->ajaxReturn(0, "你的等级还不够哦，加油升级吧", "wrong");
    			} else if ($userDegree < 5 && $skinValue > 5) {
    				$this->ajaxReturn(0, "你的等级还不够哦，加油升级吧", "wrong");
    			} else {
	    			$skinData = array(
	    				'uid' => $userloginid,
	    				'skin' => $skinValue
	    			);
	    			$UserLogin->save($skinData);
	    			$this->ajaxReturn(0,"保存皮肤成功",'yes');
    			}
    		}
    	}
    }

    /**
     * header change online status
     */
    public function changeonlinestatus()
    {
    	if ($this->isPost()) {
    		$userloginid = session('userloginid');
    		if (empty($userloginid)) {
    			$this->ajaxReturn(0,"没有登录呢",'wrong');
    		} else {
    			$onlineValue = (int)$_POST['val_online'];
    			$IUserLogin = D("IUserLogin");
    			if ($onlineValue == 2) {
    				$newOnlineData = array(
		    			'uid' => $userloginid,
		    			'online' => 1,
    				);
    				$IUserLogin->save($newOnlineData);
    				$this->ajaxReturn(1,"已经切换为正常在线状态",'yes');
    			} else {
    				$newOnlineData = array(
		    			'uid' => $userloginid,
		    			'online' => 2,
    				);
    				$IUserLogin->save($newOnlineData);
    				$this->ajaxReturn(2,"已经切换为潜水状态",'yes');
    			}
    			$this->ajaxReturn(0,"处理错误",'wrong');
    		}
    	}
    }

    public function at()
    {
    	if ($this->isPost()) {
    		$userloginid = session('userloginid');
    		if (!empty($_POST['autofillatcontent'])) {
    			$UserLogin = M("UserLogin");
    			$autofillatcontent = addslashes(htmlspecialchars(strip_tags($_POST['autofillatcontent'])));
    			$autofillatcontentArray = explode("@", $autofillatcontent);
    			$atTempString = array_pop($autofillatcontentArray);
    			if (empty($atTempString)) {
    				$this->ajaxReturn(0,"空字符",'no');
    			}
    			$searchNameNicknameSql = "SELECT uid,nickname,icon_url FROM `i_user_login` WHERE `nickname` LIKE '%".$atTempString."%' LIMIT 10";
    			$fetchResultsUserLogin = $UserLogin->query($searchNameNicknameSql);
    			if (!empty($fetchResultsUserLogin)) {
    				foreach ($fetchResultsUserLogin as $tempUserLogin) {
    					$fetchResultsUserLoginArray[] = array(
    						'uid' => $tempUserLogin['uid'],
    						'nickname' => $tempUserLogin['nickname'],
    						'icon_url' => i_icon_check($tempUserLogin['uid'],$tempUserLogin['icon_url'],'s')
    					);
    				}
    				$this->ajaxReturn($fetchResultsUserLoginArray,"匹配用户列表",'ok');
    			} else {
    				$this->ajaxReturn(0,"没找到",'no');
    			}
    		}

    		if (!empty($_POST['autospacefillatcontent'])) {
    			$UserLogin = M("UserLogin");
    			$autospacefillatcontentArray = $_POST['autospacefillatcontent'];
    			if (is_array($autospacefillatcontentArray)) {
    				foreach ($autospacefillatcontentArray as $spacefillat) {
    					$spacefillat = substr($spacefillat, 1);
    					$searchNameNicknameSql = "SELECT uid,nickname,icon_url FROM `i_user_login` WHERE `nickname` = '".$spacefillat."' LIMIT 1";
    					$fetchResultsUserLogin = $UserLogin->query($searchNameNicknameSql);
    					if (!empty($fetchResultsUserLogin['0'])) {
	    					$fetchResultsUserLoginArray[] = array(
	    						'uid' => $fetchResultsUserLogin['0']['uid'],
	    						'nickname' => $fetchResultsUserLogin['0']['nickname'],
	    						'icon_url' => i_icon_check($fetchResultsUserLogin['0']['uid'],$fetchResultsUserLogin['0']['icon_url'],'s')
	    					);
    					}
    				}
    			} else {
    				$this->ajaxReturn(0,"空字符",'no');
    			}
    			if (!empty($fetchResultsUserLoginArray)) {
    				$this->ajaxReturn($fetchResultsUserLoginArray,"匹配用户列表",'ok');
    			} else {
    				$this->ajaxReturn(0,"没找到",'no');
    			}
    		}
    	}
    }

    public function videourl()
    {
    	if ($this->isPost()) {
    		if (!empty($_POST['videourlvalue'])) {
    			$url = $_POST['videourlvalue'];
    			Vendor('Ihelpoo.Videourlparser');
    			$result = VideoUrlParser::parse($url);
    			if (!$result) {
    				$this->ajaxReturn(0,"没找到",0);
    			} else {
    				$this->ajaxReturn($result,"ok",1);
    			}
    		}
    	}
    }

    public function getuserinfo()
    {
    	$userloginid = session('userloginid');
    	$recordSchoolInfo = i_school_domain();
    	if ($this->isPost()) {
    		if (!empty($_POST['userid']) || !empty($_POST['usernickname'])) {
    			$UserLogin = M("UserLogin");
    			if (!empty($_POST['userid'])) {
    				$userid = $_POST['userid'];
    			} else if (!empty($_POST['usernickname'])) {
    				$atUser = substr($_POST['usernickname'], 1);
        			$atUserRecord = $UserLogin->where("nickname = '$atUser'")->field('uid,nickname')->find();
        			if (!empty($atUserRecord)) {
        				$userid = $atUserRecord['uid'];
        			} else {
        				$this->ajaxReturn(0,"用户不存在",0);
        			}
    			}
    			
    			$UserInfo = M("UserInfo");
    			$SchoolInfo = M("SchoolInfo");
    			$OpAcademy = M("OpAcademy");
    			$OpSpecialty = M("OpSpecialty");
    			$OpDormitory = M("OpDormitory");
    			$UserRemark = M("UserRemark");
    			$recordUserLogin = $UserLogin->where("uid = $userid")->field('uid,nickname,sex,birthday,enteryear,type,online,active,icon_url,school')->find();
    			$resultUserRemark = $UserRemark->where("uid = $userloginid AND ruid = $userid")->find();
    			if (!$recordUserLogin['uid']) {
    				$this->ajaxReturn(0,"用户不存在",0);
    			} else {
    				$recordUserInfo =$UserInfo->where("uid = $userid")->field('uid,introduction,academy_op,specialty_op,dormitory_op,fans,follow')->find();
    				if ($recordUserInfo[academy_op])
    					$recordOpAcademy = $OpAcademy->where("id = $recordUserInfo[academy_op]")->find();
    				if ($recordUserInfo[specialty_op])
    					$recordOpSpecialty = $OpSpecialty->where("id = $recordUserInfo[specialty_op]")->find();
    				if ($recordUserInfo[dormitory_op])
    					$recordOpDormitory = $OpDormitory->where("id = $recordUserInfo[dormitory_op]")->find();
    				if ($recordUserLogin['type'] == 1) {
    					$userType = '个人';
    				} else if ($recordUserLogin['type'] == 2) {
    					$userType = '组织';
    				} else if ($recordUserLogin['type'] == 3) {
    					$userType = '商家';
    				}
    				$recordUserInfoFans = $recordUserInfo['fans'] == NULL ? 0:$recordUserInfo['fans'];
    				$recordUserInfoFollow = $recordUserInfo['follow'] == NULL ? 0:$recordUserInfo['follow'];
    				$recordUserInfoIntroduction = $recordUserInfo['introduction'] == NULL ? '':$recordUserInfo['introduction'];
    				$recordOpAcademyName = $recordOpAcademy['name'] == NULL ? '':$recordOpAcademy['name'];
    				$recordOpSpecialtyName = $recordOpSpecialty['name'] == NULL ? '':$recordOpSpecialty['name'];
    				$recordOpDormitoryName = $recordOpDormitory['name'] == NULL ? '':$recordOpDormitory['name'];
    				$recordUserInfoIntroduction = $recordUserInfo['introduction'] == NULL ? '':$recordUserInfo['introduction'];
    				
    				Vendor('Ihelpoo.Ofunction');
    				$ofunction = new Ofunction();
    				$resultUserRemark['remark'] =  $ofunction->cut_str($resultUserRemark['remark'], 5);
    				$resultUserRemarkremark = empty($resultUserRemark['remark']) ? NULL : $resultUserRemark['remark'];
    				$userInfoArray = array(
    					'uid' => $recordUserLogin['uid'],
    					'nickname' => $ofunction->cut_str($recordUserLogin['nickname'],8),
    					'remark' => $resultUserRemarkremark,
    					'sex' => $recordUserLogin['sex'],
    					'constellation' => i_constellation($recordUserLogin['birthday']),
    					'type' => $userType,
    					'online' => $recordUserLogin['online'],
    					'degree' => i_degree($recordUserLogin['active']),
    					'icon_url' => i_icon_check("$recordUserLogin[uid]", "$recordUserLogin[icon_url]", "s"),
    					'introduction' => $recordUserInfoIntroduction,
    					'academy' => $recordOpAcademyName,
    					'academy_id' => $recordOpAcademy['id'],
    					'specialty' => $recordOpSpecialtyName,
    					'specialty_id' => $recordOpSpecialty['id'],
    					'dormitory' => $recordOpDormitoryName,
    					'dormitory_id' => $recordOpDormitory['id'],
    					'fans' => $recordUserInfoFans,
    					'follow' => $recordUserInfoFollow,
    					'user_relation' => '',
    				);
    				
    				/**
    				 * domain
    				 */
    				$thisUserSchoolInfo = $SchoolInfo->find($recordUserLogin['school']);
    				$userInfoArray['domain'] = $thisUserSchoolInfo['domain_main'] == NULL ? $thisUserSchoolInfo['domain'] : $thisUserSchoolInfo['domain_main'];
    				$userInfoArray['domain'] = "http://".$userInfoArray['domain']."/";
    				
    				/**
    				 * school info
    				 */
    				if ($recordUserLogin['school'] != $recordSchoolInfo['id']) {
    					$userInfoArray['schoolname'] = $thisUserSchoolInfo['school'];
    				} else {
    					$userInfoArray['schoolname'] = NULL;
    				}
    				
    				/**
    				 *
    				 * user relation
    				 * quan or quaned (focus or follow)
    				 */
    				if (!empty($userloginid)) {
    					$UserPriority = M("UserPriority");
    					$isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $userid")->find();
    					if ($isPriorityExist) {
    						$userInfoArray['user_relation'] = "priority";
    					}
    					$isShieldExist = $UserPriority->where("uid = $userloginid AND sid = $userid")->find();
    					if ($isShieldExist) {
    						$userInfoArray['user_relation'] = "shield";
    					}
    				}
    				$this->ajaxReturn($userInfoArray,"ok",1);
    			}
    		}
    	}
    }
    
    public function plusview()
    {
        if (empty($_POST['sidString'])) {
            $this->ajaxReturn(0,'empty sid','error');
        }
        $sidStringArr = explode("-", $_POST['sidString']);
        $userloginid = session('userloginid');
        $sid = $sidStringArr['1'];
        $RecordPlus = M('RecordPlus');
        $resultsRecordPlus = $RecordPlus->where("sid = $sid")
            ->join("i_user_login ON i_record_plus.uid = i_user_login.uid")
            ->field("id,i_user_login.uid,i_record_plus.sid,i_record_plus.create_time,nickname,icon_url")
            ->limit(5)
            ->order("create_time DESC")
            ->select();
        if (!empty($resultsRecordPlus)) {
            $html = '<p class="stream_plus_users_p gray">他们赞过了这条信息</p>';
            $html .= '<ul class="stream_plus_users_ul">';
            foreach ($resultsRecordPlus as $recordPlus) {
                $html .= '<li>';
                $html .= '<a href="/wo/' . $recordPlus['uid'] . '" title="' . $recordPlus['nickname'] . '"><img src="' . i_icon_check($recordPlus['uid'], $recordPlus['icon_url'], 's') . '" height="25" class="radius3" /></a>';
                $html .= '</li>';
            }
            if ($sidStringArr['0'] == 'i') {
                $html .= '<li><a href="/item/say/' . $sid . '" class="f12">更多</a></li>';
            } else {
                $html .= '<li><a href="/item/help/' . $sid . '" class="f12">更多</a></li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p class="stream_plus_users_p">还没有人赞过这条信息，快来赞赞吧！</p>';
        }
        $this->ajaxReturn($html,'return html','yes');
    }
    
    public function newremark()
    {
    	$userloginid = session('userloginid');
    	if ($this->isPost()) {
    		$newuserid = (int)$_POST['newuserid'];
    		$newremarkname = trim(addslashes(strip_tags($_POST["newremarkname"])));
    		if (!empty($newuserid)) {
    			$UserRemark = M("UserRemark");
    			$recordUserRemark = $UserRemark->where("uid = $userloginid AND ruid = $newuserid")->find();
    			if (!empty($recordUserRemark['id'])) {
    				$updateUserRemark = array(
    					'id' => $recordUserRemark['id'],
    					'uid' => $userloginid,
    					'ruid' => $newuserid,
    					'remark' => $newremarkname,
    					'time' => time()
    				);
    				$UserRemark->save($updateUserRemark);
    				$this->ajaxReturn($updateUserRemark,"update remark ok",1);
    			} else {
    				$newUserRemark = array(
    					'id' => '',
    					'uid' => $userloginid,
    					'ruid' => $newuserid,
    					'remark' => $newremarkname,
    					'time' => time()
    				);
    				$UserRemark->add($newUserRemark);
    				$this->ajaxReturn($newUserRemark,"new remark ok",2);
    			}
    			$this->ajaxReturn(0,"remark error",0);
    		}
    	}
    }
    
    public function msggetusers()
    {
    	$userloginid = session('userloginid');
    	if ($this->isPost()) {
    		if (!empty($_POST['messagesystemid'])) {
    			$messagesystemid = (int)$_POST['messagesystemid'];
    			$MsgSystem = M("MsgSystem");
    			$recordMsgSystem = $MsgSystem->find($messagesystemid);
    			$recordMsgSystemData = explode(',', $recordMsgSystem['data']);
    			$UserLogin = M("UserLogin");
    			$userInfoArray = array();
    			foreach ($recordMsgSystemData as $recordMsgSystemDataIn) {
    				$recordUserLogin = $UserLogin->where("uid = $recordMsgSystemDataIn")->field('uid,nickname,icon_url')->find();
    				$recordUserLogin['icon'] = i_icon_check($recordUserLogin['uid'],$recordUserLogin['icon_url'],'s');
    				$userInfoArray[] = $recordUserLogin;
    			}
    			$this->ajaxReturn($userInfoArray,"ok",1);
    		}
    	}
    }


    public function todeliver()
    {
        if ($this->isPost()) {
            if (!empty($_POST['to'])) {
                $to = $_POST['to'];
                $TalkCountent = M("TalkContent");
                $updateData = array(
                    'deliver' => '1',
                );
                $TalkCountent->where("touid = $to")->save($updateData);
                $this->ajaxReturn(0,"ok",1);
            }
        }
    }

	public function imgupload()
    {
        if ($this->isPost()) {
        	$userloginid = session('userloginid');

        	/**
        	 * album default size controll
        	 */
        	$UserAlbum = M("UserAlbum");
        	$totalAlbumSize = $UserAlbum->where("uid = $userloginid")->sum('size');
        	$UserLogin = M("UserLogin");
        	$recordUserLogin = $UserLogin->find($userloginid);
        	$userLevel = i_degree($recordUserLogin['active']);
        	$totalAlbumDefaultSize = i_configure_album_size($userLevel);
        	if ($totalAlbumSize >= $totalAlbumDefaultSize) {
        		$this->ajaxReturn(0,'相册容量不够了,请联系我帮圈圈扩容','error');
        	}
    		if (!empty($_FILES)) {
    			if ($_FILES["uploadedimg"]["error"] > 0) {
    				$this->ajaxReturn(0,'上传图片失败, info'.$_FILES["uploadedimg"]["error"],'error');
    			} else {
    				$imageOldName = $_FILES["uploadedimg"]["name"];
    				$imageType = $_FILES["uploadedimg"]["type"];
    				$imageType = trim($imageType);
    				$imageSize = $_FILES["uploadedimg"]["size"];
    				$imageTmpName = $_FILES["uploadedimg"]["tmp_name"];
    				$imageOldNameArray = explode('.', $imageOldName);
    			}

    			/**
    			 * $tempRealSize = getimagesize($_FILES["uploadedimg"]["tmp_name"]);
    			 * $logoRealWidth = $tempRealSize['0'];
    			 * $logoRealHeight = $tempRealSize['1'];
    			 */
    			if ($imageSize > 3670016) {
    				$this->ajaxReturn(0,'上传图片太大, 最大能上传单张 3.5MB','error');
    			}  else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {
    				
    				/**
        			 * storage in upyun
        			 */
        			Vendor('Ihelpoo.Upyun');
        			$upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
        			$fh = fopen($imageTmpName, 'rb');
        			$fileName = 'recordsay'.time().'.'.$imageOldNameArray[1];
        			$storageTempFilename = '/useralbum/'.$userloginid.'/'.$fileName;
        			$rsp = $upyun->writeFile($storageTempFilename, $fh, True);
        			fclose($fh);
        			$imageStorageUrl = image_storage_url();
        			$newfilepath = $imageStorageUrl.$storageTempFilename;
        			 
        			$opts = array(
	        			UpYun::X_GMKERL_TYPE    => 'fix_max',
	        			UpYun::X_GMKERL_VALUE   => 150,
	        			UpYun::X_GMKERL_QUALITY => 95,
	        			UpYun::X_GMKERL_UNSHARP => True
        			);
        			$fh = fopen($imageTmpName, 'rb');
        			$storageThumbTempFilename = '/useralbum/'.$userloginid.'/thumb_'.$fileName;
        			$rsp = $upyun->writeFile($storageThumbTempFilename, $fh, True, $opts);
        			fclose($fh);
    				
        			/**
        			 * insert into i_user_album
        			 */
        			$UserAlbum = M("UserAlbum");
        			$newAlbumIconData = array(
        				'uid' => $userloginid,
        				'type' => 2,
        				'url' => $newfilepath,
        				'size' => $imageSize,
        				'time' => time()
        			);
        			$UserAlbum->add($newAlbumIconData);

        			/**
        			 * ajax return
        			 */
        			$this->ajaxReturn($newfilepath,'上传成功','uploaded');
    			} else {
    				$this->ajaxReturn(0,'上传图片格式错误, 目前仅支持.jpg .png .gif','error');
    			}
    		}
    		exit();
    	}
    }

	public function imgtalkupload()
    {
        if ($this->isPost()) {
        	$userloginid = session('userloginid');

        	/**
        	 * album default size controll
        	 */
        	$UserAlbum = M("UserAlbum");
        	$totalAlbumSize = $UserAlbum->where("uid = $userloginid")->sum('size');
        	$UserLogin = M("UserLogin");
        	$recordUserLogin = $UserLogin->find($userloginid);
        	$userLevel = i_degree($recordUserLogin['active']);
        	$totalAlbumDefaultSize = i_configure_album_size($userLevel);
        	if ($totalAlbumSize >= $totalAlbumDefaultSize) {
        		$this->ajaxReturn(0,'相册容量不够了,请联系我帮圈圈扩容','error');
        	}
    		if (!empty($_FILES)) {
    			if ($_FILES["uploadedimg"]["error"] > 0) {
    				$this->ajaxReturn(0,'上传图片失败, info'.$_FILES["uploadedimg"]["error"],'error');
    			} else {
    				$imageOldName = $_FILES["uploadedimg"]["name"];
    				$imageType = $_FILES["uploadedimg"]["type"];
    				$imageType = trim($imageType);
    				$imageSize = $_FILES["uploadedimg"]["size"];
    				$imageTmpName = $_FILES["uploadedimg"]["tmp_name"];
    				$imageOldNameArray = explode('.', $imageOldName);
    			}

    			/**
    			 * $tempRealSize = getimagesize($_FILES["uploadedimg"]["tmp_name"]);
    			 * $logoRealWidth = $tempRealSize['0'];
    			 * $logoRealHeight = $tempRealSize['1'];
    			 */
    			if ($imageSize > 3670016) {
    				$this->ajaxReturn(0,'上传图片太大, 最大能上传单张 3.5MB','error');
    			}  else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {
    				
    				/**
        			 * storage in upyun
        			 */
        			Vendor('Ihelpoo.Upyun');
        			$upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
        			$fh = fopen($imageTmpName, 'rb');
        			$fileName = 'talk'.time().'.'.$imageOldNameArray[1];
        			$storageTempFilename = '/useralbum/'.$userloginid.'/'.$fileName;
        			$rsp = $upyun->writeFile($storageTempFilename, $fh, True);
        			fclose($fh);
        			$imageStorageUrl = image_storage_url();
        			$newfilepath = $imageStorageUrl.$storageTempFilename;
        			 
        			$opts = array(
	        			UpYun::X_GMKERL_TYPE    => 'fix_max',
	        			UpYun::X_GMKERL_VALUE   => 150,
	        			UpYun::X_GMKERL_QUALITY => 95,
	        			UpYun::X_GMKERL_UNSHARP => True
        			);
        			$fh = fopen($imageTmpName, 'rb');
        			$storageThumbTempFilename = '/useralbum/'.$userloginid.'/thumb_'.$fileName;
        			$rsp = $upyun->writeFile($storageThumbTempFilename, $fh, True, $opts);
        			fclose($fh);
    				
        			/**
        			 * insert into i_user_album
        			 */
        			$UserAlbum = M("UserAlbum");
        			$newAlbumIconData = array(
        					'uid' => $userloginid,
        					'type' => 4,
        					'url' => $newfilepath,
        					'size' => $imageSize,
        					'time' => time()
        			);
        			$UserAlbum->add($newAlbumIconData);

        			/**
        			 * ajax return
        			 */
        			$this->ajaxReturn($newfilepath,'上传成功','uploaded');
    			} else {
    				$this->ajaxReturn(0,'上传图片格式错误, 目前仅支持.jpg .png .gif','error');
    			}
    		}
    	}
    }
    
    public function weiboswitch()
    {
        if ($this->isPost()) {
        	$userloginid = session('userloginid');
        	if (!empty($_POST['changeswitch'])) {
        		$changeswitch = $_POST['changeswitch'];
        		$UserLoginWb = M("UserLoginWb");
        		$recordUserLoginWb = $UserLoginWb->where("uid = $userloginid")->find();
        		if (!empty($recordUserLoginWb['uid'])) {
	        		if ($changeswitch == 'true') {
	        			$updateWbData = array(
	        				'uid' => $userloginid,
	        				'switch' => 1,
	        			);
	        		} else if ($changeswitch == 'false') {
	        			$updateWbData = array(
	        				'uid' => $userloginid,
	        				'switch' => 0,
	        			);
	        		}
	        		$UserLoginWb->save($updateWbData);
	        		$this->ajaxReturn(0,'chaneg switch ok','post');
        		}
        	}
        	$this->ajaxReturn(0,0,'post');
        }
    }
    
    /**
     * ajax quan
     */
    public function quanta()
    {
    	$userloginid = session('userloginid');
        $UserLogin = M("UserLogin");
        $UserInfo = M("UserInfo");
        $UserPriority = M("UserPriority");
        
        $quanUid = (int)$_POST['uid'];
        $quanUidString = $_POST['uidstring'];
        $quanUidArray = explode(',', $quanuidString);
        $quanUidArray = array_unique($quanUidArray);
        if (!empty($quanUid)) {
        	$recordUserLogin = $UserLogin->find($quanUid);
        	if (empty($recordUserLogin)) {
        		$this->ajaxReturn(0,'你要圈的用户不存在','error');
        	}
        	if ($quanUid == $userloginid) {
        		$this->ajaxReturn(0,'自己不能圈自己噢','error');
        	}
        	$isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $quanUid")->find();
        	if ($isPriorityExist) {
        		$this->ajaxReturn(0,'你已经圈了ta','error');
        	}
        	$isShieldExist = $UserPriority->where("uid = $userloginid AND sid = $quanUid")->find();
        	if ($isShieldExist) {
        		$this->ajaxReturn(0,'你已经屏蔽了ta，取消屏蔽后才能圈','error');
        	}
        	 
        	/**
        	 * begin insert priority data
        	 */
        	$priorityInsertData = array(
	            'id' => '',
	            'uid' => $userloginid,
	            'pid' => $quanUid,
	        	'pid_type' => $recordUserLogin['type'],
	            'time' => time(),
        	);
        	$isPriorityDataInserted = $UserPriority->add($priorityInsertData);
        	if ($isPriorityDataInserted) {
        		
        		/**
        		 * update i_user_info follow fans nums
        		 */
        		$userInfoPriority = $UserInfo->find($userloginid);
        		$userInfoPrioritied = $UserInfo->find($quanUid);
        		$newUserInfoPriorityData = array(
		        	'uid' => $userloginid,
		        	'follow' => $userInfoPriority['follow'] + 1,
        		);
        		$UserInfo->save($newUserInfoPriorityData);
        		$newUserInfoPrioritiedData = array(
		        	'uid' => $quanUid,
		        	'fans' => $userInfoPrioritied['fans'] + 1,
        		);
        		$UserInfo->save($newUserInfoPrioritiedData);
        		
        		//TODO
        		/**
        		 * send system message to prioritied user
        		 *
         		 * send system message to prioriti user for user type 2
                 * if ($userLogin['type'] == 2) 
	             * $msgPriorityUserType2Content = "你加入了 ".$userLogin['nickname']." 组织; 默认接收我们组织推送的消息, 信息会越来越灵通:)";
        		 */


                i_savenotice($userloginid, $quanUid, 'mutual/priority', '');

        		
        		$this->ajaxReturn(0,'成功圈了ta','ok');
        	}
        }
        
        /**
         * quan more then one person
         */
        if (!empty($quanUidArray)) {
        	foreach ($quanUidArray as $quanUid) {
	        	$recordUserLogin = $UserLogin->find($quanUid);
	        	if (empty($recordUserLogin)) {
	        		$this->ajaxReturn(0,'你要圈的用户不存在','error');
	        	}
	        	if ($quanUid == $userloginid) {
	        		$this->ajaxReturn(0,'自己不能圈自己噢','error');
	        	}
	        	$isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $quanUid")->find();
	        	if ($isPriorityExist) {
	        		$this->ajaxReturn(0,'你已经圈了ta','error');
	        	}
	        	$isShieldExist = $UserPriority->where("uid = $userloginid AND sid = $quanUid")->find();
	        	if ($isShieldExist) {
	        		$this->ajaxReturn(0,'你已经屏蔽了ta，取消屏蔽后才能圈','error');
	        	}
	        	 
	        	/**
	        	 * begin insert priority data
	        	 */
	        	$priorityInsertData = array(
		            'id' => '',
		            'uid' => $userloginid,
		            'pid' => $quanUid,
		        	'pid_type' => $recordUserLogin['type'],
		            'time' => time(),
	        	);
	        	$isPriorityDataInserted = $UserPriority->add($priorityInsertData);
	        	if ($isPriorityDataInserted) {
	        		
	        		/**
	        		 * update i_user_info follow fans nums
	        		 */
	        		$userInfoPriority = $UserInfo->find($userloginid);
	        		$userInfoPrioritied = $UserInfo->find($quanUid);
	        		$newUserInfoPriorityData = array(
			        	'uid' => $userloginid,
			        	'follow' => $userInfoPriority['follow'] + 1,
	        		);
	        		$UserInfo->save($newUserInfoPriorityData);
	        		$newUserInfoPrioritiedData = array(
			        	'uid' => $quanUid,
			        	'fans' => $userInfoPrioritied['fans'] + 1,
	        		);
	        		$UserInfo->save($newUserInfoPrioritiedData);
	        		
	        		//TODO
	        		/**
	        		 * send system message to prioritied user
	        		 *
	         		 * send system message to prioriti user for user type 2
	                 * if ($userLogin['type'] == 2) 
		             * $msgPriorityUserType2Content = "你加入了 ".$userLogin['nickname']." 组织; 默认接收我们组织推送的消息, 信息会越来越灵通:)";
	        		 */
	        	}
        	}
        	$this->ajaxReturn(0,'成功圈了他们','ok');
        }
    }
    
    public function quantacancel()
    {
    	$userloginid = session('userloginid');
        $UserInfo = M("UserInfo");
        $UserPriority = M("UserPriority");
        $quanUid = (int)$_POST['uid'];
        if (!empty($quanUid)) {
        	$isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $quanUid")->find();
        	if ($isPriorityExist) {
        		$isDelPriorityData = $UserPriority->where("uid = $userloginid AND pid = $quanUid")->delete();
        		
        		/**
		         * update i_user_info follow fans nums
		         */
		        $userInfoPriority = $UserInfo->find($userloginid);
		        $userInfoPrioritied = $UserInfo->find($quanUid);
		        $newUserInfoPriorityData = array(
		        	'uid' => $userloginid,
		        	'follow' => $userInfoPriority['follow'] - 1,
		        );
		        $UserInfo->save($newUserInfoPriorityData);
		        $newUserInfoPrioritiedData = array(
		        	'uid' => $quanUid,
		        	'fans' => $userInfoPrioritied['fans'] - 1,
		        );
		        $UserInfo->save($newUserInfoPrioritiedData);
		        $this->ajaxReturn(0,'成功取消圈','ok');
        	} else {
        		$this->ajaxReturn(0,'出错了','error');
        	}
        }
    }
    
}

?>