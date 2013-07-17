<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class WeiboAction extends Action {

    protected function _initialize()
    {
    	$userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$IUserLogin = D("IUserLogin");
    		$userloginedrecord = $IUserLogin->userExists($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	} else {
        	redirect('/user/notlogin', 0, '你还没有登录呢...');
        }
        header("Content-Type:text/html; charset=utf-8");
    }
    
    /**
     *
     */
    public function transhelp()
    {
        $userloginid = session('userloginid');
        $this->assign('title','微博帮助转入');
        $UserLogin = M("UserLogin");
        $RecordSay = M("RecordSay");
        if ($this->isPost()) {
        	if (!empty($_POST['commenttext'])) {
        		if (!empty($_POST['weibopic'])) {
        			$RecordOutimg = M("RecordOutimg");
        			$imageOutNewData = array(
                        'id' => '',
                        'uid' => $userloginid,
                        'rpath' => $_POST['weibopic'],
                        'time' => time(),
        			);
        			$weibopicture = $RecordOutimg->add($imageOutNewData);
        		} else {
        			$weibopicture = '';
        		}
	        	$dataRecordSay = array(
		            'sid' => NULL,
		            'uid' => $userloginid,
		            'say_type' => 0,
		            'content' => $_POST['commenttext'],
		            'image' => $weibopicture,
		            'url' => '',
		            'authority' => 0,
		            'time' => time(),
		            'last_comment_ti' => time(),
		            'from' => '新浪微博求助-'.$_POST['weiboid']
	            );
	            $sayLastInsertId = $RecordSay->add($dataRecordSay);
	            $itemSayUrI = 'item/say/'.$sayLastInsertId;
	            $this->ajaxReturn($itemSayUrI,'移动成功','ok');
        	}
        }
        $this->display();
    }
    

}

?>