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
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname', $recordSchoolInfo['school']);
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
    
    public function ihelpoo()
    {
    	$title = "关于我们 ";
    	$this->assign('title', $title);
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
    
    public function jobs()
    {
    	$title = "招聘 武汉我帮网络科技有限公司";
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
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	}
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname', $recordSchoolInfo['school']);
    	$title = "意见建议 ".$recordSchoolInfo['school'];
    	$this->assign('title', $title);
    	$DataSuggestion = M("DataSuggestion");
    	if ($this->isPost()) {
	    	$connection = trim(addslashes(strip_tags($_POST["connection"])));
	    	$content = trim(addslashes(strip_tags($_POST["content"])));
	    	$verificationcode = (int)strtolower(trim($_POST["verificationcode"]));
	    	if (empty($verificationcode)) {
	    		
	    		/**
	    		 * create virification code
	    		 */
	    		Vendor('Ihelpoo.Verifi');
	    		$verifi = new Verifi();
	    		$verifiString = $verifi->value_rand();
	    		$_SESSION['verificationcode'] = $verifiString['formula'];
	    		$_SESSION['verificationresult'] = $verifiString['result'];
	    		$this->ajaxReturn(0, "请输入验证码", 'verifi');
	    	}
	    	
	    	if ($verificationcode != $_SESSION['verificationresult']) {
	    		$this->ajaxReturn(0, "验证码错误", 'error');
	    	}
	    	
	    	if (!empty($content) && !empty($connection)) {
		    	$newDataSuggestion = array(
		    		'id' => '',
		    		'uid' => $userloginid,
		    		'contact' => $connection,
		    		'suggestion' => $content,
		    		'time' => time(),
		    		'school_id' => $recordSchoolInfo['id']
		    	);
		    	$DataSuggestion->add($newDataSuggestion);
		    	
		    	/**
		    	 * send email to ihelpoo group && school group
		    	 */
		    	$emailcontent = "联系方式:<br />".$userloginedrecord['nickname']." ".$connection."<hr />内容:<br />".$content." <br/><br/><span style='color:gray;font-size:12px'>请登录后台及时处理回复，并做好记录</span>";
		    	$emailtitle = "我帮圈圈 意见建议 ".$recordSchoolInfo['school'];
		    	i_send('admin@tvery.com', $emailtitle, $emailcontent);
		    	/**
		    	$AdminUser = M("AdminUser");
		    	$recordsAdminUser = $AdminUser->select();
		    	foreach ($recordsAdminUser as $adminUser) {
		    		if (!empty($adminUser['email'])) {
		    			i_send($adminUser['email'], $emailtitle, $emailcontent);
		    		}
		    	}
		    	
		    	$SchoolWebmaster = M("SchoolWebmaster");
		    	$recordSchoolWebmaster = $SchoolWebmaster->where("sid = $recordSchoolInfo[id]")->join('i_user_login ON i_school_webmaster.uid = i_user_login.uid')
		    	->field("i_user_login.uid,i_user_login.email,i_user_login.nickname")
		    	->select();
		    	foreach ($recordSchoolWebmaster as $schoolWebmaster) {
		    		i_send($schoolWebmaster['email'], $emailtitle, $emailcontent);
		    	}*/
		    	$this->ajaxReturn(0, "提交成功", "yes");
	    	} else {
	    		$this->ajaxReturn(0, "提交失败了", "error");
	    	}
    	}
    	
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
        
        if (!empty($_GET['school'])) {
        	$recordDataSuggestion = $DataSuggestion->where("school_id")
        	->join('i_user_login ON i_data_suggestion.uid = i_user_login.uid')
        	->join('i_school_info ON i_data_suggestion.school_id = i_school_info.id')
        	->field('i_data_suggestion.id,i_user_login.uid,i_data_suggestion.suggestion,i_data_suggestion.time,i_data_suggestion.ihelpoo_reply,i_data_suggestion.ihelpoo_reply_uid,i_data_suggestion.ihelpoo_reply_time,i_data_suggestion.school_reply,i_data_suggestion.school_reply_uid,i_data_suggestion.school_reply_time,i_data_suggestion.school_id,nickname,sex,birthday,enteryear,type,online,active,icon_url,i_school_info.school,i_school_info.domain,i_school_info.domain_main')
        	->limit($offset, $count)->order("i_data_suggestion.time DESC")->select();
        	$totalRecordNums = $DataSuggestion->where("school_id")->count();
        	$this->assign('liststyle', 'schoolall');
        } else {
        	$recordDataSuggestion = $DataSuggestion->where("school_id = $recordSchoolInfo[id]")
        	->join('i_user_login ON i_data_suggestion.uid = i_user_login.uid')
        	->join('i_school_info ON i_data_suggestion.school_id = i_school_info.id')
        	->field('i_data_suggestion.id,i_user_login.uid,i_data_suggestion.suggestion,i_data_suggestion.time,i_data_suggestion.ihelpoo_reply,i_data_suggestion.ihelpoo_reply_uid,i_data_suggestion.ihelpoo_reply_time,i_data_suggestion.school_reply,i_data_suggestion.school_reply_uid,i_data_suggestion.school_reply_time,i_data_suggestion.school_id,nickname,sex,birthday,enteryear,type,online,active,icon_url,i_school_info.school,i_school_info.domain,i_school_info.domain_main')
        	->limit($offset, $count)->order("i_data_suggestion.time DESC")->select();
        	$totalRecordNums = $DataSuggestion->where("school_id = $recordSchoolInfo[id]")->count();
        }
        $this->assign('recordDataSuggestion', $recordDataSuggestion);
        $this->assign('totalRecordNums', $totalRecordNums);
        $totalPages = ceil($totalRecordNums / $count);
        $this->assign('totalPages', $totalPages);
    	$this->display();
    }

}

?>