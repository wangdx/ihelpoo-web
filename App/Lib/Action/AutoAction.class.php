<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class AutoAction extends Action {

    protected function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
    }

    /**
     * invite long time not login user If they had new msg
     */
    public function reinviteltnl()
    {
    	$UserLogin = M("UserLogin");
    	$MsgComment = M("MsgComment");
    	//MsgSystem
    	$MsgAt = M("MsgAt");
    	$TalkContent = M("TalkContent");
    	$timewidth = time() - 604800;
    	$userLongNotLogin = $UserLogin->where("logintime < $timewidth")->select();
    	//$userLongNotLogin = $UserLogin->where("uid = 10001")->select();
    	if (!empty($userLongNotLogin)) {

    		/**
    		 * send email
    		 */
    		Vendor('Ihelpoo.Email');
    		$emailObj = new Email();
    		$i = 0;
    		foreach ($userLongNotLogin as $userNotLogin) {
    			$userNotLoginUid = $userNotLogin['uid'];
    			$msgCommentNums = $MsgComment->where("uid = $userNotLoginUid AND deliver = '0'")->count();
    			$msgSystemNums = 0;
    			$msgAtNums = $MsgAt->where("touid = $userNotLoginUid AND deliver = '0'")->count();
    			$newTalkNums = $TalkContent->where("touid = $userNotLoginUid AND deliver = '0'")->count();
    			if ($msgCommentNums != 0 || $msgSystemNums != 0 || $msgAtNums != 0 || $newTalkNums != 0) {
    				$messageNums = $msgCommentNums + $msgSystemNums + $msgAtNums + $newTalkNums;
    				if ($i < 30) {
    					$isSend = $emailObj->longtimeNotlogin($userNotLogin['email'], $userNotLogin['nickname'], $messageNums);
	    				if ($isSend) {
	    					$sendReinviteArray[] = array(
	                            'nickname' => $userNotLogin['nickname'],
	                            'email' => $userNotLogin['email'],
	    					);
	    					$i++;
	    				}
    				}
    			}
    		}


    		/**
    		 * admin user operating record
    		 */
    		if (!empty($i)) {
    			$AdminUserrecord = M("AdminUserrecord");
    			$newAdminUserrecordData = array(
						'id' => '',
						'uid' => 1,
						'record' => 'Auto 邮件提醒消息 reinviteltnl, ok:'.$i,
						'time' => time(),
    			);
    			$AdminUserrecord->add($newAdminUserrecordData);
    		}
    	}
    }

}

?>