<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class WoAction extends Action {

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

        /**
         * wo/*.phtml top user nav
         */
        if (!empty($_GET["_URL_"][2])) {
        	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        }
        if ($userId < 10000) {
    		if (!empty($userloginid)) {
    			$userId = $userloginid;
    		}
    	}
    	
        $UserLogin = M("UserLogin");
        $userLogin = $UserLogin->find($userId);
        $this->assign('userLogin',$userLogin);
        $this->assign('title',$userLogin['nickname']." 的小窝");

        /**
         * change skin 
         */
        $this->assign('changeskin', $userLogin['skin']);
        
        /**
         * show user info
         */
        $UserInfo = M("UserInfo");
        $recordUserInfo = $UserInfo->find($userId);
        $this->assign('recordUserInfo',$recordUserInfo);

        /**
         * priority & shield
         */
        if (!empty($userloginid)) {
        	$UserPriority = M("UserPriority");
	        $isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $userId")->find();
	        if ($isPriorityExist) {
	            $this->assign('priorityExist', 1);
	        }
	        $isShieldExist = $UserPriority->where("uid = $userloginid AND sid = $userId")->find();
	        if ($isShieldExist) {
	            $this->assign('shieldExist', 1);
	        }
        }

        /**
         * user edu info
         */
        $recordSchoolInfo = i_school_domain();
        $this->assign('recordSchoolInfo', $recordSchoolInfo);
        $this->assign('schoolname', $recordSchoolInfo['school']);
        $OpAcademy = M("OpAcademy");
        $OpSpecialty = M("OpSpecialty");
        if (!empty($recordUserInfo['academy_op'])) {
        	$userAcademy = $OpAcademy->where("id = $recordUserInfo[academy_op]")->find();
        	$this->assign('userAcademy', $userAcademy);
        }
        if (!empty($recordUserInfo['specialty_op'])) {
        	$userSpecialty = $OpSpecialty->where("id = $recordUserInfo[specialty_op]")->find();
        	$this->assign('userSpecialty', $userSpecialty);
        }
        if ($userLogin['school'] != $recordSchoolInfo['id']) {
        	$SchoolInfo = M("SchoolInfo");
        	$userLoginSchoolInfo = $SchoolInfo->find($userLogin['school']);
        	$this->assign('userLoginSchoolInfo', $userLoginSchoolInfo);
        }
        
        /**
         * show user honor nums
         */
        $UserHonor = M("UserHonor");
        $totalUserHonorNums = $UserHonor->where("uid = $userloginid")->count();
        $this->assign('totalUserHonorNums', $totalUserHonorNums);
        
        /**
         * show remark 
         */
        $UserRemark = M("UserRemark");
        $recordUserRemark = $UserRemark->where("uid = $userloginid AND ruid = $userId")->find();
        $this->assign('recordUserRemark', $recordUserRemark);
    }

    public function _empty()
    {
    	$this->index();
    }

    protected function index()
    {
    	$userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][1]));
    	if ($userId < 10000) {
    		if (!empty($userloginid)) {
    			$userId = $userloginid;
    		}
    	}
        $UserLogin = M("UserLogin");
        $userLogin = $UserLogin->find($userId);
        $this->assign('userLogin',$userLogin);
        $this->assign('title',$userLogin['nickname']." 的小窝");

        /**
         * change skin 
         */
        $this->assign('changeskin', $userLogin['skin']);
        
        /**
         * show user info
         */
        $UserInfo = M("UserInfo");
        $recordUserInfo = $UserInfo->find($userId);
        $this->assign('recordUserInfo',$recordUserInfo);
        
        /**
         * user edu info
         */
        $recordSchoolInfo = i_school_domain();
        $this->assign('recordSchoolInfo', $recordSchoolInfo);
        $OpAcademy = M("OpAcademy");
        $OpSpecialty = M("OpSpecialty");
        if (!empty($recordUserInfo['academy_op'])) {
        	$userAcademy = $OpAcademy->where("id = $recordUserInfo[academy_op]")->find();
        	$this->assign('userAcademy', $userAcademy);
        }
        if (!empty($recordUserInfo['specialty_op'])) {
        	$userSpecialty = $OpSpecialty->where("id = $recordUserInfo[specialty_op]")->find();
        	$this->assign('userSpecialty', $userSpecialty);
        }
        if ($userLogin['school'] != $recordSchoolInfo['id']) {
        	$SchoolInfo = M("SchoolInfo");
        	$userLoginSchoolInfo = $SchoolInfo->find($userLogin['school']);
        	$this->assign('userLoginSchoolInfo', $userLoginSchoolInfo);
        }
        
        /**
         * show user honor nums
         */
        $UserHonor = M("UserHonor");
        $totalUserHonorNums = $UserHonor->where("uid = $userloginid")->count();
        $this->assign('totalUserHonorNums', $totalUserHonorNums);

        /**
         * priority & shield
         */
        if (!empty($userloginid)) {
	        $UserPriority = M("UserPriority");
	        $isPriorityExist = $UserPriority->where("uid = $userloginid AND pid = $userId")->find();
	        if ($isPriorityExist) {
	            $this->assign('priorityExist', 1);
	        } else {
	        	$this->assign('priorityExist', 0);
	        }
	        $isShieldExist = $UserPriority->where("uid = $userloginid AND sid = $userId")->find();
	        if ($isShieldExist) {
	            $this->assign('shieldExist', 1);
	        } else {
	        	$this->assign('shieldExist', 0);
	        }
        }
    	$RecordSay = M("RecordSay");
    	
    	$this->assign('thisschoolid', $recordSchoolInfo['id']);
        $this->assign('schoolname', $recordSchoolInfo['school']);

        /**
         * show remark 
         */
        $UserRemark = M("UserRemark");
        $recordUserRemark = $UserRemark->where("uid = $userloginid AND ruid = $userId")->find();
        $this->assign('recordUserRemark', $recordUserRemark);
        
        /**
         *
         */
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        $sayRecord = $RecordSay->where("i_record_say.uid = $userId AND i_record_say.say_type IN (0, 9)")
        ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->field('sid,i_user_login.uid,say_type,content,image,url,comment_co,diffusion_co,hit_co,plus_co,time,school_id,from,last_comment_ti,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        ->order('i_record_say.time DESC')
   	    ->limit($offset,$count)->select();
   	    $this->assign('sayRecord',$sayRecord);

        /**
         * page link
         */
        $totalRecords = $RecordSay->where("uid = $userId AND i_record_say.say_type IN (0, 9)")->count();
        $totalPages = ceil($totalRecords / $count);
        $this->assign('totalRecords',$totalRecords);
        $this->assign('totalPages',$totalPages);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:wo_dynamic');
    	} else {
    		$this->display('index');
    	}
    }

	public function dynamic()
    {
    	$userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if ($userId < 10000) {
    		if (!empty($userloginid)) {
    			$userId = $userloginid;
    		}
    	}
    	$RecordSay = M("RecordSay");

        /**
         *
         */
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        $sayRecord = $RecordSay->where("i_record_say.uid = $userId")
        ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->field('sid,i_user_login.uid,say_type,content,image,url,comment_co,diffusion_co,hit_co,time,from,last_comment_ti,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        ->order('i_record_say.time DESC')
   	    ->limit($offset,$count)->select();
   	    $this->assign('sayRecord',$sayRecord);

        /**
         * page link
         */
        $totalRecords = $RecordSay->where("uid = $userId")->count();
        $totalPages = ceil($totalRecords / $count);
        $this->assign('totalRecords',$totalRecords);
        $this->assign('totalPages',$totalPages);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:wo_index');
    	} else {
    		$this->display();
    	}
    }

    public function help()
    {
    	$userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if ($userId < 10000) {
    		if (!empty($userloginid)) {
    			$userId = $userloginid;
    		} else {
    			$userId = 10000;
    		}
    	}
    	$RecordSay = M("RecordSay");
    	$RecordHelp = M("RecordHelp");

        /**
         *
         */
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        if (isset($_GET['win'])) {
        	$recordSayHelp = $RecordSay->where("i_record_help.win_uid = $userId AND i_record_say.say_type = 1")->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        	->join('i_record_help ON i_record_help.sid = i_record_say.sid')
        	->field('hid,i_record_say.sid,reward_coins,i_record_help.status,win_uid,thanks,thanks_ti,i_user_login.uid,say_type,content,image,url,comment_co,diffusion_co,hit_co,from,last_comment_ti,nickname,sex,birthday,enteryear,type,online,active,icon_url,time')
        	->order('i_record_say.time DESC')
        	->limit($offset, $count)->select();
        	$totalRecords = $RecordHelp->where("win_uid = $userId")->count();
        } else {
        	$recordSayHelp = $RecordSay->where("i_record_say.uid = $userId AND i_record_say.say_type = 1")->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        	->join('i_record_help ON i_record_help.sid = i_record_say.sid')
        	->field('hid,i_record_say.sid,reward_coins,i_record_help.status,win_uid,thanks,thanks_ti,i_user_login.uid,say_type,content,image,url,comment_co,diffusion_co,hit_co,from,last_comment_ti,nickname,sex,birthday,enteryear,type,online,active,icon_url,time')
        	->order('i_record_say.time DESC')
        	->limit($offset, $count)->select();
        	$totalRecords = $RecordSay->where("uid = $userId AND say_type = 1")->count();
        }
   	    $this->assign('recordSayHelp',$recordSayHelp);

        /**
         * page link
         */
        $totalPages = ceil($totalRecords / $count);
        $this->assign('totalRecords',$totalRecords);
        $this->assign('totalPages',$totalPages);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:wo_help');
    	} else {
    		$this->display('index');
    	}
    }

    public function diffusion()
    {
    	$userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if ($userId < 10000) {
    		if (!empty($userloginid)) {
    			$userId = $userloginid;
    		}
    	}

        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        $RecordDiffusion = M("RecordDiffusion");
        $recordsDiffusion = $RecordDiffusion->where("i_record_diffusion.uid = $userId")
        ->join('i_record_say ON i_record_diffusion.sid = i_record_say.sid')
        ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->field('id,i_record_say.sid,view,comment_id,helpreply_id,assess_id,i_record_diffusion.time,i_user_login.uid,say_type,content,image,url,comment_co,diffusion_co,hit_co,from,last_comment_ti,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        ->order('i_record_diffusion.time DESC')
       	->limit($offset,$count)->select();

       	$this->assign('recordDiffusion',$recordsDiffusion);

       	/**
         * pageing link
         */
       	$userRecordDiffusionNums = $RecordDiffusion->where("uid = $userId")->count();
        $totalPages = ceil($userRecordDiffusionNums / $count);
        $this->assign('totalRecords',$userRecordDiffusionNums);
        $this->assign('totalPages',$totalPages);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:wo_diffusion');
    	} else {
    		$this->display('index');
    	}
    }
    
    public function plus()
    {
    	$userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if ($userId < 10000) {
    		if (!empty($userloginid)) {
    			$userId = $userloginid;
    		}
    	}

        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        $RecordPlus = M("RecordPlus");
        $resultRecordPlus = $RecordPlus->where("i_record_plus.uid = $userId AND i_record_say.sid != ''")
        ->join('i_record_say ON i_record_plus.sid = i_record_say.sid')
        ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->field('id,i_record_say.sid,i_record_plus.create_time,i_user_login.uid,say_type,content,image,url,comment_co,diffusion_co,hit_co,from,last_comment_ti,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        ->order('i_record_plus.create_time DESC')
       	->limit($offset,$count)->select();

       	$this->assign('resultRecordPlus',$resultRecordPlus);

       	/**
         * pageing link
         */
       	$userRecordPlusNums = $RecordPlus->where("i_record_plus.uid = $userId AND i_record_say.sid != ''")->join('i_record_say ON i_record_plus.sid = i_record_say.sid')->count();
        $totalPages = ceil($userRecordPlusNums / $count);
        $this->assign('totalRecords',$userRecordPlusNums);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }

    public function intersection()
    {
        $userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        if (empty($userId) && !empty($userloginid)) {
            $userId = $userloginid;
        }

        if (!empty($userloginid)) {
        	$UserLogin = M("UserLogin");
            $userFirst = $UserLogin->where("i_user_login.uid = $userloginid")
            ->join('i_user_info ON i_user_login.uid = i_user_info.uid')->find();
           	$this->assign('userFirst',$userFirst);

            $userSecond = $UserLogin->where("i_user_login.uid = $userId")
            ->join('i_user_info ON i_user_login.uid = i_user_info.uid')->find();
           	$this->assign('userSecond',$userSecond);

            /**
             * user quan
             */
            $UserPriority = M("UserPriority");
            $userPriority = $UserPriority->where("pid = $userloginid OR pid = $userId")
            ->join('i_user_login ON i_user_priority.uid = i_user_login.uid')
            ->field('i_user_priority.uid,nickname,sex,birthday,enteryear,type,online,active,icon_url')
            ->select();
            $userPrioritied = $UserPriority->where("(i_user_priority.uid = $userloginid AND pid != '') OR (i_user_priority.uid = $userId AND pid != '')")
            ->join('i_user_login ON i_user_priority.pid = i_user_login.uid')
            ->field('i_user_priority.pid,nickname,sex,birthday,enteryear,type,online,active,icon_url')
            ->select();
            $this->assign('userPrioritys',$userPriority);
            $this->assign('userPrioritieds',$userPrioritied);
        }
        $this->display();
    }

    public function honor()
    {
    	$userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if ($userId < 10000) {
    		if (!empty($userloginid)) {
    			$userId = $userloginid;
    		}
    	}
        $IUserLogin = D("IUserLogin");
        $userStatus = $IUserLogin->userExists($userId);
        $this->assign('userStatus',$userStatus);

        /**
         *
         */
        $UserHonor = M("UserHonor");
        $userHonors = $UserHonor->where("uid = $userId")->select();
        $this->assign('userHonors',$userHonors);
        $this->display();
    }

    public function album()
    {
    	$userloginid = session('userloginid');
    	$UserAlbum = D("UserAlbum");
    	$imageStorageUrl = image_storage_url();

    	/**
    	 * ajax delete part
    	 */
    	if ($this->isPost()) {
    		if (!empty($_POST['changeway']) && !empty($_POST['thisimageid']) && !empty($_POST['thisuserid'])) {
    			$photoId = (int)$_POST['thisimageid'];
    			$changeway = $_POST['changeway'];
    			$userId = (int)$_POST['thisuserid'];
    			$imageItem = $UserAlbum->where("id = $photoId")->find();
    			if ($changeway == "next") {
    				$imageItemNext = $UserAlbum->where("type = $imageItem[type] AND uid = $userId AND id < $imageItem[id]")->order("time DESC")->find();
    			} else {
    				$imageItemNext = $UserAlbum->where("type = $imageItem[type] AND uid = $userId AND id > $imageItem[id]")->order("time ASC")->find();
    			}
    			if (empty($imageItemNext['id'])) {
    				$imageItemNext = $imageItem;
    				$imageItemNext['empty'] = 'true';
    			}
    			
    			/**
    			 * image access control
    			 */
    			if ($userloginid != $userId && $imageItem['type'] == 4) {
    				$this->ajaxReturn(0,'聊天图片仅主人自己可见...','error');
    			}
    			
    			$imageItemUpdateHit = array(
	    			'id' => $imageItemNext['id'],
	    			'hit' => $imageItemNext['hit'] + 1,
    			);
    			$UserAlbum->save($imageItemUpdateHit);
    			$imageItemNext['time'] = i_time($imageItemNext['time']);
    			$imageItemNext['size'] = round($imageItemNext['size']/1024)."KB";
    			$this->ajaxReturn($imageItemNext,'返回图片数据','ok');
    		}
    		
    		if (!empty($userloginid) && !empty($_POST['imageid'])) {
    			$deleteImageId = (int)$_POST['imageid'];
    			$deleteAlbumRecord = $UserAlbum->find($deleteImageId);

    			Vendor('Ihelpoo.Upyun');
        		$upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
        		
    			/**
    			 * delete icon
    			 */
    			if ($deleteAlbumRecord['type'] == 1) {
    				$urlFilename = str_ireplace("$imageStorageUrl", "", $deleteAlbumRecord['url']);
    				$urlThumbFilename = str_ireplace("iconorignal", "thumb_iconorignal", $urlFilename);
    				$isStorageDeleteFlag = $upyun->delete($urlFilename);
    				$upyun->delete($urlThumbFilename);
    				if ($isStorageDeleteFlag) {
    					$UserAlbum->where("id = $deleteImageId AND uid = $userloginid")->delete();
    					$this->ajaxReturn(0,'删除成功,3秒后页面跳转','ok');
    				}
    				$this->ajaxReturn($isStorageDeleteFlag,'删除头像失败','wrong');
    			} else if ($deleteAlbumRecord['type'] == 2) {
    				$deleteAlbumRecordUrl = $deleteAlbumRecord['url'];

    				/**
    				 * check if linked records;
    				 */
    				$RecordOutimg = M("RecordOutimg");
    				$RecordSay = M("RecordSay");
    				$resultRecordOutimg = $RecordOutimg->where("rpath = '".$deleteAlbumRecordUrl."'")->find();
    				if (!empty($resultRecordOutimg)) {
    					$searchImageIdSql = "SELECT * FROM `i_record_say` WHERE `uid` = $userloginid AND `image` LIKE '%".$resultRecordOutimg['id']."%'";
    					$imageIdRecordSay = $RecordSay->query($searchImageIdSql);
    					if (!empty($imageIdRecordSay)) {
    						foreach ($imageIdRecordSay as $say) {
    							$imageIdArray = explode(';', $say['image']);
    							if (in_array($resultRecordOutimg['id'], $imageIdArray)) {
    								$this->ajaxReturn($say,'相关信息流包含此图，请先删除记录','existsay');
    							}
    						}
    					}
    					$RecordOutimg->where("id = $resultRecordOutimg[id]")->delete();
    				}

    				/**
    				 * check if linked recordcomnent;
    				 */
    				$RecordComment = M("RecordComment");
    				$resultRecordComment = $RecordComment->where("image = '".$deleteAlbumRecordUrl."'")->find();
    				if (!empty($resultRecordComment)) {
    					$this->ajaxReturn($resultRecordComment,'相关评论包含此图，请先删除评论','existcomment');
    				}

    				/**
    				 * check if linked recordcomnent;
    				 */
    				$RecordHelpreply = M("RecordHelpreply");
    				$resultRecordHelpreply = $RecordHelpreply->where("image = '".$deleteAlbumRecordUrl."'")->find();
    				if (!empty($resultRecordHelpreply)) {
    					$this->ajaxReturn($resultRecordHelpreply,'相关帮助回复包含此图，请先删除','existhelpreply');
    				}

    				/**
    				 * delete
    				 */
    				$urlFilename = str_ireplace("$imageStorageUrl", "", $deleteAlbumRecordUrl);
    				$urlThumbFilename = str_ireplace("recordsay", "thumb_recordsay", $urlFilename);
    				$isStorageDeleteFlag = $upyun->delete($urlFilename);
    				$upyun->delete($urlThumbFilename);
    				if ($isStorageDeleteFlag) {
    					$UserAlbum->where("id = $deleteImageId AND uid = $userloginid")->delete();
    					$this->ajaxReturn(0,'删除成功,3秒后页面跳转','ok');
    				}
    				$this->ajaxReturn($isStorageDeleteFlag,'删除信息流图片失败','wrong');
    			} else if ($deleteAlbumRecord['type'] == 3) {
    				$deleteAlbumRecordUrl = $deleteAlbumRecord['url'];
    				$RecordCommodity = M("RecordCommodity");

    				/**
    				 * image
    				 */
    				$imageRecordCommodity = $RecordCommodity->where("shopid = $userloginid AND image = '".$deleteAlbumRecordUrl."'")->find();
    				if (!empty($imageRecordCommodity)) {
    					$this->ajaxReturn($imageRecordCommodity,'相关商品包含此图像，请先删除','existcommodity');
    				}

    				/**
    				 * detail
    				 */
    				$searchCommodityDetailSql = "SELECT * FROM `i_record_commodity` WHERE `shopid` = $userloginid AND `detail` LIKE '%".$deleteAlbumRecordUrl."%'";
    				$imageIdRecordCommodity = $RecordCommodity->query($searchCommodityDetailSql);
    				if (!empty($imageIdRecordCommodity)) {
    					$this->ajaxReturn($imageIdRecordCommodity['0'],'相关商品内容包含此图像，请先删除记录','existcommodity');
    				}

    				/**
    				 * delete
    				 */
    				$urlFilename = str_ireplace("$imageStorageUrl", "", $deleteAlbumRecordUrl);
    				$urlThumbFilename = str_ireplace("goods", "thumb_goods", $urlFilename);
    				$isStorageDeleteFlag = $upyun->delete($urlFilename);
    				$upyun->delete($urlThumbFilename);
    				$urlThumb2Filename = str_ireplace("goodscontent", "thumb_goodscontent", $urlFilename);
    				$storage->delete("public", $urlThumb2Filename);
    				if ($isStorageDeleteFlag) {
    					$UserAlbum->where("id = $deleteImageId AND uid = $userloginid")->delete();
    					$this->ajaxReturn(0,'删除成功,3秒后页面跳转','ok');
    				}
    				$this->ajaxReturn($isStorageDeleteFlag,'删除店铺图片失败','wrong');
    			} else if ($deleteAlbumRecord['type'] == 4) {
    				$deleteAlbumRecordUrl = $deleteAlbumRecord['url'];

    				/**
    				 * delete
    				 */
    				$urlFilename = str_ireplace("$imageStorageUrl", "", $deleteAlbumRecordUrl);
    				$urlThumbFilename = str_ireplace("talk", "thumb_talk", $urlFilename);
    				$isStorageDeleteFlag = $upyun->delete($urlFilename);
    				$upyun->delete($urlThumbFilename);
    				if ($isStorageDeleteFlag) {
    					$UserAlbum->where("id = $deleteImageId AND uid = $userloginid")->delete();
    					$this->ajaxReturn(0,'删除成功,3秒后页面跳转','ok');
    				}
    				$this->ajaxReturn($isStorageDeleteFlag,'删除聊天图片失败','wrong');
    			}
    		}
    		exit();
    	}

    	/**
    	 * view part
    	 */
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if (!empty($_GET["_URL_"][3])) {
    		$albumId = (int)htmlspecialchars(trim($_GET["_URL_"][3]));
    	} else {
    		$albumId = 0;
    	}
    	if (!empty($_GET["_URL_"][4])) {
    		$photoId = (int)htmlspecialchars(trim($_GET["_URL_"][4]));
    	} else {
    		$photoId = 0;
    	}
    	if ($userId < 10000) {
    		if (!empty($userloginid)) {
    			$userId = $userloginid;
    		}
    	}

    	$page = i_page_get_num();
        $count = 15;
        $offset = $page * $count;
    	if ($albumId == 1) {
    		$totalImages = $UserAlbum->where("uid = $userId AND type = 1")->count();
    		$resultsUserAlbum = $UserAlbum->where("uid = $userId AND type = 1")->limit($offset,$count)->order("time DESC")->select();
    		$totalAlbumSize = $UserAlbum->where("uid = $userId AND type = 1")->sum('size');
    	} else if ($albumId == 2) {
    		$totalImages = $UserAlbum->where("uid = $userId AND type = 2")->count();
    		$resultsUserAlbum = $UserAlbum->where("uid = $userId AND type = 2")->limit($offset,$count)->order("time DESC")->select();
    		$totalAlbumSize = $UserAlbum->where("uid = $userId AND type = 2")->sum('size');
    	} else if ($albumId == 3) {
    		$totalImages = $UserAlbum->where("uid = $userId AND type = 3")->count();
    		$resultsUserAlbum = $UserAlbum->where("uid = $userId AND type = 3")->limit($offset,$count)->order("time DESC")->select();
    		$totalAlbumSize = $UserAlbum->where("uid = $userId AND type = 3")->sum('size');
    	} else if ($albumId == 4) {
    		$totalImages = $UserAlbum->where("uid = $userId AND type = 4")->count();
    		$resultsUserAlbum = $UserAlbum->where("uid = $userId AND type = 4")->limit($offset,$count)->order("time DESC")->select();
    		$totalAlbumSize = $UserAlbum->where("uid = $userId AND type = 4")->sum('size');

    		/**
    		 * image access control
    		 */
    		if ($userloginid != $userId) {
    			redirect('/wo/album/'.$userId, 3, '此相册仅主人自己可见...');
    		}
    	} else if ($albumId == 9) {
    		$imageItem = $UserAlbum->where("id = $photoId")->find();
    		$imageItemPrivious = $UserAlbum->where("type = $imageItem[type] AND uid = $userId AND id > $imageItem[id]")->order("time ASC")->find();
    		$imageItemNext = $UserAlbum->where("type = $imageItem[type] AND uid = $userId AND id < $imageItem[id]")->order("time DESC")->find();

    		/**
    		 * image access control
    		 */
    		if ($userloginid != $userId && $imageItem['type'] == 4) {
    			redirect('/wo/album/'.$userId, 3, '聊天图片仅主人自己可见...');
    		}
    		$this->assign('imageItem',$imageItem);
    		$this->assign('imageItemPrivious',$imageItemPrivious);
    		$this->assign('imageItemNext',$imageItemNext);
    		$imageItemUpdateHit = array(
    			'id' => $imageItem['id'],
    			'hit' => $imageItem['hit'] + 1,
    		);
    		$UserAlbum->save($imageItemUpdateHit);
    	} else {
	        $totalImages = $UserAlbum->where("uid = $userId")->count();
	        $iconUserAlbum = $UserAlbum->where("uid = $userId AND type = 1")->order("time DESC")->find();
	        $recordUserAlbum = $UserAlbum->where("uid = $userId AND type = 2")->order("time DESC")->find();
	        $shopUserAlbum = $UserAlbum->where("uid = $userId AND type = 3")->order("time DESC")->find();
	        $talkUserAlbum = $UserAlbum->where("uid = $userId AND type = 4")->order("time DESC")->find();
	        $totalIconImages = $UserAlbum->where("uid = $userId AND type = 1")->count();
	        $totalRecordImages = $UserAlbum->where("uid = $userId AND type = 2")->count();
	        $totalShopImages = $UserAlbum->where("uid = $userId AND type = 3")->count();
	        $totalTalkImages = $UserAlbum->where("uid = $userId AND type = 4")->count();
	        $totalAlbumSize = $UserAlbum->where("uid = $userId")->sum('size');
	        $this->assign('iconUserAlbum',$iconUserAlbum);
	        $this->assign('recordUserAlbum',$recordUserAlbum);
	        $this->assign('shopUserAlbum',$shopUserAlbum);
	        $this->assign('talkUserAlbum',$talkUserAlbum);
	        $this->assign('totalImages',$totalImages);
	        $this->assign('totalIconImages',$totalIconImages);
	        $this->assign('totalRecordImages',$totalRecordImages);
	        $this->assign('totalShopImages',$totalShopImages);
	        $this->assign('totalTalkImages',$totalTalkImages);

	    	/**
	    	 * album degree configure
	    	 * 1~3 0.5GB
	    	 * 4~6 1GB
	    	 * 7~8 2GB
	    	 * 9>  10GB
	    	 */
	    	$UserLogin = M("UserLogin");
	    	$recordUserLogin = $UserLogin->find($userId);
	    	$userLevel = i_degree($recordUserLogin['active']);
	    	$totalAlbumDefaultSize = i_configure_album_size($userLevel);
	    	$this->assign('totalAlbumDefaultSize',$totalAlbumDefaultSize);
    	}

    	/**
         * pageing link
         */
        $totalPages = ceil($totalImages / $count);
        $this->assign('totalRecords',$totalImages);
        $this->assign('totalPages',$totalPages);
        $this->assign('albumId',$albumId);
        if (!empty($resultsUserAlbum)) {
    		$this->assign('resultsUserAlbum',$resultsUserAlbum);
        }
    	$this->assign('totalImages',$totalImages);
    	$this->assign('totalAlbumSize',$totalAlbumSize);
        $this->display();
    }

    public function quan()
    {
    	$userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if (empty($userId) && !empty($userloginid)) {
    		$userId = $userloginid;
    	}

        /**
         * show prioritied users
         */
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;

        $UserPriority = M("UserPriority");
        $userPrioritys = $UserPriority->where("i_user_priority.uid = $userId AND pid != ''")
        ->join('i_user_login ON i_user_priority.pid = i_user_login.uid')
        ->order('i_user_priority.time DESC')
       	->limit($offset, $count)
       	->select();
       	$this->assign('userPrioritys',$userPrioritys);

        /**
         * pageing link
         */
       	$userPrioritiedNums = $UserPriority->where("uid = $userId AND pid != ''")->count();
        $totalPages = ceil($userPrioritiedNums / $count);
        $this->assign('userPrioritiedNums',$userPrioritiedNums);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }

    public function quaned()
    {
    	$userloginid = session('userloginid');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if (empty($userId) && !empty($userloginid)) {
    		$userId = $userloginid;
    	}

        /**
         * show prioritied users
         */
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;

        $UserPriority = M("UserPriority");
        $userPriorityeds = $UserPriority->where("i_user_priority.pid = $userId")
        ->join('i_user_login ON i_user_priority.uid = i_user_login.uid')
        ->order('i_user_priority.time DESC')
       	->limit($offset, $count)
       	->select();
       	$this->assign('userPriorityeds',$userPriorityeds);

        /**
         * pageing link
         */
       	$userPrioritiedNums = $UserPriority->where("pid = $userId")->count();
        $totalPages = ceil($userPrioritiedNums / $count);
        $this->assign('userPrioritiedNums',$userPrioritiedNums);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }

}

?>