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
    	$title = "在线区域位置3.2 ".$recordSchoolInfo['school']." 帮助主题社交网站";
        $this->assign('title',$title);
    	
    	/**
         * calculate online user numbers. calculate per 30 second
         */
        $UserLogin = M("UserLogin");
        $userOnlineObject = $UserLogin->where("online != 0")->join('i_user_status ON i_user_status.uid = i_user_login.uid')->join('i_user_info ON i_user_info.uid = i_user_login.uid')->select();
        
        foreach ($userOnlineObject as $userOnlineOne) {
        	if (900 < (time() - $userOnlineOne['last_active_ti'])) {
        		$updateUserOnlineStatusData = array(
                    'uid' => $userOnlineOne['uid'],
            	    'online' => 0,
        		);
        		$UserLogin->save($updateUserOnlineStatusData);
        	}
        }
        $userOnlineNums = $UserLogin->where("online != 0 AND school = $recordSchoolInfo[id]")->count();
        
        /**
         * hidden online user nums 
         */
        $hiddenUserNums = $UserLogin->where("online = 2")->count();
        $this->assign('hiddenUserNums',$hiddenUserNums);
        $this->assign('onlineUserNums',$userOnlineNums);
        $this->assign('userOnlineObject',$userOnlineObject);
        
        /**
         * 
         */
        $userloginid = session('userloginid');
        if ($userloginid) {
            $userVisitStatus = $UserLogin->where("uid = $userloginid")->find();
            $this->assign('userVisitStatus',$userVisitStatus);
        }
        $this->display();
    }
}

?>