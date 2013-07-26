<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class SchooladminAction extends Action {

    protected function _initialize() {
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	}
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('recordSchoolInfo',$recordSchoolInfo);
    	
    	$webmasterloginid = session('webmasterloginid');
    	if (!empty($webmasterloginid)) {
    		$webmasterloginname = session('webmasterloginname');
    		$this->assign('webmasterloginid',$webmasterloginid);
    		$this->assign('webmasterloginname',$webmasterloginname);
    	}
    	
    	function logincheck()
    	{
    		$webmasterloginid = session('webmasterloginid');
    		if (empty($webmasterloginid)) {
    			redirect('/schooladmin', 3, '还没有登录呢...');
    		} else {
    			$webmasterloginid = session('webmasterloginid');
    			$webmasterloginname = session('webmasterloginname');
    			return array(
    				'webmasteruid' => $webmasterloginid,
    				'webmastername' => $webmasterloginname,
    			);
    		}
    	}
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index()
    {
    	$this->assign('title','校园管理后台');
    	$webmasterloginid = session('webmasterloginid');
    	if ($webmasterloginid) {
    		redirect('/schooladmin/main', 1, '已经登录...');
    	}
    	if ($this->isPost()) {
	    	$UserLogin = M("UserLogin");
	        $validate = array(
	            array('webmaster', 'email', 'webmaster email格式错误'),
	            array('password', 'require', '密码不能为空'),
	            array('cypher', 'require', '口令不能为空'),
	        );
	        $AdminUser->setProperty("_validate", $validate);
	        $result = $UserLogin->create();
	        if (!$result) {
	            exit($UserLogin->getError());
	        } else {
	            $webmaster = trim(addslashes(htmlspecialchars(strip_tags($_POST["webmaster"]))));
	            $password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));
	            $cypher = trim(addslashes(htmlspecialchars(strip_tags($_POST["cypher"]))));
	            $password = md5($password);
	            $cypher = md5($cypher);
	            $recordWebmasterUserLogin = $UserLogin->where("email = '$webmaster'")->find();
	            if (!empty($recordWebmasterUserLogin['uid'])) {
	            	if ($password != $recordWebmasterUserLogin['password']) {
                	    redirect('/schooladmin', 2, 'password wrong...');
                	}
                	$thisyear = getdate();
                    $cypherNum = "help".$thisyear['mon'].$thisyear['mday'];
                    if ($cypher != md5($cypherNum)) {
                	    redirect('/schooladmin', 2, 'cypher wrong...');
                	}
                	$recordSchoolInfo = i_school_domain();
                	if ($recordSchoolInfo['id'] != $recordWebmasterUserLogin['school']) {
                		redirect('/schooladmin', 2, '你不能登录其他学校的后台...');
                	}
                	session('webmasterloginid',$recordWebmasterUserLogin['uid']);
                	session('webmasterloginname',$recordWebmasterUserLogin['nickname']);
                	
                	/**
                	 * webmaster user operating record
                	 */
                	$SchoolRecord = M("SchoolRecord");
                	$newSchoolRecordData = array(
                		'id' => '',
                		'sys_id' => '',
                		'uid' => $recordWebmasterUserLogin['uid'],
                		'sid' => $recordSchoolInfo['id'],
                		'record' => '登录校园管理后台',
                		'time' => time()
                	
                	);
                	$SchoolRecord->add($newSchoolRecordData);
                    redirect('/schooladmin/main', 3, '登录成功...');
	            }
	        }
    	}
        $this->display();
    }
    
    public function quit()
    {
    	$this->assign('title','安全退出');
    	$webmasterloginid = session('webmasterloginid');
        session('webmasterloginid', null);
        session('webmasterloginname', null);
        
        /**
         * admin user operating record
         */
        if (!empty($webmasterloginid)) {
	        /**
	         * webmaster user operating record
	         */
	        $recordSchoolInfo = i_school_domain();
	        $SchoolRecord = M("SchoolRecord");
	        $newSchoolRecordData = array(
                'id' => '',
                'sys_id' => '',
                'uid' => $webmasterloginid,
                'sid' => $recordSchoolInfo['id'],
                'record' => '退出校园管理后台',
                'time' => time()
	         
	        );
	        $SchoolRecord->add($newSchoolRecordData);
        }
        $this->display();
    }
    
    public function index()
    {
    	
    }

}

?>