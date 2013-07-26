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
    				'uid' => $webmasterloginid,
    				'nickname' => $webmasterloginname,
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
	        $UserLogin->setProperty("_validate", $validate);
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
                	
                	$SchoolWebmaster = M("SchoolWebmaster");
                	$recordSchoolWebmaster = $SchoolWebmaster->where("uid = $recordWebmasterUserLogin[uid]")->find();
                	if (empty($recordSchoolWebmaster['id'])) {
                		redirect('/schooladmin', 2, '你不是站长，不能登录校园管理后台...');
                	}
                	$recordSchoolInfo = i_school_domain();
                	if ($recordSchoolInfo['id'] != $recordWebmasterUserLogin['school']) {
                		redirect('/schooladmin', 2, '不能登录其他学校的校园管理后台...');
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
                	$callname = $recordSchoolWebmaster['position'] == 'm' ? '站长' : '副站长';
                    redirect('/schooladmin/main', 3, '登录成功! 欢迎您,'.$callname.' ...');
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
    
    public function main()
    {
    	$webmaster = logincheck();
    	$this->assign('title','校园管理后台');
    	$this->display();
    }
    
    public function parameter()
    {
    	$webmaster = logincheck();
    	$this->assign('title','校园参数配置');
    	$webmaster['uid'];
    	$webmaster['nickname'];
    	
    	$recordSchoolInfo = i_school_domain();
    	$SchoolSystem = M("SchoolSystem");
    	$recordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->select();
    	$this->assign('recordSchoolSystem',$recordSchoolSystem);
    	$lastrecordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->find();
    	$this->assign('lastrecordSchoolSystem',$lastrecordSchoolSystem);
    	
        /**
         * update system parameter
         */
        if ($this->isPost()) {
        	$parameter = $_POST['parameter'];
        	
        	/**
	         * webmaster user operating record
	         */
	        $SchoolRecord = M("SchoolRecord");
	        $newSchoolRecordData = array(
                'id' => '',
                'sys_id' => '',
                'uid' => $webmaster['uid'],
                'sid' => $recordSchoolInfo['id'],
                'record' => '修改配置参数',
                'time' => time()
	         
	        );
	        $SchoolRecord->add($newSchoolRecordData);
	        redirect('/schooladmin/parameter', 1, 'ok...');
        }
        $this->display();
    }

}

?>