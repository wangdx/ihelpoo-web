<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class ActivityAction extends Action {

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

    public function index()
    {
    	$this->assign('title','活动');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
    	$ActivityItem = M("ActivityItem");
    	$daybeforetime = time() - 86400;
    	$recordActivityItem = $ActivityItem->where("i_activity_item.status = 1 AND i_activity_item.activity_ti > $daybeforetime AND i_activity_item.school_id = $recordSchoolInfo[id]")
    	->join('i_user_login ON i_activity_item.sponsor_uid = i_user_login.uid')
    	->field('aid,i_activity_item.status,run_type,subject,sponsor_uid,activity_ti,join_num,hit,content,sid,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
    	->order('activity_ti ASC')->find();
    	if (!empty($recordActivityItem['aid'])) {
    		$this->assign('recordActivityItem',$recordActivityItem);
    	}

    	/**
    	 * activity nums
    	 */
    	$time = time();
    	$totalActivityNums = $ActivityItem->where("school_id = $recordSchoolInfo[id]")->count();
    	$totalOkActivityNums = $ActivityItem->where("status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	$afterActivityNums = $ActivityItem->where("status = 1 AND $time > activity_ti AND school_id = $recordSchoolInfo[id]")->count();
    	$beforeActivityNums = $ActivityItem->where("status = 1 AND $time < activity_ti AND school_id = $recordSchoolInfo[id]")->count();
    	$this->assign('totalActivityNums',$totalActivityNums);
    	$this->assign('totalOkActivityNums',$totalOkActivityNums);
    	$this->assign('afterActivityNums',$afterActivityNums);
    	$this->assign('beforeActivityNums',$beforeActivityNums);

    	/**
    	 * show activity join user info
    	 */
    	if (!empty($recordActivityItem)) {
	    	$ActivityUser = M("ActivityUser");
	    	$recordsActivityUser = $ActivityUser->where("aid = $recordActivityItem[aid]")
	    	->join('i_user_login ON i_activity_user.uid = i_user_login.uid')
	    	->field('id,aid,i_activity_user.uid,partner_uid,invite_status,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
	    	->order('time DESC')
	    	->select();
	    	$this->assign('recordsActivityUser',$recordsActivityUser);
    	}
    	$this->display();
    }

    public function add()
    {
    	$userloginid = session('userloginid');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	if (empty($userloginid)) {
    		redirect('/user/notlogin', 0, '你还没有登录呢...');
    	}
    	$this->assign('title','组织新活动');
    	$ActivityItem = M("ActivityItem");
    	$activityid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if ($this->isPost()) {
    		$aid = (int)trim(strip_tags($_POST["aid"]));
    		$subject = trim(addslashes(htmlspecialchars(strip_tags($_POST["subject"]))));
    		$activity_ti = trim(htmlspecialchars(strip_tags($_POST["activity_ti"])));
    		$run_type = (int)trim(htmlspecialchars(strip_tags($_POST["run_type"])));
    		$content = trim(addslashes($_POST["content"]));
    		if (empty($subject)) {
    			redirect('/activity/add/', 3, '活动主题不能为空! 3秒后页面跳转...');
    		}
    		if (empty($activity_ti)) {
    			redirect('/activity/add/', 3, '活动时间不能为空! 3秒后页面跳转...');
    		}
    		$activityTimeLength = strlen($activity_ti);
    		if ($activityTimeLength != 10) {
    			redirect('/activity/add/', 3, '活动时间格式不对，请参考时间事例! 3秒后页面跳转...');
    		}
    		$year = (int)substr($activity_ti,0,4);
    		$month = (int)substr($activity_ti,5,2);
    		$day = (int)substr($activity_ti,8,2);
    		$activity_time = mktime(0,0,0,$month,$day,$year);
    		if ($activity_time < (time() - 86400)) {
    			redirect('/activity/add/', 3, '活动时间不能少于当前时间:( 3秒后页面跳转...');
    		}
    		if (empty($content)) {
    			redirect('/activity/add/', 3, '活动内容不能为空! 3秒后页面跳转...');
    		}
    		if (!empty($aid)) {
    			$editActivityItemArray = array(
	    			'aid' => $aid,
	    			'status' => 0,
    				'run_type' => $run_type,
	    			'subject' => $subject,
	    			'activity_ti' => $activity_time,
	    			'content' => $content,
	    			'school_id' => $recordSchoolInfo['id'],
	    			'time' => time()
	    		);
	    		$ActivityItem->save($editActivityItemArray);
	    		redirect('/activity/item/'.$aid, 3, '编辑活动成功，等待重新审核通过后，大家就能参加啦! 3秒后页面跳转...');
    		} else {
	    		$newActivityItemArray = array(
	    			'aid' => '',
	    			'status' => 0,
	    			'subject' => $subject,
	    			'sponsor_uid' => $userloginid,
	    			'activity_ti' => $activity_time,
	    			'content' => $content,
	    			'school_id' => $recordSchoolInfo['id'],
	    			'time' => time()
	    		);
	    		$newInsertActivityId = $ActivityItem->add($newActivityItemArray);
	    		redirect('/activity/item/'.$newInsertActivityId, 3, '发布活动成功，等待审核通过后，大家就能参加啦! 3秒后页面跳转...');
    		}
    	}

    	/**
    	 * edit activity
    	 */
    	if (!empty($activityid)) {
    		$editActivityItem = $ActivityItem->where("aid = $activityid")->find();
    		if (($editActivityItem['sponsor_uid'] == $userloginid) || ($userloginid == 10000)) {
    			if (!empty($editActivityItem)) {
    				$this->assign('editActivityItem', $editActivityItem);
    			}
    		} else {
    			redirect('/activity/add/', 3, '你不是活动主办方，没有编辑权限...');
    		}
    	}

    	$this->display();
    }

	public function item()
    {
    	$this->assign('title','活动详情');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
    	$ActivityItem = M("ActivityItem");
    	$ActivityUser = M("ActivityUser");
    	$activityid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$recordActivityItem = $ActivityItem->where("aid = $activityid")
    	->join('i_user_login ON i_activity_item.sponsor_uid = i_user_login.uid')
    	->field('aid,i_activity_item.status,run_type,subject,sponsor_uid,activity_ti,join_num,hit,content,sid,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
    	->find();
    	if (!empty($recordActivityItem['aid'])) {
    		$this->assign('recordActivityItem',$recordActivityItem);
    	} else {
    		redirect('/activity/lists/', 3, '活动不存在:( 3秒后页面跳转...');
    	}

    	/**
    	 * join activity
    	 */
    	if (!empty($_GET['action'])) {
    		if (empty($userloginid)) {
    			redirect('/user/notlogin', 0, '你还没有登录呢...');
    		}
    		if ($recordActivityItem['status'] != 1) {
    			redirect('/activity/item/'.$recordActivityItem['aid'], 3, '该活动没有通过审核，暂时不能加入:( 3秒后页面跳转...');
    		}
    		$getAction = $_GET['action'];
    		if ($getAction == 'jionagree') {
    			$UserInfo = M("UserInfo");
    			$UserLogin = M("UserLogin");
    			$recordUserInfo = $UserInfo->where("uid = $userloginid")->find();
    			if (empty($recordUserInfo['qq']) || empty($recordUserInfo['mobile'])) {
    				$this->assign('userinfolack',true);
    			} else {
    				$recordActivityUser = $ActivityUser->where("aid = $activityid AND uid = $userloginid")->find();
    				if (!empty($recordActivityUser['id'])) {
    					redirect('/activity/item/'.$recordActivityUser['aid'], 3, '您已经加入该活动:) 3秒后页面跳转...');
    				}
    				$newActivityUserArray = array(
    					'id' => '',
    					'aid' => $activityid,
    					'uid' => $userloginid,
    					'partner_uid' => '',
    					'invite_status' => 0,
    					'time' => time(),
    				);
    				$ActivityUser->add($newActivityUserArray);

    				/**
    				 * update join_num
    				 */
    				$updateActivityItem = array(
    					'aid' => $recordActivityItem['aid'],
    					'join_num' => $recordActivityItem['join_num'] + 1,
    				);
    				$ActivityItem->save($updateActivityItem);

    				/**
    				 * change user info
    				 */
    				$UserChangeinfo = M("UserChangeinfo");
    				$existRecordUserChangeinfo = $UserChangeinfo->where("uid = $userloginid AND withid = $recordActivityItem[sponsor_uid]")->find();
    				if (empty($existRecordUserChangeinfo['id'])) {
    					$dataChangeinfo = array(
		            	    'id' => '',
		            	    'uid' => $userloginid,
		            	    'withid' => $recordActivityItem['sponsor_uid'],
		            	    'time' => time(),
    					);
    					$UserChangeinfo->add($dataChangeinfo);
    				}

    				$existRecordUserChangeinfo2 = $UserChangeinfo->where("uid = $recordActivityItem[sponsor_uid] AND withid = $userloginid")->find();
    				if (empty($existRecordUserChangeinfo2['id'])) {
    					$dataChangeinfo2 = array(
		            	    'id' => '',
		            	    'uid' => $recordActivityItem['sponsor_uid'],
		            	    'withid' => $userloginid,
		            	    'time' => time(),
    					);
    					$UserChangeinfo->add($dataChangeinfo2);
    				}


    				if ($userloginid != $recordActivityItem['sponsor_uid']) {
	    				/**
	    				 * send message to sponsor
	    				 */
	    				$recordUserLogin = $UserLogin->where("uid = $userloginid")->find();
	    				$MsgSystem = M("MsgSystem");
	    				$msgContent = "<a class='getuserinfo' userid=".$recordUserLogin['uid'].">".$recordUserLogin['nickname']."</a> 加入了您组织的活动 “".$recordActivityItem['subject']."”，你可以查看其真实联系方式!";
			            $msgData = array(
		                	'id' => NULL,
		                	'uid' => $recordActivityItem['sponsor_uid'],
		                 	'type' => 'system',
		              		'content' => $msgContent,
		                	'time' => time(),
		                	'deliver' => 0,
			            );
			            $MsgSystem->add($msgData);

			            /**
	    				 * send message to jioner
	    				 */
			            $msgContent = "您加入了活动 “".$recordActivityItem['subject']."”，可以查看主办方真实联系方式!";
			            $msgData = array(
		                	'id' => NULL,
		                	'uid' => $userloginid,
		                 	'type' => 'system',
		              		'content' => $msgContent,
		                	'time' => time(),
		                	'deliver' => 0,
			            );
			            $MsgSystem->add($msgData);
    				}
    				redirect('/activity/item/'.$activityid, 3, '成功加入活动:) 3秒后页面跳转...');
    			}
    		}

    		/**
    		 * choose parter
    		 */
    		if ($getAction == "selectsure") {

    			$recordActivityUser = $ActivityUser->where("aid = $activityid AND uid = $userloginid")->find();
    			if (empty($recordActivityUser['id'])) {
    				redirect('/activity/item/'.$activityid, 3, '需要先加入此次活动, 才能选择Parter:) 3秒后页面跳转...');
    			}

    			$parteruid = $_GET['parter'];
    			if (!empty($parteruid)) {
    				$ActivityUserinvite = M("ActivityUserinvite");

    				$recordSelfActivityUser = $ActivityUser->where("aid = $activityid AND uid = $userloginid")->find();
    				if ($recordSelfActivityUser['invite_status'] == 2) {
    					redirect('/activity/item/'.$activityid, 3, '你已经有了Parter了哦 :) 3秒后页面跳转...');
    				}

    				$recordSingleActivityUserinvite = $ActivityUserinvite->where("aid = $activityid AND invite_uid = $userloginid")->find();
    				if (!empty($recordSingleActivityUserinvite['id'])) {
    					$recordSingleActivityUser = $ActivityUser->where("aid = $activityid AND uid = $recordSingleActivityUserinvite[uid]")->find();
    				}
    				if ($recordSingleActivityUser['invite_status'] == 1) {
    					redirect('/activity/item/'.$activityid, 3, '你已经选择了一个Parter 对方拒绝后才能选择第二个 :) 3秒后页面跳转...');
    				}

    				$recordActivityUserinvite = $ActivityUserinvite->where("uid = $parteruid AND aid = $activityid AND invite_uid = $userloginid")->find();
    				if (!empty($recordActivityUserinvite['id'])) {
    					redirect('/activity/item/'.$activityid, 3, '你已经选择对方为Parter 等待对方确认 :) 3秒后页面跳转...');
    				}

    				$newActivityUserinviteData = array(
    					'id' => '',
    					'aid' => $activityid,
    					'uid' => $parteruid,
    					'invite_uid' => $userloginid,
    					'time' => time(),
    				);
    				$ActivityUserinvite->add($newActivityUserinviteData);

    				/**
    				 * update ActivityUser invite_status
    				 */
    				$recordActivityUser = $ActivityUser->where("aid = $activityid AND uid = $parteruid")->find();
    				$updateActivityUserInvitestatus = array(
    					'id' => $recordActivityUser['id'],
    					'invite_status' => 1,
    				);
    				$ActivityUser->save($updateActivityUserInvitestatus);

    				/**
    				 * send msg system
    				 */
//    				$MsgSystem = M("MsgSystem");
//    				$msgSystemType = 'activity/item-para:invite';
//    				$contentMsgSystem = "邀请你成为他的活动Parter!";
//    				$pushMsgData = array(
//	        	    	'id' => '',
//	       	            'uid' => $parteruid,
//            	        'type' => $msgSystemType,
//	        	    	'url_id' => $activityid,
//	        	    	'from_uid' => $userloginid,
//	        	    	'content' => $contentMsgSystem,
//	        	    	'time' => time(),
//	        	    	'deliver' => 0,
//    				);
//    				$MsgSystem->add($pushMsgData);
                    i_savenotice($userloginid, $parteruid, 'activity/item-para:invite', $activityid);//TODO ajax, bounce
    				redirect('/activity/item/'.$activityid, 3, '成功选择Parter 等待对方确认 :) 3秒后页面跳转...');
    			}
    		}
    	}

    	/**
    	 * update hit
    	 */
    	$updateActivityItemArray = array(
    		'aid' => $recordActivityItem['aid'],
    		'hit' => $recordActivityItem['hit'] + 1,
    	);
    	$ActivityItem->save($updateActivityItemArray);

    	/**
    	 * show activity join user info
    	 */
    	$page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
    	$recordsActivityUser = $ActivityUser->where("aid = $recordActivityItem[aid]")
    	->join('i_user_login ON i_activity_user.uid = i_user_login.uid')
    	->field('id,aid,i_activity_user.uid,partner_uid,invite_status,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
    	->limit($offset,$count)->order('time DESC')->select();

    	$this->assign('recordsActivityUser',$recordsActivityUser);
    	$this->assign('recordsActivityComment',$recordsActivityComment);

    	/**
         * page link
         */
    	$totalRecordsNums = $ActivityUser->where("aid = $recordActivityItem[aid]")->count();
        $this->assign('totalRecordsNums', $totalRecordsNums);
        $totalPages = ceil($totalRecordsNums / $count);
        $this->assign('totalpages', $totalPages);
        $this->assign('pageCount', $count);

        /**
         * show parter
         */
        if ($recordActivityItem['run_type'] == '2' ) {
        	$parterActivityUsers = $ActivityUser->where("aid = $recordActivityItem[aid] AND uid != '' AND partner_uid !=''")->select();
        	$parterUserArray = array();
        	$plusUserIdArray = array();
        	foreach ($parterActivityUsers as $parterActivityUser) {
        		$plusUserId = $parterActivityUser['uid'] + $parterActivityUser['partner_uid'];
        		$parterUserArrayIn = array(
	        		'uid' => $parterActivityUser['uid'],
	        		'partner_uid' => $parterActivityUser['partner_uid'],
	        		'time' => $parterActivityUser['time'],
        		);
        		if (!in_array($plusUserId, $plusUserIdArray)) {
        			$parterUserArray[] = $parterUserArrayIn;
        			$plusUserIdArray[] = $plusUserId;
        		}
        	}
        	$this->assign('parterUserArray', $parterUserArray);
        }

    	$this->display();
    }

    public function parterinvite()
    {
    	$this->assign('title','活动Parter邀请');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
    	if (empty($userloginid)) {
    		redirect('/user/notlogin', 0, '你还没有登录呢...');
    	}
    	$ActivityItem = M("ActivityItem");
    	$ActivityUser = M("ActivityUser");
    	$ActivityUserinvite = M("ActivityUserinvite");
    	$activityid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$inviteuid = (int)htmlspecialchars(trim($_GET["_URL_"][3]));
    	$recordActivityItem = $ActivityItem->where("aid = $activityid")->find();
    	if (!empty($recordActivityItem['aid'])) {
    		$this->assign('recordActivityItem',$recordActivityItem);
    	} else {
    		redirect('/activity/lists/', 3, '活动不存在:( 3秒后页面跳转...');
    	}
    	$recordActivityUser = $ActivityUser->where("aid = $activityid AND uid = $userloginid")->find();
    	if (empty($recordActivityUser['id'])) {
    		redirect('/activity/item/'.$activityid, 3, '您还没有加入该活动:( 3秒后页面跳转...');
    	}

    	/**
    	 * accept handle
    	 */
    	if (!empty($_GET['acceptid'])) {
    		if ($recordActivityUser['invite_status'] == 2) {
    			redirect('/activity/item/'.$activityid, 3, '您已经有了活动Parter，不能太贪心噢 :D 3秒后页面跳转...');
    		}
    		$acceptActivityUserinvite = $ActivityUserinvite->find($_GET['acceptid']);
    		if (!empty($acceptActivityUserinvite['id'])) {
    			$inviteuserid = $acceptActivityUserinvite['invite_uid'];
    			$acceptActivityUser = $ActivityUser->where("aid = $acceptActivityUserinvite[aid] AND uid = $userloginid")->find();
    			$acceptUpdateActivityUserArray = array(
	    			'id' => $acceptActivityUser['id'],
	    			'partner_uid' => $inviteuserid,
	    			'invite_status' => 2,
    			);
    			$ActivityUser->save($acceptUpdateActivityUserArray);

    			$accept2ActivityUser = $ActivityUser->where("aid = $acceptActivityUserinvite[aid] AND uid = $inviteuserid")->find();
    			$accept2UpdateActivityUserArray = array(
	    			'id' => $accept2ActivityUser['id'],
	    			'partner_uid' => $userloginid,
	    			'invite_status' => 2,
    			);
    			$ActivityUser->save($accept2UpdateActivityUserArray);

    			/**
    			 * change user info
    			 */
    			$UserChangeinfo = M("UserChangeinfo");
    			$existRecordUserChangeinfo = $UserChangeinfo->where("uid = $userloginid AND withid = $inviteuserid")->find();
    			if (empty($existRecordUserChangeinfo['id'])) {
    				$dataChangeinfo = array(
		            	    'id' => '',
		            	    'uid' => $userloginid,
		            	    'withid' => $inviteuserid,
		            	    'time' => time(),
    				);
    				$UserChangeinfo->add($dataChangeinfo);
    			}

    			$existRecordUserChangeinfo2 = $UserChangeinfo->where("uid = $inviteuserid AND withid = $userloginid")->find();
    			if (empty($existRecordUserChangeinfo2['id'])) {
    				$dataChangeinfo2 = array(
		            	    'id' => '',
		            	    'uid' => $inviteuserid,
		            	    'withid' => $userloginid,
		            	    'time' => time(),
    				);
    				$UserChangeinfo->add($dataChangeinfo2);
    			}

    			/**
    			 * send message to sponsor
    			 */
    			$UserLogin = M("UserLogin");
    			$recordUserLogin = $UserLogin->where("uid = $userloginid")->find();
    			$MsgSystem = M("MsgSystem");
    			$msgContent = "<a class='getuserinfo' userid=".$recordUserLogin['uid'].">".$recordUserLogin['nickname']."</a> 成为了您参加的活动 “".$recordActivityItem['subject']."” 的Parter，你们可以互相查看真实联系方式了!";
    			$msgData = array(
	                	'id' => NULL,
	                	'uid' => $inviteuserid,
	                 	'type' => 'system',
	              		'content' => $msgContent,
	                	'time' => time(),
	                	'deliver' => 0,
    			);
    			$MsgSystem->add($msgData);

    			/**
    			 * send message to jioner
    			 */
    			$recordInviteUserLogin = $UserLogin->where("uid = $inviteuserid")->find();
    			$msgContent = "<a class='getuserinfo' userid=".$recordInviteUserLogin['uid'].">".$recordInviteUserLogin['nickname']."</a> 成为了您参加的活动 “".$recordActivityItem['subject']."” 的Parter，你们可以互相查看真实联系方式了!";
    			$msgData = array(
	                	'id' => NULL,
	                	'uid' => $userloginid,
	                 	'type' => 'system',
	              		'content' => $msgContent,
	                	'time' => time(),
	                	'deliver' => 0,
    			);
    			$MsgSystem->add($msgData);
    			redirect('/activity/item/'.$activityid, 3, '选择Parter成功 :) 3秒后页面跳转...');
    		}
    	}

    	/**
    	 * activity info
    	 */
    	$recordsActivityUserinvite = $ActivityUserinvite->where("i_activity_userinvite.uid = $userloginid AND aid = $activityid")
    	->join('i_user_login ON i_activity_userinvite.invite_uid = i_user_login.uid')
    	->field('id,aid,i_activity_userinvite.uid,i_activity_userinvite.invite_uid,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
    	->select();
    	$this->assign('recordsActivityUserinvite',$recordsActivityUserinvite);
    	$this->display();
    }

    public function itemnotice()
    {
    	$this->assign('title','活动通知');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
    	$ActivityItem = M("ActivityItem");
    	$ActivityUser = M("ActivityUser");
    	$ActivityNotice = M("ActivityNotice");
    	$activityid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$recordActivityItem = $ActivityItem->where("aid = $activityid")->find();
    	if (!empty($recordActivityItem['aid'])) {
    		$this->assign('recordActivityItem',$recordActivityItem);
    	} else {
    		redirect('/activity/lists/', 3, '活动不存在:( 3秒后页面跳转...');
    	}

    	/**
    	 * show activity join user info
    	 */
    	$recordsActivityUser = $ActivityUser->where("aid = $recordActivityItem[aid]")
    	->join('i_user_login ON i_activity_user.uid = i_user_login.uid')
    	->field('id,aid,i_activity_user.uid,partner_uid,invite_status,time,email,nickname,sex,birthday,enteryear,type,online,active,icon_url')
    	->order('time DESC')
    	->select();

    	if ($this->isPost()) {
    		if ($userloginid != $recordActivityItem['sponsor_uid']) {
    			redirect('/activity/item/'.$recordActivityItem['aid'], 3, '你没有该活动发布通知权限:( 3秒后页面跳转...');
    		}
    		$aid = (int)trim(strip_tags($_POST["aid"]));
    		$activitynotice = trim(addslashes(htmlspecialchars(strip_tags($_POST["activitynotice"]))));
    		if (!empty($aid) && !empty($activitynotice)) {
    			$TalkContent = M("TalkContent");
    			Vendor('Ihelpoo.Email');
				$emailObj = new Email();
    			foreach ($recordsActivityUser as $activityUser) {

	    			/**
	    			 * send msg talk
	    			 */
	    			$dataTalkContent = array(
			            'id' => '',
			            'uid' => $userloginid,
			            'touid' => $activityUser['uid'],
			            'content' => $activitynotice."<span class='f12 gray'>(“".$recordActivityItem['subject']."”活动通知)</span>",
		    			'image' => '',
			            'deliver' => 0,
			            'del' => 0,
			           	'time' => time(),
	    			);
	    			if ($activityUser['uid'] != $userloginid){
	    				$TalkContent->add($dataTalkContent);
	    			}

	    			/**
	    			 * send mail
	    			 */
	    			if (!empty($activityUser['email'])) {
	    				$emailObj->activityNotice($activityUser['email'], $activityUser['nickname'], $activitynotice, $recordActivityItem['aid'], $recordActivityItem['subject']);
	    			}

    			}
    			$newActivityNoticeData = array(
    				'id' => '',
    				'aid' => $recordActivityItem['aid'],
    				'content' => $activitynotice,
    				'number' => $recordActivityItem['join_num'],
    				'time' => time()
    			);
    			$ActivityNotice->add($newActivityNoticeData);
    			redirect('/activity/item/'.$recordActivityItem['aid'], 3, '发送通知成功:) 3秒后页面跳转...');
    		}
    	}

    	$this->assign('recordActivityItem',$recordActivityItem);
    	$this->assign('recordsActivityUser',$recordsActivityUser);
    	$recordsActivityNotice = $ActivityNotice->where("aid = $recordActivityItem[aid]")->order("time DESC")->select();
    	$this->assign('recordsActivityNotice',$recordsActivityNotice);
    	$this->display();
    }

	public function lists()
    {
    	$this->assign('title','活动列表');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
    	$ActivityItem = M("ActivityItem");

    	$page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;

        if (!empty($_GET['self'])) {
        	$getSelf = $_GET['self'];
        } else {
        	$getSelf = 'empty';
        }
        $this->assign('getSelf', $getSelf);

        if ($getSelf == 'sponsor') {
        	if (empty($userloginid)) {
        		redirect('/user/notlogin', 0, '你还没有登录呢...');
        	}
        	$recordsActivityItem = $ActivityItem->where("sponsor_uid = $userloginid AND i_activity_item.school_id = $recordSchoolInfo[id]")->limit($offset,$count)->order('time DESC')->select();
        	$totalRecordsNums = $ActivityItem->where("sponsor_uid = $userloginid AND i_activity_item.school_id = $recordSchoolInfo[id]")->count();
        } else if ($getSelf == 'mine') {
        	if (empty($userloginid)) {
        		redirect('/user/notlogin', 0, '你还没有登录呢...');
        	}
        	$ActivityUser = M("ActivityUser");
        	$recordsActivityItem = $ActivityItem->where("i_activity_item.status = 1 AND i_activity_user.uid = $userloginid AND i_activity_item.school_id = $recordSchoolInfo[id]")
        	->join('i_user_login ON i_activity_item.sponsor_uid = i_user_login.uid')
        	->join('i_activity_user ON i_activity_item.aid = i_activity_user.aid')
        	->field('i_activity_item.aid,i_activity_item.status,subject,sponsor_uid,activity_ti,join_num,hit,content,good_nu,bad_nu,i_activity_item.time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        	->limit($offset,$count)
        	->order('activity_ti DESC')
        	->select();
    		$totalRecordsNums = $ActivityUser->where("uid = $userloginid")->count();
        } else {
        	$recordsActivityItem = $ActivityItem->where("i_activity_item.status = 1 AND i_activity_item.school_id = $recordSchoolInfo[id]")
        	->join('i_user_login ON i_activity_item.sponsor_uid = i_user_login.uid')
        	->field('aid,i_activity_item.status,subject,sponsor_uid,activity_ti,join_num,hit,content,good_nu,bad_nu,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        	->limit($offset,$count)
        	->order('activity_ti DESC')
        	->select();
    		$totalRecordsNums = $ActivityItem->where("i_activity_item.status = 1 AND i_activity_item.school_id = $recordSchoolInfo[id]")->count();
        }
    	if (!empty($recordsActivityItem)) {
    		$this->assign('recordsActivityItem',$recordsActivityItem);
    	}

        /**
         * page link
         */
        $this->assign('totalRecordsNums', $totalRecordsNums);
        $totalPages = ceil($totalRecordsNums / $count);
        $this->assign('totalpages', $totalPages);
        $this->assign('pageCount', $count);
    	$this->display();
    }

    public function lotterydraw()
    {
    	$this->assign('title','活动抽奖');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
    	$ActivityItem = M("ActivityItem");
    	$ActivityUser = M("ActivityUser");
    	$ActivityLotterydraw = M("ActivityLotterydraw");
    	$activityid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$recordActivityItem = $ActivityItem->where("aid = $activityid")->find();
    	if (!empty($recordActivityItem['aid'])) {
    		$this->assign('recordActivityItem',$recordActivityItem);
    	} else {
    		redirect('/activity/lists/', 3, '活动不存在:( 3秒后页面跳转...');
    	}

    	/**
    	 * show activity join user info
    	 */
    	$recordsActivityUser = $ActivityUser->where("aid = $recordActivityItem[aid]")
    	->join('i_user_login ON i_activity_user.uid = i_user_login.uid')
    	->field('id,aid,i_activity_user.uid,partner_uid,invite_status,time,email,nickname,sex,birthday,enteryear,type,online,active,icon_url')
    	->order('time DESC')
    	->select();

    	if ($this->isPost()) {
    		if ($userloginid != $recordActivityItem['sponsor_uid']) {
    			redirect('/activity/lotterydraw/'.$recordActivityItem['aid'], 3, '你没有该活动抽奖权限:( 3秒后页面跳转...');
    		}
    		$aid = (int)trim(strip_tags($_POST["aid"]));
    		$usernumbers = (int)trim(strip_tags($_POST["usernumbers"]));
    		$userArrayNumbers = count($recordsActivityUser);
    		if ($usernumbers > $userArrayNumbers){
    			redirect('/activity/lotterydraw/'.$recordActivityItem['aid'], 3, '抽奖人数不能大于参加活动人数:( 3秒后页面跳转...');
    		}
    		$lotterydrawActivityUser = array_rand($recordsActivityUser,$usernumbers);
    		if (is_int($lotterydrawActivityUser)) {
    			$userids = "<a href='".__ROOT__."/wo/".$recordsActivityUser[$lotterydrawActivityUser]['uid']."'>".$recordsActivityUser[$lotterydrawActivityUser]['nickname']."</a>";
    		} else {
    			$userids = NULL;
	    		foreach ($lotterydrawActivityUser as $lotterydrawid){
	    			$userids .= "<a href='".__ROOT__."/wo/".$recordsActivityUser[$lotterydrawid]['uid']."'>".$recordsActivityUser[$lotterydrawid]['nickname']."</a> ";
	    		}
    		}

    		$newActivityLotterydrawData = array(
    			'id' => '',
    			'aid' => $recordActivityItem['aid'],
    			'userids' => $userids,
    			'time' => time()
    		);
    		$ActivityLotterydraw->add($newActivityLotterydrawData);
    		redirect('/activity/lotterydraw/'.$recordActivityItem['aid'], 3, '恭喜,抽奖成功 :) 3秒后页面跳转...');
    	}

    	$this->assign('recordsActivityUser',$recordsActivityUser);
    	$recordsActivityLotterydraw = $ActivityLotterydraw->where("aid = $recordActivityItem[aid]")->order("time DESC")->select();
    	$this->assign('recordsActivityLotterydraw',$recordsActivityLotterydraw);
    	$this->display();
    }

    public function uploadappendix()
    {
    	$this->assign('title','活动附件');
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
    	if (empty($userloginid)) {
    		redirect('/user/notlogin', 0, '你还没有登录呢...');
    	}
    	$ActivityItem = M("ActivityItem");
    	$ActivityUser = M("ActivityUser");
    	$activityid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$recordActivityItem = $ActivityItem->where("aid = $activityid")->find();
    	if (!empty($recordActivityItem['aid'])) {
    		$this->assign('recordActivityItem',$recordActivityItem);
    	} else {
    		redirect('/activity/lists/', 3, '活动不存在:( 3秒后页面跳转...');
    	}
    	$recordActivityUser = $ActivityUser->where("aid = $activityid AND uid = $userloginid")->find();
    	if (empty($recordActivityUser['id'])) {
    		redirect('/activity/item/'.$activityid, 3, '参加该活动后才能上传相关附件:( 3秒后页面跳转...');
    	}
    	$ActivityAppendix = M("ActivityAppendix");

    	if ($recordActivityItem['sponsor_uid'] != $userloginid) {
	    	$recordsActivityAppendix = $ActivityAppendix->where("i_activity_appendix.uid = $userloginid")
	    	->join('i_user_login ON i_activity_appendix.uid = i_user_login.uid')
	    	->field('id,aid,i_activity_appendix.uid,url,time,email,nickname,sex,birthday,enteryear,type,online,active,icon_url')
	    	->order("time DESC")->select();
    	} else {
	    	$recordsActivityAppendix = $ActivityAppendix->join('i_user_login ON i_activity_appendix.uid = i_user_login.uid')
	    	->field('id,aid,i_activity_appendix.uid,url,time,email,nickname,sex,birthday,enteryear,type,online,active,icon_url')
	    	->order('time DESC')->select();
    	}
    	$this->assign('recordsActivityAppendix',$recordsActivityAppendix);
    	$this->display();
    }

    public function ajaxuploadappendix()
    {
    	$userloginid = session('userloginid');
    	if (empty($userloginid)) {
    		$this->ajaxReturn(0,'没登录','error');
    	}
    	if ($this->isPost()) {

    		/**
    		 * insert into activity
    		 */

    		if (!empty($_POST['activityid']) && !empty($_POST['appendixurl'])) {
    			$ActivityAppendix = M("ActivityAppendix");
    			$newActivityAppendixData = array(
    				'id' => '',
    				'aid' => $_POST['activityid'],
    				'uid' => $userloginid,
    				'url' => $_POST['appendixurl'],
    				'time' => time(),
    			);
    			$isSaved = $ActivityAppendix->add($newActivityAppendixData);
    			if ($isSaved) {
    				$this->ajaxReturn(0,'保存成功','ok');
    			}
    		}

    		if (!empty($_FILES)) {
    			if ($_FILES["uploadappendix"]["error"] > 0) {
    				$this->ajaxReturn(0,'上传失败, info'.$_FILES["uploadappendix"]["error"],'error');
    			} else {
    				$imageOldName = $_FILES["uploadappendix"]["name"];
    				$imageType = $_FILES["uploadappendix"]["type"];
    				$imageType = trim($imageType);
    				$imageSize = $_FILES["uploadappendix"]["size"];
    				$imageTmpName = $_FILES["uploadappendix"]["tmp_name"];
    			}

    			if ($imageSize > 9970016) {
    				$this->ajaxReturn(0,'上传文件太大, 最大能上传单张 10MB','error');
    			}  else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {
    				import("@.ORG.UploadFile");
    				$config=array(
		                'allowExts'=>array('jpeg','jpg','gif','png','bmp'),
		                'savePath'=>'./Public/activity/'.$userloginid.'/',
		                'saveRule'=>$userloginid.time(),
    				);
    				$upload = new UploadFile($config);
    				$upload->imageClassPath="@.ORG.Image";
    				$upload->thumb=false;
        			$upload->thumbMaxHeight=150;
        			$upload->thumbMaxWidth=150;
    				if (!$upload->upload()) {
    					$uploadErrorInfo = $upload->getErrorMsg();
    					$this->ajaxReturn($uploadErrorInfo,'上传出错','error');
    				} else {
    					$info = $upload->getUploadFileInfo();
    					$storage = new SaeStorage();
    					$newfilepath = $storage->getUrl("public", "activity/".$userloginid."/".$info[0]['savename']);

    					/**
    					 * ajax return
    					 */
    					$this->ajaxReturn($newfilepath,'上传成功','uploaded');
    				}
    			} else {
    				$this->ajaxReturn('','上传图片格式错误, 目前仅支持.jpg .png .gif','error');
    			}
    		}
    	}
    }

}

?>