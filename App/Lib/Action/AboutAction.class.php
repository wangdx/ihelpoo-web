<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class AboutAction extends Action {

    protected function _initialize()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname', $recordSchoolInfo['school']);
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index()
    {
    	$recordSchoolInfo = i_school_domain();
    	$title = "关于我们 ".$recordSchoolInfo['school'];
    	$this->assign('title', $title);
    	
    	$SchoolSystem = M("SchoolSystem");
    	$recordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->find();
    	$this->assign('schoolabout',$recordSchoolSystem['about']);
    	
    	$SchoolWebmaster = M("SchoolWebmaster");
    	$recordSchoolWebmaster = $SchoolWebmaster->where("sid = $recordSchoolInfo[id]")->join("i_user_login ON i_school_webmaster.uid = i_user_login.uid")->order("position DESC")->select();
    	$this->assign('schoolwebmaster',$recordSchoolWebmaster);
    	$this->display();
    }
    
    /**
     * artical
     */
    public function artical()
    {
    	$articalid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$CmsArtical = M("CmsArtical");
    	if (!empty($articalid)) {
    		$cmsArticalRecord = $CmsArtical->where("id = $articalid")->find();
    		$this->assign('cmsArticalRecord',$cmsArticalRecord);
    		$this->assign('title',$cmsArticalRecord['title'].' 关于我们');
    		
    		/**
    		 * update hit nums
    		 */
    		$updateArticalHit = array(
	        	'id' => $articalid,
	        	'hit' => $cmsArticalRecord['hit'] + 1,
    		);
    		$CmsArtical->save($updateArticalHit);
    	} else {
    		$cmsArticalRecords = $CmsArtical->order("time DESC")->select();
    		$this->assign('cmsArticalRecords',$cmsArticalRecords);
    		$this->assign('title','文章列表 关于我们');
    	}
        $this->display();
    }
    
    public function company()
    {
    	$title = "武汉我帮网络科技有限公司";
    	$this->assign('title', $title);
    	$this->display();
    }
    
    public function sns()
    {
    	$title = "作共建地方化校园SNS";
    	$this->assign('title', $title);
    	$this->display();
    }
    
    public function snsapply()
    {
    	$title = "合作申请，作共建地方化校园SNS";
    	$this->assign('title', $title);
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    		$SchoolApplyverify = M("SchoolApplyverify");
	    	$recordSchoolApplyverify = $SchoolApplyverify->where("uid = '$userloginid'")->find();
	    	$this->assign('recordSchoolApplyverify',$recordSchoolApplyverify);
    	}
    	
    	if ($this->isPost()) {
	    	$question1 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question1"]))));
	    	$question2 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question2"]))));
	    	$question3 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question3"]))));
	    	$question4 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question4"]))));
	    	$question5 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question5"]))));
	    	$question6 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question6"]))));
	    	$question7 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question7"]))));
	    	$question8 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question8"]))));
	    	$question9 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question9"]))));
	    	$question10 = trim(addslashes(htmlspecialchars(strip_tags($_POST["question10"]))));
	    	
	    	if (!empty($recordSchoolApplyverify['id'])) {
	    		$updateApplyverifyData = array(
	    			'id' => $recordSchoolApplyverify['id'],
	    			'verify_status' => 1,
	    			'question1' => $question1,
	    			'question2' => $question2,
	    			'question3' => $question3,
	    			'question4' => $question4,
	    			'question5' => $question5,
	    			'question6' => $question6,
	    			'question7' => $question7,
	    			'question8' => $question8,
	    			'question9' => $question9,
	    			'question10' => $question10,
	    			'time' => time(),
	    		);
	    		$SchoolApplyverify->save($updateApplyverifyData);
	    		$this->ajaxReturn(0, "更新成功", "yes");
	    	} else {
		    	$newApplyverifyData = array(
			    	'id' => '',
			    	'uid' => $userloginid,
			    	'verify_status' => 0,
			    	'question1' => $question1,
	    			'question2' => $question2,
	    			'question3' => $question3,
	    			'question4' => $question4,
	    			'question5' => $question5,
	    			'question6' => $question6,
	    			'question7' => $question7,
	    			'question8' => $question8,
	    			'question9' => $question9,
	    			'question10' => $question10,
			    	'time' => time()
		    	);
		    	$SchoolApplyverify->add($newApplyverifyData);
		    	
		    	/**
		    	 * send email
		    	 */
                i_send('admin@tvery.com','system to cho','有新学校申请开通我帮圈圈:)');
		    	$this->ajaxReturn(0, "提交成功", "yes");
	    	} 
	    	$this->ajaxReturn(0, "出错了", "wrong");
    	}
    	$this->display();
    }
    
    public function suggestion()
    {
    	$title = "意见建议";
    	$this->assign('title', $title);
    	if ($this->isPost()) {
	    	$connection = trim(strip_tags($_POST["connection"]));
	    	$content = trim(strip_tags($_POST["content"]));
	    	$emailcontent = "联系方式:<br />".$connection."<hr />内容:<br />".$content;
	    	i_send('admin@tvery.com','我帮圈圈 意见建议', $emailcontent);
	    	//i_send('admin@tvery.com','我帮圈圈 意见建议',$emailcontent);
	    	//i_send('admin@tvery.com','我帮圈圈 意见建议',$emailcontent);
	    	$this->ajaxReturn(0, "提交成功", "yes");
    	}
    	$this->display();
    }

}

?>