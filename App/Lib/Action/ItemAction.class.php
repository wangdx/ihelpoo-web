<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class ItemAction extends Action {

    protected function _initialize()
    {
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	}
        header("Content-Type:text/html; charset=utf-8");
    }

    /**
     *
     * content from i_record_say
     */
    public function say()
    {
    	$userloginid = session('userloginid');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
        $recordId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        if (empty($recordId)) {
            redirect('/stream', 3, '你访问的内容不存在 或者被删除了 :(...');
        }
        $RecordSay = M("RecordSay");
        $sayRecord = $RecordSay->where("sid = $recordId AND say_type IN (0,2,9)")->find();
        if (empty($sayRecord)) {
        	redirect('/stream', 3, '你访问的内容不存在 或者被删除了 :(...');
        }
        $this->assign('sayRecord',$sayRecord);
        $this->assign('recordSchoolInfo',$recordSchoolInfo);
        
        /**
         * school
         */
        if ($sayRecord['school_id'] != $recordSchoolInfo['id']) {
	        $SchoolInfo = M("SchoolInfo");
	        $sayRecordSchoolInfo = $SchoolInfo->find($sayRecord['school_id']);
	        $this->assign('sayRecordSchoolInfo',$sayRecordSchoolInfo);
        }

        //$userLogin = $dbUserLogin->userExists($user->uid);
        //$this->view->itemUserLogin = $userLogin = $dbUserLogin->userExists($sayRecord->uid);

        $UserLogin = M("UserLogin");
        $itemUserLogin = $UserLogin->find($sayRecord['uid']);
        $this->assign('title','详细内容 '.$recordSchoolInfo['school'].'信息流 by '.$itemUserLogin['nickname']);
        $this->assign('itemUserLogin',$itemUserLogin);

        /**
         * authority part, pass this beta
         *
         * if ($sayRecord->authority == "1") {
         *    if ($sayRecord->uid != $user->uid) {
         *        throw new Exception("设置信息权限为'仅自己'。 只有".$userLogin->nickname."可以查看这条记录");
         *        exit();
         *    }
         * }
         */

        /**
         * diffision part
         */
        $RecordDiffusion = M("RecordDiffusion");
        if (!empty($sayRecord['diffusion_co'])) {
        	$recordDiffusionArray = $RecordDiffusion->where("sid = $recordId")->join('i_user_login ON i_record_diffusion.uid = i_user_login.uid')
		    ->field('id,i_user_login.uid,i_record_diffusion.sid,i_record_diffusion.view,i_record_diffusion.time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
		    ->order('i_record_diffusion.time DESC')
		    ->select();
		    $this->assign('recordDiffusionArray', $recordDiffusionArray);
        }
        
        /**
         * puls part
         */
        $RecordPlus = M("RecordPlus");
        if (!empty($sayRecord['plus_co'])) {
        	$recordPlusArray = $RecordPlus->where("sid = $recordId")->join('i_user_login ON i_record_plus.uid = i_user_login.uid')
		    ->field('id,i_user_login.uid,i_record_plus.sid,i_record_plus.create_time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
		    ->order('i_record_plus.create_time DESC')
		    ->select();
		    $this->assign('recordPlusArray', $recordPlusArray);
        }

        /**
         * show & handle images
         */
        if (!empty($sayRecord['image'])) {
        	$imageRecordArray = i_get_image($sayRecord['image']);
        	$this->assign('imageRecordArray', $imageRecordArray);
        }

        /**
         * update hit_op
         */
        $hit_co = $sayRecord['hit_co'] + 1;
        $set = array(
        	'sid' => $recordId,
        	'hit_co' => $hit_co
        );
        $RecordSay->save($set);

        $RecordComment = M("RecordComment");
        $MsgComment = M("MsgComment");

        /**
         *
         * show $count records every page
         * $count int
         * $offset int Equal current page * count
         */
        $page = i_page_get_num();
        $this->assign('pagenow', $page + 2);
        $count = 20;
        $this->assign('count', $count);
        $offset = $page * $count;
        $sayComment = $RecordComment->where("sid = $recordId")->join('i_user_login ON i_record_comment.uid = i_user_login.uid')
        ->field('cid,i_user_login.uid,sid,toid,content,image,diffusion_co,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        ->limit($offset,$count)->order('cid DESC')->select();
        $this->assign('sayComment', $sayComment);

        /**
         * page link
         */
        $totalSayCommentNums = $RecordComment->where("sid = $recordId")->count();
        $this->assign('totalSayCommentNums', $totalSayCommentNums);
        $totalPages = ceil($totalSayCommentNums / $count);
        $this->assign('totalPages', $totalPages);
        $this->assign('pageCount', $count);
        
        /**
         * weibo
         */
        $configIsLoginWeibo = C('IS_LOGIN_WEIBO');
	    $this->assign('configIsLoginWeibo', $configIsLoginWeibo);
        $this->display();
    }

    /**
     *
     * content from i_record_help
     */
    public function help()
    {
    	$userloginid = session('userloginid');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$recordId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        if (empty($recordId)) {
            redirect('/stream', 3, '你访问的内容不存在 或者被删除了 错误代码1 :(...');
        }
        $RecordSay = M("RecordSay");
        $sayRecord = $RecordSay->where("sid = $recordId")->find();
        if (empty($sayRecord)) {
        	redirect('/stream', 3, '你访问的内容不存在 或者被删除了 错误代码2 :(...');
        }
        $this->assign('sayRecord',$sayRecord);
    	$IUserLogin = D("IUserLogin");
        $helpRecordOwener = $IUserLogin->userExists($sayRecord['uid']);
        $this->assign('title','求助 详细内容'.$recordSchoolInfo['school'].' by '.$helpRecordOwener['nickname']);
        $this->assign('helpRecordOwener',$helpRecordOwener);
        $this->assign('recordSchoolInfo',$recordSchoolInfo);

        /**
         * school
         */
        if ($sayRecord['school_id'] != $recordSchoolInfo['id']) {
	        $SchoolInfo = M("SchoolInfo");
	        $sayRecordSchoolInfo = $SchoolInfo->find($sayRecord['school_id']);
	        $this->assign('sayRecordSchoolInfo',$sayRecordSchoolInfo);
        }
        
        /**
         * diffision part
         */
        $RecordDiffusion = M("RecordDiffusion");
        $recordDiffusionNums = $RecordDiffusion->where("sid = $recordId")->count();
        $this->assign('recordDiffusionNums', $recordDiffusionNums);
        if (!empty($recordDiffusionNums)) {
        	$recordDiffusionArray = $RecordDiffusion->where("sid = $recordId")->join('i_user_login ON i_record_diffusion.uid = i_user_login.uid')
        	->field('id,i_user_login.uid,i_record_diffusion.sid,i_record_diffusion.time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        	->order('i_record_diffusion.time DESC')
        	->select();
        	$this->assign('recordDiffusionArray', $recordDiffusionArray);
        }
        
        /**
         * puls part
         */
        $RecordPlus = M("RecordPlus");
        if (!empty($sayRecord['plus_co'])) {
        	$recordPlusArray = $RecordPlus->where("sid = $recordId")->join('i_user_login ON i_record_plus.uid = i_user_login.uid')
		    ->field('id,i_user_login.uid,i_record_plus.sid,i_record_plus.create_time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
		    ->order('i_record_plus.create_time DESC')
		    ->select();
		    $this->assign('recordPlusArray', $recordPlusArray);
        }

        /**
         * show & handle images
         */
        if (!empty($sayRecord['image'])) {
        	$imageRecordArray = i_get_image($sayRecord['image']);
        	$this->assign('imageRecordArray', $imageRecordArray);
        }

        /**
         * record help
         */
        $RecordHelp = M("RecordHelp");
        $helpRecord = $RecordHelp->where("sid = $recordId")->find();
        if (!empty($helpRecord['win_uid'])) {
        	$helpRecordWiner = $IUserLogin->userExists($helpRecord['win_uid']);
        	$this->assign('helpRecordWiner', $helpRecordWiner);
        }
        if (empty($helpRecord)) {
        	redirect('/stream', 3, '你访问的内容不存在 或者被删除了 错误代码3 :(...');
        }
        $this->assign('helpRecord', $helpRecord);
        $RecordHelpreply = M("RecordHelpreply");
        $allHelpreply = $RecordHelpreply->where("sid = $recordId")->join('i_user_login ON i_record_helpreply.uid = i_user_login.uid')
        ->field('id,i_user_login.uid,sid,toid,content,image,diffusion_co,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')->order("time ASC")->select();
        $this->assign('allHelpreply', $allHelpreply);

    	/**
    	 * send help email; time limit 15 days
    	 * shut down auto;
    	 */
        $MsgSystem = M("MsgSystem");
    	$timewidth = time() - 1000000;
    	$timeend = time() - 1296000;
    	if ($sayRecord['time'] < $timewidth && $sayRecord['time'] > $timeend) {
    		$AuMailSend = M("AuMailSend");
    		$isHelpTimeMailSend = $AuMailSend->where("uid = $sayRecord[uid] AND sid = $sayRecord[sid]")->find();
    		if (!$isHelpTimeMailSend) {
    			/**
    			 * send emil
    			 */
    			Vendor('Ihelpoo.Email');
                $emailObj = new Email();
    			$emailObj->helpstatusEnd($sayRecord['sid'], $helpRecordOwener['email'], $helpRecordOwener['nickname']);

    			/*
    			 * insert email already send info into i_au_mail_send
    			 */
    			$emailAlreaySendData = array(
            	    'id' => '',
            	    'uid' => $sayRecord['uid'],
            	    'helperid' => '',
            	    'sid' => $sayRecord['sid'],
            	    'type' => 1,
            	    'time' => time(),
    			);
    			$AuMailSend->add($emailAlreaySendData);

    			/*
    			 * send system message
    			 */
//    			$msgHelpEndContent = "您的帮助要到期了, 快来看看有什么进展不";
//    			$msgHelpEndData = array(
//                    'id' => NULL,
//                    'uid' => $sayRecord['uid'],
//                    'type' => 'stream/ih-para:timeLimit',
//                    'url_id' => $sayRecord['sid'],
//                    'content' => $msgHelpEndContent,
//                    'time' => time(),
//                    'deliver' => 0,
//    			);
//    			$MsgSystem->add($msgHelpEndData);

                i_savenotice('10000', $sayRecord['uid'], 'stream/ih-para:timeLimit', $sayRecord['sid']);
                $this->assign('toUid', $sayRecord['uid']);


    		}
    	} else if($sayRecord['time'] < $timeend && $helpRecord['status'] < 3) {
    		/*
    		 * system shut down then help auto
    		 */
    		$sysShutDownHelp = array(
    			'hid' => $helpRecord['hid'],
        	    'thanks' => '帮助到期了,系统自动关闭',
        	    'thanks_ti' => time(),
                'status' => 3,
    		);
    		$RecordHelp->save($sysShutDownHelp);

    		/*
    		 * send system message
    		 */
//    		$msgHelpAlreadyEndContent = "您的帮助到期了, 系统已经自动关闭";
//    		$msgHelpAlreadyEndData = array(
//                'id' => NULL,
//                'uid' => $sayRecord['uid'],
//                'type' => 'stream/ih-para:timeEnd',
//                'url_id' => $sayRecord['sid'],
//                'content' => $msgHelpAlreadyEndContent,
//                'time' => time(),
//                'deliver' => 0,
//    		);
//    		$MsgSystem->add($msgHelpAlreadyEndData);
            i_savenotice('10000', $sayRecord['uid'], 'stream/ih-para:timeEnd', $sayRecord['sid']);
            $this->assign('toUid', $sayRecord['uid']);
    	}

    	/**
    	 * update hit_op
    	 */
    	$hit_co = $sayRecord['hit_co'] + 1;
    	$hitCountSet = array(
    		'sid' => $recordId,
        	'hit_co' => $hit_co,
    	);
    	$RecordSay->save($hitCountSet);

    	/**
    	 * show help choose users ih.phtml , help status 3
    	 */
    	$helpReplyRecords = $RecordHelpreply->where("sid = $sayRecord[sid] && uid != $sayRecord[uid]")->select();
    	foreach ($helpReplyRecords as $helpReplyRecord) {
    		$userChooseArray[] = $helpReplyRecord['uid'];
    		$userChooseArray = array_unique($userChooseArray);
    	}
    	$this->assign('userChooseArray', $userChooseArray);

    	/**
    	 * add for not login visit
    	 */
    	if (!empty($userloginid)) {

    		/**
    		 * choose a helper to reward
    		 */
    		if (!empty($_POST['chooseid'])) {

    			if ($userloginid != $sayRecord['uid']) {
    				redirect('/stream', 1, 'you don not have the power to do this...');
    			}

    			$chooseId = (int)trim(addslashes(htmlspecialchars(strip_tags($_POST['chooseid']))));
    			if (!$chooseId) {
    				redirect('/stream', 1, 'choose user is wrong...');
    			}
    			$chooseThanks = trim(addslashes(htmlspecialchars(strip_tags($_POST['choosethanks']))));

    			if (!empty($helpRecord['win_uid'])) {
    				redirect('/stream', 1, 'already choosed...');
    			}

    			/**
    			 * update help status
    			 */
    			$dataRecordHelp = array(
    				'hid' => $helpRecord['hid'],
	       	        'status' => 3,
	                'win_uid' => $chooseId,
	                'thanks' => $chooseThanks,
	                'thanks_ti' => time(),
    			);
    			$RecordHelp->save($dataRecordHelp);

    			/**
    			 * insert system message
    			 */
    			$msgHelpEndContent = "问题得到解决,你被".$helpRecordOwener['nickname']."选为了最佳帮助, 得到了".$helpRecord['reward_coins']."个活跃。";
    			$msgRecordEndAction = array(
	                'id' => NULL,
	                'uid' => $chooseId,
	                'type' => 'stream/ih-para:success',
	                'url_id' => $sayRecord['sid'],
	                'from_uid' => $userloginid,
	                'content' => $msgHelpEndContent,
	                'time' => time(),
	                'deliver' => 0,
    			);
    			$affetcedMsgRecordHelpEnd = $MsgSystem->add($msgRecordEndAction);
    			if (empty($affetcedMsgRecordHelpEnd)) {
    				redirect('/stream', 1, 'message_system_help end choose insert failed...');
    			}

                i_savenotice($userloginid, $chooseId, 'stream/ih-para:success', $sayRecord['sid']);

    			/**
    			 * update $userloginid active && $chooseId coins
    			 * $helpRecordViewer = $IUserLogin->userExists($userloginid);
    			 */
    			$userStatusData = array(
    				'uid' => $userloginid,
	                'active' => $helpRecordOwener['active'] + 5,
	                'last_active_ti' => time(),
    			);
    			$IUserLogin->save($userStatusData);

    			/**
    			 * msg active
    			 */
    			$MsgActive = M("MsgActive");
    			$msgActiveArray = array(
					'id' => '',
					'uid' => $userloginid,
					'total' => $helpRecordOwener['active'],
					'change' => 5,
					'way' => 'add',
					'reason' => '采纳别人为最佳帮助',
					'time' => time(),
					'deliver' => 0,
    			);
    			$MsgActive->add($msgActiveArray);

    			$recordChooseUserStatus = $IUserLogin->userExists($chooseId);
    			$chooseUserStatusData = array(
    				'uid' => $chooseId,
               		'active' => $recordChooseUserStatus['active'] + $helpRecord['reward_coins'],
    			);
    			$IUserLogin->save($chooseUserStatusData);

    			$msgChooseActiveArray = array(
					'id' => '',
					'uid' => $chooseId,
					'total' => $recordChooseUserStatus['active'],
					'change' => $helpRecord['reward_coins'],
					'way' => 'add',
					'reason' => '帮助被采纳',
					'time' => time(),
					'deliver' => 0,
    			);
    			$MsgActive->add($msgChooseActiveArray);

    			$helpRecordSid = $_POST['sid'];
    			redirect('/item/help/'.$helpRecordSid, 1, '选择最佳帮助成功...');
    		}
    	}
    	$this->display();
    }

	/**
     *
     * ajax for say post
     */
    public function sayajax()
    {
    	$userloginid = session('userloginid');
    	$RecordSay = M("RecordSay");
    	$UserLogin = M("UserLogin");
    	$RecordComment = M("RecordComment");
    	$MsgComment = M("MsgComment");
    	if (!empty($userloginid)) {
    		if ($this->isPost()) {
    			$validate = array(
    				array('sid', 'require', '信息sid地址格式错误'),
    				array('textareacontent', 'require', '评论回复不能为空'),
    				array('imageurl', 'url', 'imageurl格式错误',2),
	    			array('cid', 'number', '信息cid地址格式错误',2),
	    			array('toid', 'number', '用户类型错误',2),
	    			array('verificationcode', 'number', '验证码格式错误')
    			);
    			$RecordComment->setProperty("_validate", $validate);
    			$result = $RecordComment->create();
    			if (!$result) {
    				$publishError = $RecordComment->getError();
    				$this->ajaxReturn(0,$publishError,'error');
    			} else {
    				$commentcontent = i_makechickableLinks(trim(addslashes(strip_tags($_POST["textareacontent"],"<a>"))));
    				$imageurl = trim(addslashes($_POST["imageurl"]));
    				$atusers = htmlspecialchars(addslashes(strtolower(trim($_POST["atusers"]))));
    				$sid = (int)htmlspecialchars(strtolower(trim($_POST["sid"])));
    				$cid = (int)htmlspecialchars(strtolower(trim($_POST["cid"])));
    				$toid = (int)htmlspecialchars(strtolower(trim($_POST["toid"])));
    				$verificationcode = (int)htmlspecialchars(strtolower(trim($_POST["verificationcode"])));
                	$recordUserLogin = $UserLogin->find($userloginid);

    				if ($verificationcode == 999) {

    					/**
    					 * show verificaation code
    					 * 999 is a mark number , when show this num, I defined that the system shut verification off;
    					 */
    					$verificationTimeRule = C('VERIFI_COMMENT_TIME');

    					/**
    					 * show verification code ; time/second low;
    					 */
    					$lastTwoRecord = $RecordComment->where("uid = $userloginid AND sid = $sid")->field("cid,uid,sid,time,content")->order("cid DESC")->limit(2)->select();
    					if ($commentcontent == $lastTwoRecord[0]['content'] && empty($toid)) {
    						$this->ajaxReturn(0,"不要贪心噢，不能回复相同的内容","repate");
    					}
    					
    					$timediffer = $lastTwoRecord[0]['time'] - $lastTwoRecord[1]['time'];
    					if ($timediffer < $verificationTimeRule) {
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
	        					'verification Time' => $verificationTimeRule,
	        					'message' => '最后发布的两条评论时间间隔小于'.$verificationTimeRule,
    						);
    						$this->ajaxReturn($verificationTimeRuleAjaxReturn,"请输入验证码",'verifi');
    					}

    					/**
    					 * show verification code ; nums/times low;
    					 */
    					$verificationNumsRule = C('VERIFI_COMMENT_UNMS');
    					$userInsertAll = $RecordComment->where("uid = $userloginid AND time > $recordUserLogin[logintime]")->order("time DESC")->count();
    					if ($userInsertAll >= $verificationNumsRule) {

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
	        					'上线' => $verificationNumsRule,
	        					'message' => '一天发布评论总数超过上线'.$verificationNumsRule,
    						);
    						$this->ajaxReturn($verificationNumsRuleAjaxReturn,"请输入验证码",'verifi');
    					}
    				} else {
    					if ($verificationcode != $_SESSION['verificationresult']) {
    						$this->ajaxReturn(0,"验证码错误",'error');
    					}
    				}

    				$dataRecordComment = array(
	                    'cid' => NULL,
	                    'uid' => $userloginid,
	                    'sid' => $sid,
	                    'toid' => $toid,
	                    'content' => $commentcontent,
    					'image' => $imageurl,
	                    'time' => time()
    				);
    				$affetced = $RecordComment->add($dataRecordComment);

    				/**
    				 * update comment_count nums
    				 */
    				$sayRecord = $RecordSay->where("sid = $sid")->find();
    				if ($affetced) {
    					$comment_co = $sayRecord['comment_co'] + 1;

    					/**
    					 * if user > level2 update last_comment_ti
    					 */
                		$userLevel = i_degree($recordUserLogin['active']);
    					if ($userLevel >= 2) {
    						$setRecordSay = array(
    							'sid' => $sid,
	        	                'comment_co' => $comment_co,
	        	                'last_comment_ti' => time(),
    						);
    					} else {
    						$setRecordSay = array(
    							'sid' => $sid,
        	                	'comment_co' => $comment_co,
    						);
    					}
    					$RecordSay->save($setRecordSay);
    				}

    				/**
    				 *update active
    				 */
    				$UserStatus = M("UserStatus");
    				$recordUserStatus = $UserStatus->where("uid = $userloginid")->find();

    				/**
    				 * day add active limit/comment low if comment < 15 per day
    				 */
    				if ($recordUserStatus['active_c_limit'] < 15) {
    					$userStatusData = array(
    						'uid' => $userloginid,
	                        'active' => $recordUserStatus['active'] + 1,
	               	        'active_c_limit' => $recordUserStatus['active_c_limit'] + 1,
    					);
    					$UserStatus->save($userStatusData);
    					$newUserLoginActive = array(
    						'uid' => $userloginid,
	                        'active' => $recordUserLogin['active'] + 1,
    					);
    					$UserLogin->save($newUserLoginActive);

    					/**
		                 * msg active
		                 */
		                $MsgActive = M("MsgActive");
		                $msgActiveArray = array(
			            	'id' => '',
			            	'uid' => $userloginid,
			            	'total' => $recordUserLogin['active'],
			            	'change' => 1,
			            	'way' => 'add',
			            	'reason' => '评论或回复他人的记录 (每天最多加15次，包含回复帮助次数)',
			            	'time' => time(),
			            	'deliver' => 0,
			            );
			            $MsgActive->add($msgActiveArray);
    				}

    				/**
    				 * If comment , Insert record into message table
    				 */
    				if ($sayRecord['uid'] != $userloginid && $sayRecord['uid'] != $toid) {
    					$msgRecordComment = array(
	                        'id' => NULL,
	                        'uid' => $sayRecord['uid'],
	                        'sid' => $sid,
	                        'ncid' => $affetced,
	                        'rid' => $userloginid,
	                        'time' => time(),
	                        'deliver' => 0,
    					);
    					$MsgComment->add($msgRecordComment);
    				}

    				/**
    				 * If reply , Insert record into message table
    				 */
    				if (!empty($toid)) {
    					$msgReplyComment = array(
	                        'id' => NULL,
	                        'uid' => $toid,
	                        'sid' => $sid,
	                        'cid' => $cid,
	                        'ncid' => $affetced,
	                        'rid' => $userloginid,
	                        'time' => time(),
	                        'deliver' => 0,
    					);
    					$MsgComment->add($msgReplyComment);
    				}

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
			       				'sid' => $sid,
				       			'cid' => $affetced,
				       			'time' => time(),
				       			'deliver' => 0
	        				);
	        				$MsgAt->add($newAtMsgData);
	        			}
	                }

    				/**
    				 * print_r json
    				 */
	                Vendor('Ihelpoo.Emotion');
	                $emotion = new Emotion();
    				$toUserLogin = $UserLogin->find($toid);
    				$jsonEncode = array(
	                    'uid' => $userloginid,
	                    'uidnickname' => $recordUserLogin['nickname'],
    					'uidicon' => i_icon_check($userloginid, $recordUserLogin['icon_url'], 's'),
	                    'sid' => $sid,
	                    'cid' => $affetced,
	                    'toid' => $toid,
	                    'toidnickname' => $toUserLogin['nickname'],
	                    'content' => $emotion->transEmotion(stripslashes($commentcontent)),
    					'image' => $imageurl,
	                    'time' => i_time(time())
    				);
    				$noticeSendUsers = $sayRecord['uid'];
    				if (!empty($toid) && ($noticeSendUsers != $toid)) {
    					$noticeSendUsers = $sayRecord['uid'].",".$toid;
    				}
    				$this->ajaxReturn($jsonEncode,$noticeSendUsers,'yes');
    			}
    		}
    	} else {
    		if ($this->isPost()) {
    			$this->ajaxReturn(0,"登录后才能评论呢",'error');
    		}
    	}
    }

    /**
     *
     * ajax for ih post
     */
    public function helpajax()
    {
    	$userloginid = session('userloginid');
    	$RecordSay = M("RecordSay");
    	$RecordHelp = M("RecordHelp");
    	$RecordHelpreply = M("RecordHelpreply");
    	$UserLogin = M("UserLogin");
    	$MsgComment = M("MsgComment");
    	$MsgSystem = M("MsgSystem");

        /**
         * add for not login visit
         */
    	if (!empty($userloginid)) {
    		if ($this->isPost()) {
    			$validate = array(
    				array('sid', 'require', 'sid不能为空'),
    				array('helpcontent', 'require', '用户类型错误'),
    				array('toid', 'number', 'toid格式错误',2),
	    			array('recorduid', 'number', 'recorduid格式错误',2),
	    			array('hrid', 'number', '信息hrid地址格式错误',2),
    			);
    			$RecordHelpreply->setProperty("_validate", $validate);
    			$result = $RecordHelpreply->create();
    			if (!$result) {
    				$publishError = $RecordHelpreply->getError();
    				$this->ajaxReturn(0,$publishError,'error');
    			} else {
    				$sid = (int)htmlspecialchars(strtolower(trim($_POST["sid"])));
    				$toid = (int)htmlspecialchars(strtolower(trim($_POST["toid"])));
    				$hrid = (int)htmlspecialchars(strtolower(trim($_POST["hrid"])));
    				$recorduid = (int)htmlspecialchars(strtolower(trim($_POST["recorduid"])));
    				$helpcontent = i_makechickableLinks(trim(addslashes(strip_tags($_POST["textareacontent"],"<a>"))));
    				$imageurl = trim(addslashes($_POST["imageurl"]));
    				$atusers = htmlspecialchars(addslashes(strtolower(trim($_POST["atusers"]))));
    				$recordUserLogin = $UserLogin->find($userloginid);

    				$dataRecordHelpreply = array(
	                    'id' => NULL,
	                    'uid' => $userloginid,
	                    'sid' => $sid,
	                    'toid' => $toid,
	                    'content' => $helpcontent,
    					'image' => $imageurl,
	                    'time' => time()
    				);
    				$affetcedHelpreply = $RecordHelpreply->add($dataRecordHelpreply);
    				if ($affetcedHelpreply) {

    					/**
    					 * If affetced Helpreply , Insert record into Sys message table
    					 */
    					$AuMailSend = M("AuMailSend");
    					$isTimeHelpMailSend = $AuMailSend->where("uid = $recorduid AND sid = $sid AND helperid = $userloginid")->find();

    					if ($recorduid != $userloginid) {
//    						$msgHelpreplyContent = "来帮助你啦";
//    						$msgRecordHelpreply = array(
//	                            'id' => NULL,
//	                            'uid' => $recorduid,
//	                            'type' => 'stream/ih-para:newHelp',
//	                            'url_id' => $sid,
//	                            'from_uid' => $userloginid,
//	                            'content' => $msgHelpreplyContent,
//	                            'time' => time(),
//	                            'deliver' => 0,
//    						);
//    						$affetcedMsgRecordHelpreply = $MsgSystem->add($msgRecordHelpreply);
//    						if (empty($affetcedMsgRecordHelpreply)) {
//    							$this->ajaxReturn(0,'message_system_help insert failed','error');
//    						}
                            i_savenotice($userloginid, $recorduid, 'stream/ih-para:newHelp', $sid);

    						/**
    						 * send new helper info email
    						 */
        					$isMailSendRule = C('IS_SEND_MAIL');
    						if ($isMailSendRule) {
    							if (!$isTimeHelpMailSend['id']) {
    								$helpRecordOwener = $UserLogin->find($recorduid);

    								/**
    								 * send new emil helper info
    								 */
    								Vendor('Ihelpoo.Email');
                					$emailObj = new Email();
    								$emailObj->helpstatusNew($helpRecordOwener['email'], $helpRecordOwener['nickname']);
    								$newHelperInfoSendData = array(
                                        'id' => '',
            	                        'uid' => $recorduid,
            	                        'helperid' => $userloginid,
            	                        'sid' => $sid,
            	                        'type' => 2,
            	                        'time' => time(),
    								);
    								$AuMailSend->add($newHelperInfoSendData);
    							}
    						}
    					} else {
    						if (!empty($toid)) {
//    							$msgHelpreplyContent = "提出了追问";
//    							$msgToiud = $toid;
//    							$msgRecordHelpreply = array(
//	                                'id' => NULL,
//	                                'uid' => $msgToiud,
//	                                'type' => 'stream/ih-para:reply',
//	                                'url_id' => $sid,
//	                                'from_uid' => $userloginid,
//	                                'content' => $msgHelpreplyContent,
//	                                'time' => time(),
//	                                'deliver' => 0,
//    							);
//    							$affetcedMsgRecordHelpreply = $MsgSystem->add($msgRecordHelpreply);
//    							if (empty($affetcedMsgRecordHelpreply)) {
//    								$this->ajaxReturn(0,'message_system_help insert failed','error');
//    							}
                                i_savenotice($userloginid, $toid, 'stream/ih-para:reply', $sid);
    						}
    					}

    					/**
    					 * update help times(comment_count) nums
    					 */
    					$sayRecord = $RecordSay->where("sid = $sid")->find();
    					if (!$isTimeHelpMailSend['id'] && ($recorduid != $userloginid)) {
    						$comment_co = $sayRecord['comment_co'] + 1;

    						/**
    						 * if user > level2 update last_comment_ti
    						 */
			                $userLevel = i_degree($recordUserLogin['active']);
    						if ($userLevel >= 2) {
    							$helpCommentSet = array(
    								'sid' => $sid,
	        	                    'comment_co' => $comment_co,
	        	                    'last_comment_ti' => time(),
    							);
    						} else {
    							$helpCommentSet = array(
    								'sid' => $sid,
        	                    	'comment_co' => $comment_co,
    							);
    						}
    						$RecordSay->save($helpCommentSet);
    					} else {

    						/**
    						 * if user > level2 update last_comment_ti
    						 */
    						$userLevel = i_degree($recordUserLogin['active']);
    						if ($userLevel >= 2) {
    							$helpCommentSet = array(
    								'sid' => $sid,
        	                    	'last_comment_ti' => time(),
    							);
    							$RecordSay->save($helpCommentSet);
    						}
    					}

    					if ($recorduid != $userloginid) {

    						/**
    						 *update active && coins
    						 */
    						$UserStatus = M("UserStatus");
    						$recordUserStatus = $UserStatus->where("uid = $userloginid")->find();

    						/**
    						 * day add active limit/comment low if comment < 15 per day
    						 */
    						if ($recordUserStatus['active_c_limit'] < 15) {
    							$userStatusData = array(
    								'uid' => $userloginid,
	                       	        'active_c_limit' => $recordUserStatus['active_c_limit'] + 1,
    							);
    							$userLoginData = array(
    								'uid' => $userloginid,
    								'active' => $recordUserLogin['active'] + 1,
    							);
    							$UserStatus->save($userStatusData);
    							$UserLogin->save($userLoginData);

    							/**
				                 * msg active
				                 */
				                $MsgActive = M("MsgActive");
				                $msgActiveArray = array(
					            	'id' => '',
					            	'uid' => $userloginid,
					            	'total' => $recordUserLogin['active'],
					            	'change' => 1,
					            	'way' => 'add',
					            	'reason' => '回复帮助 (每天最多加15次，包含评论回复他人的记录次数)',
					            	'time' => time(),
					            	'deliver' => 0,
					            );
					            $MsgActive->add($msgActiveArray);
    						}

    					}

    					/**
    					 * update help status
    					 */
    					$isRecordHelpEnd = $RecordHelp->where("sid = $sid")->find();
    					if ($isRecordHelpEnd['status'] != 3) {
    						$dataRecordHelp = array(
    							'hid' => $isRecordHelpEnd['hid'],
                            	'status' => 2,
    						);
    						$tempFlag = $RecordHelp->save($dataRecordHelp);
    					}

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
				       				'sid' => $sid,
					       			'cid' => $affetcedHelpreply,
					       			'time' => time(),
					       			'deliver' => 0
		        				);
		        				$MsgAt->add($newAtMsgData);
		        			}
		                }
    				}

    				/**
    				 * print_r json
    				 */
    				if (!empty($toid)) {
    					$toUserLogin = $UserLogin->find($toid);
    					$toUserLoginNickname = $toUserLogin['nickname'];
    				} else {
    					$toUserLoginNickname = '';
    				}
    				$helperUserLogin = $UserLogin->find($userloginid);
    				$jsonEncode = array(
	                    'uid' => $userloginid,
	                    'uidnickname' => $helperUserLogin['nickname'],
    					'uidicon' => i_icon_check($userloginid, $helperUserLogin['icon_url'], 's'),
	                    'sid' => $sid,
	                    'toid' => $toid,
	                    'toidnickname' => $toUserLoginNickname,
	                    'content' => $helpcontent,
    					'image' => $imageurl,
	                    'time' => date("Y-m-d H:i", time()),
    				);
    				$noticeSendUsers = $sayRecord['uid'];
    				if (!empty($toid) && ($noticeSendUsers != $toid)) {
    					$noticeSendUsers = $sayRecord['uid'].",".$toid;
    				}
    				$this->ajaxReturn($jsonEncode, $noticeSendUsers, 'yes');
    			}
    		}

    		/**
    		 * add for not login visit
    		 */
    	} else {
    		if ($this->isPost()) {
    			$this->ajaxReturn(0,"登录后才能帮助呢",'error');
    		}
    	}
    }

    /**
     *
     * For ajax del function
     */
    public function del()
    {
        $userloginid = session('userloginid');
        if (empty($userloginid)) {
        	$this->ajaxReturn(0,"您还没有登录呢",'wrong');
        }
        $RecordSay = M("RecordSay");
        $RecordComment = M("RecordComment");
        $RecordHelp = M("RecordHelp");
        $RecordHelpreply = M("RecordHelpreply");
        $RecordDiffusion = M("RecordDiffusion");
        if (!empty($_POST['delrecord'])) {
            $delRecordId = (int)trim($_POST['delrecord']);
            $deleteRecord = $RecordSay->where("sid = $delRecordId")->find();
            if ($deleteRecord['say_type'] == 1) {
            	$delRecordAffected = $RecordSay->where("sid = $delRecordId AND uid = $userloginid")->delete();
            	if ($delRecordAffected) {
            	    $RecordHelp->where("sid = $delRecordId")->delete();
            	    $RecordHelpreply->where("sid = $delRecordId")->delete();
            	}
            } else {
                $delRecordAffected = $RecordSay->where("sid = $delRecordId AND uid = $userloginid")->delete();
                if ($delRecordAffected) {
                	$RecordComment->where("sid = $delRecordId")->delete();
                }
            }
            if (!$delRecordAffected) {
                $this->ajaxReturn(0,"没有权限,删除出错啦",'wrong');
            }

            /**
             * delete diffusion
             */
            $RecordDiffusion->where("sid = $delRecordId")->delete();
            $this->ajaxReturn(1,"删除成功,3秒后自动跳转",'ok');
        }

        if (!empty($_POST['delcomment'])) {
            $delCommentId =  (int)trim($_POST['delcomment']);
            $recordSayId = $RecordComment->where("cid = $delCommentId")->find();
            $recordSay = $RecordSay->where("sid = $recordSayId[sid]")->find();
            if ($recordSay['uid'] == $userloginid) {
                $delCommentAffected = $RecordComment->where("cid = $delCommentId")->delete();
            } else {
                $delCommentAffected = $RecordComment->where("cid = $delCommentId AND uid = $userloginid")->delete();
            }
            if (!$delCommentAffected) {
                $this->ajaxReturn(0,'没有权限,删除评论出错啦','wrong');
            }
            $this->ajaxReturn(1,'删除回复成功','ok');
        }

        if (!empty($_POST['delhelpreply'])) {
            $delHelpreplyId = (int)trim($_POST['delhelpreply']);
            $recordSayId = $RecordHelpreply->where("id = $delHelpreplyId")->find();
            $recordSay = $RecordSay->where("sid = $recordSayId[sid]")->find();
            if ($recordSay['uid'] == $userloginid) {
                $delHelpreplyAffected = $RecordHelpreply->where("id = $delHelpreplyId")->delete();
            }
            if (!$delHelpreplyAffected) {
            	if ($recordSayId['uid'] == $userloginid) {
            		$RecordHelpreply->where("id = $delHelpreplyId")->delete();
            		$this->ajaxReturn(1,'删除成功','ok');
            	}
                $this->ajaxReturn(0,'没有权限,删除回复出错啦','wrong');
            }
            $this->ajaxReturn(1,'删除成功','ok');
        }
        exit();
    }

}

?>