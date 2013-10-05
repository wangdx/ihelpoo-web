<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class AppAction extends Action {

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
    	$title = "App下载 校园帮助主题社交网站";
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$this->assign('title',$title);
    	if(i_is_mobile()) {
        	$this->display();
    	} else {
    		$this->display('/mobile/app_index');
    	}
    }

}

?>