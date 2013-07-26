<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class SupportAction extends Action {

    protected function _initialize() {
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	}
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index() {
    	$this->assign('title','使用帮助');
        $this->display();
    }
    
    public function fillaccount()
    {
    	$this->assign('title','完善账号');
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$userHash = htmlspecialchars(trim($_GET["_URL_"][3]));
    	$UserLogin = M("UserLogin");
    	
    	/**
         * post
         */
        if ($this->isPost()) {
	        $validate = array(
	            array('email', 'email', '邮箱格式不对'),
	            array('email','','邮箱他人已经占用，请重填！',0,'unique',1),
	            array('password', 'require', '密码不能为空'),
	        );
	        $UserLogin->setProperty("_validate", $validate);
	        $result = $UserLogin->create();
	        if (!$result) {
	            $errorRegister = $UserLogin->getError();
	            $this->ajaxReturn(0,$errorRegister,'wrong');
	        } else {
	        	$postuserId = (int)htmlspecialchars(strtolower(trim($_POST["userId"])));
	        	$postuserHash = htmlspecialchars(strtolower(trim($_POST["userHash"])));
		        if (md5($postuserId*2) != $postuserHash) {
	    			$this->ajaxReturn(0,'密钥错误','wrong');
	    		}
	        	
	            $email = htmlspecialchars(strtolower(trim($_POST["email"])));
	            $password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));
	            $password = md5($password);
	            $recordUserLogin = $UserLogin->find($postuserId);
	            if (empty($recordUserLogin['email'])) {
		            $newUserlogignData = array(
		            	'uid' => $postuserId,
		            	'status' => '1',
						'email' => $email,
		            	'password' => $password,
		            );
		            $newUserId = $UserLogin->save($newUserlogignData);
		            if ($newUserId) {
		            	$this->ajaxReturn(0,'','yes');
		            } else {
		            	$this->ajaxReturn(0,'出错了','wrong');
		            }
	            } else {
	            	$this->ajaxReturn(0,'您的账号登录资料已经完善，可以直接登录了','wrong');
	            }
	        }
        }
    	if (md5($userId*2) != $userHash) {
    		redirect('/', 3, '密钥错误 3秒后页面跳转 :(...');
    	}
    	$recordUserLogin = $UserLogin->find($userId);
    	$this->assign('recordUserLogin', $recordUserLogin);
    	$this->assign('userId', $userId);
    	$this->assign('userHash', $userHash);
    	$this->display();
    }

}

?>