<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class LabAction extends Action {

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
    	$recordSchoolInfo = i_school_domain();
    	$title = "实验室 ".$recordSchoolInfo['school']." 帮助主题社交网站";
        $this->assign('title',$title);
        $this->display();
    }

    public function maponline()
    {
    	$recordSchoolInfo = i_school_domain();
    	$userloginid = session('userloginid');
    	$title = "在线区域位置4.1 ".$recordSchoolInfo['school']." 帮助主题社交网站";
        $this->assign('title',$title);
        $UserLogin = M("UserLogin");
        $userOnlineObject = $UserLogin->where("online != 0 AND school = $recordSchoolInfo[id]")->join('i_user_status ON i_user_status.uid = i_user_login.uid')->join('i_user_info ON i_user_info.uid = i_user_login.uid')->select();
        
        /**
         * show online user nums, refrash per 15 second
         */
        $WebStatus = M("WebStatus");
        $recordWebStatus = $WebStatus->find($recordSchoolInfo['id']);
        if (5 < (time() - $recordWebStatus['time'])) {
        	$userOnlineNums = $UserLogin->where("online != 0 AND school = $recordSchoolInfo[id]")->count();
        	$newWebStats = array(
        		'sid' => $recordSchoolInfo['id'],
        		'online_nums' => $userOnlineNums,
        		'time' => time(),
        	);
        	$WebStatus->save($newWebStats);
        	foreach ($userOnlineObject as $userOnlineOne) {
	        	if (900 < (time() - $userOnlineOne['last_active_ti'])) {
	        		$updateUserOnlineStatusData = array(
	                    'uid' => $userOnlineOne['uid'],
	            	    'online' => 0,
	        		);
	        		$UserLogin->save($updateUserOnlineStatusData);
	        	}
        	}
        } else {
        	$userOnlineNums = $recordWebStatus['online_nums'];
        }
        
        /**
         * hidden online user nums 
         */
        $hiddenUserNums = $UserLogin->where("online = 2 AND school = $recordSchoolInfo[id]")->count();
        $this->assign('hiddenUserNums',$hiddenUserNums);
        $this->assign('onlineUserNums',$userOnlineNums);
        $this->assign('userOnlineObject',$userOnlineObject);
        
        /**
         * dormitory
         */
        $OpDormitory = M("OpDormitory");
        $recordOpDormitory = $OpDormitory->where("school = $recordSchoolInfo[id]")->order("type ASC,id ASC")->select();
        $this->assign('recordOpDormitory',$recordOpDormitory);
        $this->display();
    }
}

?>