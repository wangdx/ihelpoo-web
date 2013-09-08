<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class IndexAction extends Action {

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
    	$userloginid = session('userloginid');
    	$recordSchoolInfo = i_school_domain();
    	$title = "App ".$recordSchoolInfo['school']." 帮助主题社交网站";
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$this->assign('title',$title);
        $this->display();
    }

}

?>