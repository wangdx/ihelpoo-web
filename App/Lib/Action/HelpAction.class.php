<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class HelpAction extends Action {

    protected function _initialize()
    {
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$IUserLogin = D("IUserLogin");
    		$userloginedrecord = $IUserLogin->userExists($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	}
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index()
    {
    	$recordSchoolInfo = i_school_domain();
    	$title = "帮助广场 - ".$recordSchoolInfo['school'];
        $this->assign('title',$title);

    	/**
    	 * 
    	 * show help record not finish
    	 */
   	    $RecordSay = M("RecordSay");
   	    $recordHelpGoonList = $RecordSay->where("i_record_say.say_type = 1 AND i_record_help.status < 3 AND i_record_say.school_id = $recordSchoolInfo[id]")
   	    ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
   	    ->join('i_record_help ON i_record_say.sid = i_record_help.sid')
   	    ->order('i_record_say.time DESC')
   	    ->limit(15)
   	    ->select();
   	    $this->assign('recordHelpGoonList',$recordHelpGoonList);
   	    
   	    /**
   	     * 
   	     * show help record finished
   	     */
   	    $recordHelpFinishList = $RecordSay->where("i_record_say.say_type = 1 AND i_record_help.status = 3 AND i_record_say.school_id = $recordSchoolInfo[id]")
   	    ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
   	    ->join('i_record_help ON i_record_say.sid = i_record_help.sid')
   	    ->order('i_record_say.time DESC')
   	    ->limit(15)
   	    ->select();
   	    $this->assign('recordHelpFinishList',$recordHelpFinishList);
   	    
   	    /**
   	     * 
   	     * show user order by active
   	     */
   	    $UserLogin = M("UserLogin");
   	    $recordUserActiveList = $UserLogin->where("school = $recordSchoolInfo[id]")->order('i_user_login.active DESC')
   	    ->limit(10)
   	    ->select();
   	    $this->assign('recordUserActiveList',$recordUserActiveList);
   	    $this->display();
    }
    
    public function lists()
    {
    	$recordSchoolInfo = i_school_domain();
    	$title = "帮助列表 - ".$recordSchoolInfo['school'];
    	$this->assign('title',$title);
    	
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        $RecordSay = M("RecordSay");

        /**
         * show help record not finish, below is the same
         */
        $recordHelpList = $RecordSay->where("i_record_say.say_type = 1 AND i_record_say.school_id = $recordSchoolInfo[id]")
        ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->join('i_record_help ON i_record_say.sid = i_record_help.sid')
        ->order('i_record_say.time DESC')
   	    ->limit($offset,$count)
   	    ->select();
   	    $this->assign('recordHelpList',$recordHelpList);
   	    
   	    /**
   	     * paging
   	     */
   	    $totalHelpNums = $RecordSay->where("say_type = 1 AND school_id = $recordSchoolInfo[id]")->count();
        $totalPages = ceil($totalHelpNums / $count);
        $this->assign('totalHelpNums',$totalHelpNums);
        $this->assign('totalPages',$totalPages);
        
        $this->display();
    }
    
    public function well()
    {   
    	$recordSchoolInfo = i_school_domain();
    	$title = "最佳帮助 - ".$recordSchoolInfo['school'];
    	$this->assign('title',$title);
    	
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        
    	/**
         * show help record finish well done
    	 */
        $RecordSay = M("RecordSay");
        $recordHelpWellList = $RecordSay->where("i_record_say.say_type = 1 AND i_record_help.win_uid != '' AND i_record_say.school_id = $recordSchoolInfo[id]")
        ->join('i_record_help ON i_record_say.sid = i_record_help.sid')
        ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->order('i_record_say.time DESC')
        ->limit($offset,$count)
        ->select();
        $this->assign('recordHelpWellList',$recordHelpWellList);
        
   	    /**
   	     * paging
   	     */
        $RecordHelp = M("RecordHelp");
   	    $totalHelpNums = $RecordHelp->where("i_record_help.win_uid != '' AND i_record_say.school_id = $recordSchoolInfo[id]")->join('i_record_help ON i_record_say.sid = i_record_help.sid')->count();
        $totalPages = ceil($totalHelpNums / $count);
        $this->assign('totalHelpNums',$totalHelpNums);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }
    
    public function need()
    {
    	$title = "需要帮助 - 我帮圈圈";
    	$this->assign('title',$title);
    	
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        
        /**
         * show help record finish well done
    	 */
        $RecordSay = M("RecordSay");
        $recordHelpNeedList = $RecordSay->where("i_record_say.say_type = 1 AND i_record_help.status < 3")
        ->join('i_record_help ON i_record_say.sid = i_record_help.sid')
        ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->order('i_record_say.time DESC')
        ->limit($offset,$count)
        ->select();
        $this->assign('recordHelpNeedList',$recordHelpNeedList);
        
   	    /**
   	     * paging
   	     */
        $RecordHelp = M("RecordHelp");
   	    $totalHelpNums = $RecordHelp->where("status < 3")->count();
        $totalPages = ceil($totalHelpNums / $count);
        $this->assign('totalHelpNums',$totalHelpNums);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }

}

?>