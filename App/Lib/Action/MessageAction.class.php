<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class MessageAction extends Action
{

    public function resetNoticeCount($redismq, $userloginid)
    {
        $redismq->hDel(C('R_NOTICE') . C('R_SYSTEM') . substr($userloginid, 0, strlen($userloginid) - 3), substr($userloginid, -3));
    }

    protected function _initialize()
    {
        $userloginid = session('userloginid');
        if (!empty($userloginid)) {
            i_db_update_activetime($userloginid);
            $IUserLogin = D("IUserLogin");
            $userloginedrecord = $IUserLogin->userExists($userloginid);
            $this->assign('userloginedrecord', $userloginedrecord);
        } else {
            redirect('/user/notlogin', 0, '你还没有登录呢...');
        }
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '消息');
        redirect('/message/system', 0, '页面跳转...');
    }

    public function system()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '帮助和其他系统消息');

        /**
         * show $count records every page
         * $count int
         * $offect int Equal current page * count
         */
        $page = i_page_get_num();
        $count = 15;
        $offset = $page * $count;

        /**
         * select
         */
        $MsgSystem = M("MsgSystem");
        $msgSystem = $MsgSystem->where("uid = $userloginid")->limit($offset, $count)->order('id DESC')->select();

        /**
         * page link
         */
        $totalMsgSystemNums = $MsgSystem->where("uid = $userloginid")->count();
        $totalPages = ceil($totalMsgSystemNums / $count);
        $this->assign('totalrecordnums', $totalMsgSystemNums);
        $this->assign('totalpages', $totalPages);


        $redis = new Redis();
        $redis->connect(C('REDIS_HOST'), C('REDIS_PORT'));

        $msgIds = $redis->hKeys(C('R_ACCOUNT') . C('R_MESSAGE') . $userloginid);
        $msgIdsStr = '';
        foreach ($msgIds as $msg) {
            $msgIdsStr .= $msg . ",";
            $redis->hSet(C('R_ACCOUNT'). C('R_MESSAGE') . $userloginid , $msg, 1);
        }
        $msgIdsStr = rtrim($msgIdsStr, ",");


        $MsgNotice = M("MsgNotice");
        if(!empty($msgIdsStr)){
            $msgNotice = $MsgNotice->where("id in ($msgIdsStr)")->limit($offset, $count)->order('create_time DESC')->select();
        }

        $IUserLogin = D("IUserLogin");
        $this->resetNoticeCount($redis, $userloginid);
        foreach($msgNotice as $notice){
            $fromUser = $IUserLogin->userExists($notice['source_id']);
            $from_user = "<a href='" . __ROOT__ . "/wo/" . $notice['source_id'] . "' target='_blank' class='getuserinfo' userid='" . $notice['source_id'] . "'>" . $fromUser['nickname'] . "</a>";

            $tpl =   $redis->hGet(C('R_Notice_Message_Template'), $notice['format_id']);
            printf($tpl, $from_user, __ROOT__, hGet(C('R_Notice_Message_Link'), $notice['notice_type']), $notice['detail_id']);

            $msgSysArray[] = array(
                'deliver' => $redis->hGet(C('R_ACCOUNT'). C('R_MESSAGE') . $userloginid , $notice['notice_id']),
                'content' => $tpl,
                'time' => i_time($notice['time']),
            );
        }

        $RecordDiffusion = M("RecordDiffusion");
        if (!empty($msgIdsStr)) {
            $recordDiffusion = $RecordDiffusion->where("id in ($msgIdsStr)")->limit($offset, $count)->order('id DESC')->select();
        }

        /**
         * msg form system
         */
        $IUserLogin = D("IUserLogin");

        $this->resetNoticeCount($redis, $userloginid);
        foreach ($recordDiffusion as $rd) {
            $fromUser = $IUserLogin->userExists($rd['uid']);
            $from_user = "<a href='" . __ROOT__ . "/wo/" . $rd['uid'] . "' target='_blank' class='getuserinfo' userid='" . $rd['uid'] . "'>" . $fromUser['nickname'] . "</a>";

            $msgSysArray[] = array(
                'deliver' => $redis->hGet(C('R_ACCOUNT') . $userloginid . C('R_MESSAGE'), $rd['id']),
                'from_user' => $from_user,
                'content' => $rd['assess_id'],
                'url' => $rd['sid'],
                'time' => i_time($rd['time']),
            );

        }

//    	foreach ($msgSystem as $msg) {
//    		if (!empty($msg['from_uid'])) {
//    			$fromUser = $IUserLogin->userExists($msg['from_uid']);
//    			$from_user = "<a href='".__ROOT__."/wo/".$msg['from_uid']."' target='_blank' class='getuserinfo' userid='".$msg['from_uid']."'>".$fromUser['nickname']."</a>";
//    		} else {
//    			$from_user = '';
//    		}
//    		if (!empty($msg['type'])) {
//    			if ($msg['type'] == 'setting/realfirst') {
//    				$msg_system_url = "<a href='".__ROOT__."/setting/realfirst?step=1' target='_blank'>详情</a>";
//    			} else if ($msg['type'] == 'mutual/rc-para:?please') {
//    				$msg_system_url = "<a href='".__ROOT__."/mutual/rc/".$msg['url_id']."?please' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'mutual/rc') {
//    				$msg_system_url = "<a href='".__ROOT__."/mutual/rc/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'stream/i') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/say/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'stream/ih') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'stream/ih-para:timeLimit') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'stream/ih-para:timeEnd') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'stream/ih-para:success') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'stream/ih-para:newHelp') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'stream/ih-para:reply') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'stream/i-para:diffusion') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/say/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>去看看</a>";
//    			} else if ($msg['type'] == 'stream/ih-para:diffusion') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>去看看</a>";
//    			} else if ($msg['type'] == 'stream/ih-para:needhelp') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>帮助去</a>";
//    			} else if ($msg['type'] == 'stream/i-para:diffusiontoowner') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/say/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详细</a>";
//    			} else if ($msg['type'] == 'stream/ih-para:diffusiontoowner') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/help/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详细</a>";
//    			} else if ($msg['type'] == 'helprooter/userhonor') {
//    				$msg_system_url = "<a href='".__ROOT__."/wo/honor/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详细</a>";
//    			} else if ($msg['type'] == 'lab/pushaudit-para:yes') {
//    				$msg_system_url = "<a href='".__ROOT__."/lab/push.content/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详细</a>";
//    			} else if ($msg['type'] == 'lab/pushaudit-para:no') {
//    				$msg_system_url = "<a href='".__ROOT__."/lab/push?audit=fail' target='_blank' class='a_view_info_sys'>详细</a>";
//    			} else if ($msg['type'] == 'rooter/userinvite') {
//    				$msg_system_url = "<a href='".__ROOT__."/mutual/invite/' target='_blank' class='a_view_info_sys'>详细</a>";
//    			} else if ($msg['type'] == 'stream/i-para:groupmsgpush') {
//    				$msg_system_url = "<a href='".__ROOT__."/item/say/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详细</a>";
//    			} else if ($msg['type'] == 'activity/item-para:invite') {
//    				$msg_system_url = "<a href='".__ROOT__."/activity/item/".$msg['url_id']."' target='_blank' class='a_view_info_sys'>详细</a>";
//    			} else if ($msg['type'] == 'mallset/buypay') {
//    				$msg_system_url = "<a href='".__ROOT__."/mallset/seller?step=needsure' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'mallset/seller-refuse') {
//    				$msg_system_url = "<a href='".__ROOT__."/mallset/buyer?step=finish' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'mallset/seller-sure') {
//    				$msg_system_url = "<a href='".__ROOT__."/mallset/buyer?step=needsure' target='_blank' class='a_view_info_sys'>详情</a>";
//    			} else if ($msg['type'] == 'system') {
//    				$msg_system_url = '';
//    			} else {
//    				$msg_system_url = '';
//    			}
//    		}
//    		$msgSysArray[] = array(
//        	    'deliver' => $msg['deliver'],
//                'from_user' => $from_user,
//                'content' => $msg['content'],
//                'url' => $msg_system_url,
//                'time' => i_time($msg['time']),
//    		);
//    		if ($msg['deliver'] == 0) {
//    			$deliverMsgUpdate = array(
//            		'id' => $msg['id'],
//            		'deliver' => 1
//            	);
//
//            	$MsgSystem->save($deliverMsgUpdate);
//    		}
//
//    	}
        $this->assign('msgsysarray', $msgSysArray);

        if (isset($_GET['suredelsys'])) {
            $MsgSystem->where("uid = $userloginid")->delete();
            redirect('/message/system', 1, '删除成功...');
        }
        $this->display();
    }

    public function comment()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '评论和回复消息');

        /**
         * show $count records every page
         * $count int
         * $offect int Equal current page * count
         */
        $page = i_page_get_num();
        $count = 15;
        $offset = $page * $count;

        /**
         * select
         */
        $MsgComment = M("MsgComment");
        $totalMsgCommentNums = $MsgComment->where("uid = $userloginid")->count();
        if ($totalMsgCommentNums > $count) {
            //$msgComment = $MsgComment->where("uid = $userloginid")->limit(($totalMsgCommentNums - ($page + 1) * $count),$count)->select();
        } else {
            //$msgComment = $MsgComment->where("uid = $userloginid")->select();
        }
        $msgComment = $MsgComment->where("uid = $userloginid")->order('id DESC')->limit($offset, $count)->select();

        /**
         * page link
         */
        $totalPages = ceil($totalMsgCommentNums / $count);
        $this->assign('totalrecordnums', $totalMsgCommentNums);
        $this->assign('totalpages', $totalPages);

        $IUserLogin = D("IUserLogin");
        $RecordComment = M("RecordComment");
        $RecordSay = M("RecordSay");

        Vendor('Ihelpoo.Ofunction');
        $ofunction = new Ofunction();

        foreach ($msgComment as $msg) {
            $replyUser = $IUserLogin->userExists($msg['rid']);
            $nickname = $replyUser['nickname'] != NULL ? $replyUser['nickname'] : "匿名用户";
            if (!empty($msg['cid'])) {
                $recordCommentData = $RecordComment->where("cid = $msg[cid]")->find();
                $content = $ofunction->cut_str($recordCommentData['content'], '15');
                $content = $content == NULL ? "<span class='gray'>这条评论被你删除了的</span>" : $content;
                $info = "回复了你的评论: " . $content;
                if (!empty($msg['ncid'])) {
                    $recordCommentDetailData = $RecordComment->where("cid = $msg[ncid]")->find();
                    $contentdetail = $recordCommentDetailData['content'];
                }
                $contentdetail = $contentdetail == NULL ? "<span class='gray'>回复又被" . $nickname . "删除了</span>" : $contentdetail;
            } else {
                $recordSayData = $RecordSay->where("sid = $msg[sid]")->find();
                $content = $ofunction->cut_str($recordSayData['content'], '15');
                $content = $content == NULL ? "<span class='gray'>内容被你删除了的</span>" : $content;
                $info = "评论了你: " . $content;
                $recordCommentDetailData = $RecordComment->where("cid = $msg[ncid]")->find();
                $contentdetail = $recordCommentDetailData['content'];
                $contentdetail = $contentdetail == NULL ? "<span class='gray'>评论又被" . $nickname . "删除了</span>" : $contentdetail;
            }
            $msgArray[] = array(
                'deliver' => $msg['deliver'],
                'uid' => $replyUser['uid'],
                'icon_url' => $replyUser['icon_url'],
                'nickname' => $nickname,
                'cid' => $msg['cid'],
                'ncid' => $msg['ncid'],
                'sid' => $msg['sid'],
                'info' => $info,
                'contentdetail' => stripslashes($contentdetail),
                'time' => i_time($msg['time']),
            );
            if ($msg['deliver'] == 0) {
                $deliverMsgUpdate = array(
                    'id' => $msg['id'],
                    'deliver' => 1
                );
                $MsgComment->save($deliverMsgUpdate);
            }
        }
        //$msgArray = array_reverse($msgArray, true);
        $this->assign('msgarray', $msgArray);

        if (isset($_GET['suredel'])) {
            $MsgComment->where("uid = $userloginid")->delete();
            redirect('/message/comment', 1, '删除成功...');
        }
        $this->display();
    }

    public function at()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '@我的消息');

        /**
         * show $count records every page
         * $count int
         * $offect int Equal current page * count
         */
        $page = i_page_get_num();
        $count = 15;
        $offset = $page * $count;

        /**
         * select
         */
        $MsgAt = M("MsgAt");
        $recordsMsgAt = $MsgAt->where("touid = $userloginid")
            ->join('i_user_login ON i_msg_at.fromuid = i_user_login.uid')
            ->field('id,touid,fromuid,sid,cid,hid,aid,time,deliver,uid,nickname,icon_url')
            ->limit($offset, $count)->order('time DESC')->select();

        $RecordSay = M("RecordSay");
        $RecordComment = M("RecordComment");
        $RecordHelpreply = M("RecordHelpreply");
        foreach ($recordsMsgAt as $msg) {
            if (!empty($msg['cid'])) {
                $info = "这条评论@了你";
                $recordCommentData = $RecordComment->where("cid = $msg[cid]")->field('cid,sid,content')->find();
                $contentdetail = $recordCommentData['content'] == NULL ? "<span class='gray'>评论被" . $msg['nickname'] . "删除了</span>" : $recordCommentData['content'];
            } else if (!empty($msg['hid'])) {
                $info = "这条帮助回复@了你";
                $recordHelpreplyData = $RecordHelpreply->where("id = $msg[hid]")->field('id,sid,content')->find();
                $contentdetail = $recordHelpreplyData['content'] == NULL ? "<span class='gray'>帮助内容被" . $msg['nickname'] . "删除了</span>" : $recordHelpreplyData['content'];
            } else if (!empty($msg['sid'])) {
                $recordSayData = $RecordSay->where("sid = $msg[sid]")->field('sid,say_type,content')->find();
                if ($recordSayData['say_type'] == 0) {
                    $info = "这条记录@了你";
                } else if ($recordSayData['say_type'] == 1) {
                    $info = "这条帮助@了你";
                }
                $contentdetail = $recordSayData['content'] == NULL ? "<span class='gray'>信息被" . $msg['nickname'] . "删除了</span>" : $recordSayData['content'];
            }
            $msgArray[] = array(
                'deliver' => $msg['deliver'],
                'uid' => $msg['fromuid'],
                'icon_url' => $msg['icon_url'],
                'nickname' => $msg['nickname'],
                'cid' => $msg['cid'],
                'hid' => $msg['hid'],
                'sid' => $msg['sid'],
                'info' => $info,
                'contentdetail' => stripslashes($contentdetail),
                'time' => i_time($msg['time']),
            );
            if ($msg['deliver'] == 0) {
                $readFlag = array(
                    'id' => $msg['id'],
                    'deliver' => 1,
                );
                $MsgAt->save($readFlag);
            }
        }

        $this->assign('msgarray', $msgArray);

        /**
         * page link
         */
        $totalMsgAtNums = $MsgAt->where("touid = $userloginid")->count();
        $totalPages = ceil($totalMsgAtNums / $count);
        $this->assign('totalrecordnums', $totalMsgAtNums);
        $this->assign('totalpages', $totalPages);

        if (isset($_GET['suredel'])) {
            $MsgAt->where("touid = $userloginid")->order('time ASC')->limit($count)->delete();
            redirect('/message/at', 1, '删除成功...');
        }
        $this->display();
    }

    public function coin()
    {
        $userloginid = session('userloginid');
        $this->assign('title', 'RMB记录');

        $page = i_page_get_num();
        $count = 15;
        $offset = $page * $count;
        $UserCoins = M("UserCoins");
        $recordsUserCoins = $UserCoins->where("uid = $userloginid")->limit($offset, $count)->order('time DESC')->select();
        $this->assign('recordsUserCoins', $recordsUserCoins);

        /**
         * update deliver status
         */
        foreach ($recordsUserCoins as $recordUserCoins) {
            if ($recordUserCoins['deliver'] == 0) {
                $readFlag = array(
                    'id' => $recordUserCoins['id'],
                    'deliver' => 1,
                );
                $UserCoins->save($readFlag);
            }
        }

        /**
         * page link
         */
        $totalUserCoinsNums = $UserCoins->where("uid = $userloginid")->count();
        $totalPages = ceil($totalUserCoinsNums / $count);
        $this->assign('totalrecordnums', $totalUserCoinsNums);
        $this->assign('totalpages', $totalPages);
        $this->display();
    }

    public function active()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '活跃详细记录');

        $page = i_page_get_num();
        $count = 15;
        $offset = $page * $count;
        $MsgActive = M("MsgActive");
        $recordsMsgActive = $MsgActive->where("uid = $userloginid")->limit($offset, $count)->order('id DESC')->select();
        $this->assign('recordsMsgActive', $recordsMsgActive);

        /**
         * update deliver status
         */
        $updateRecordsMsgActive = $MsgActive->where("uid = $userloginid")->select();
        foreach ($updateRecordsMsgActive as $recordMsgActive) {
            if ($recordMsgActive['deliver'] == 0) {
                $readFlag = array(
                    'id' => $recordMsgActive['id'],
                    'deliver' => 1,
                );
                $MsgActive->save($readFlag);
            }
        }

        /**
         * page link
         */
        $totalMsgActiveNums = $MsgActive->where("uid = $userloginid")->count();
        $totalPages = ceil($totalMsgActiveNums / $count);
        $this->assign('totalrecordnums', $totalMsgActiveNums);
        $this->assign('totalpages', $totalPages);
        $this->display();
    }

}

?>