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
    		$IUserLogin = D("IUserLogin");
    		$userloginedrecord = $IUserLogin->userExists($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    	}
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
    	$userloginid = session('userloginid');
        
        $UserLogin = M("UserLogin");
        $SchoolSystem = M("SchoolSystem");
        $SchoolInfo = M("SchoolInfo");
        $recordSchoolInfo = $SchoolInfo->find(1);
        $this->assign('title','我帮圈圈 '.$recordSchoolInfo['school'].' 帮助主题社交网站');
        $recordSchoolSystem = $SchoolSystem->where("sid = 1")->order("time DESC")->find();
    	$indexUserValue = '9999,'.$recordSchoolSystem['index_user'];
    	$indexUserValueArray = explode(",", $indexUserValue);
        $indexUserValueArray = array_unique($indexUserValueArray);
        foreach ($indexUserValueArray as $valueIn) {
        	$valueIn = (int)$valueIn;
        	if (!empty($valueIn)) {
        		$sqlValueString .= $valueIn.",";
        	}
        }
        $sqlValueString = substr($sqlValueString, 0, -1);
    	$allUser = $UserLogin->where("uid IN ($sqlValueString)")->order('logintime DESC')->select();
    	$this->assign('allUser',$allUser);
		$allUserNums = $UserLogin->count();
    	$this->assign('allUserNums',$allUserNums);
    	
    	/**
         * index_spread_info
         */
        $indexSpreadInfoVaule = $recordSchoolSystem['index_spread_info'];
        $this->assign('indexSpreadInfoVaule',$indexSpreadInfoVaule);
        
        /**
         * index background image
         */
        $indexbgimg = $recordSchoolSystem['image_index'];
        $this->assign('indexbgimg',$indexbgimg);
        $this->display();
    }
    
    public function school()
    {
    	$title = "我帮圈圈 帮助主题社交网站 开通校园列表";
        $this->assign('title',$title);
    }

    public function hot()
    {
        $title = "热门 湖北民族学院帮助主题社交网站";
        $this->assign('title',$title);

        $RecordSay = M("RecordSay");
        $UserLogin = M("UserLogin");
        if ($_GET['t'] == 'week') {
            $timeWidth = time() - 3600 * 24 * 7;
        } else if ($_GET['t'] == 'day') {
            $timeWidth = time() - 3600 * 24;
        } else if ($_GET['t'] == 'all') {
            $timeWidth = 0;
        } else {
        	redirect('/index/hot?w=hit&t=week', 0, '缺少参数, 跳转到指定页面 :)...');
        }

        if ($_GET['w'] == 'hit') {
            $recordList = $RecordSay->where("say_type = 0 AND time > $timeWidth")
            ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.hit_co DESC')
       	    ->limit(50)
       	    ->select();
        } else if($_GET['w'] == 'comment') {
            $recordList = $RecordSay->where("say_type = 0 AND time > $timeWidth")
        	->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.comment_co DESC')
   	        ->limit(50)
   	        ->select();
        } else if($_GET['w'] == 'diffusion') {
            $recordList = $RecordSay->where("say_type = 0 AND time > $timeWidth")
        	->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.diffusion_co DESC')
   	        ->limit(50)
   	        ->select();
        } else if($_GET['w'] == 'help') {
            $recordList = $RecordSay->where("say_type = 1 AND time > $timeWidth")
            ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.hit_co DESC')
   	        ->limit(50)
   	        ->select();
        } else if($_GET['w'] == 'helpreply') {
            $recordList = $recordList = $RecordSay->where("say_type = 1 AND time > $timeWidth")
            ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.comment_co DESC')
   	        ->limit(50)
   	        ->select();
        } else if($_GET['w'] == 'useractive') {
            $recordList = $UserLogin->order('active DESC')
   	        ->limit(100)
   	        ->select();
        } else if($_GET['w'] == 'usercoins') {
   	        $recordList = $UserLogin->order('coins DESC')
   	        ->limit(50)
   	        ->select();
        } else {
       	    redirect('/index/hot?w=hit&t=week', 0, '缺少参数, 跳转到指定页面 :)...');
        }
        $this->assign('recordList',$recordList);
        $this->display();
    }

    public function mate()
    {
        $title = "大家 湖北民族学院帮助主题社交网站";
        $this->assign('title',$title);

    	$UserLogin = M("UserLogin");
    	$UserInfo = M("UserInfo");
    	$allUserNums = $UserLogin->where("status != 0")->count();
    	$this->assign('allUserNums',$allUserNums);

    	$page = i_page_get_num();
        $count = 15;
        $offset = $count * $page;
        if (!empty($_GET['n'])) {
            if (preg_match("/[0-9]/", $_GET['n']) && $_GET['n'] > 0) {
                $number = (int)$_GET['n'];
            } else {
                $number = 0;
				exit('what are you doing? Tanks for bless, bye cho!');
            }
        } else {
            $number = 0;
        }
        
        if (!empty($_GET['sex'])) {
            if (preg_match("/[0-9]/", $_GET['sex']) && $_GET['sex'] > 0) {
                $sex = (int)$_GET['sex'];
            } else {
                $sex = 0;
				exit('what are you doing? Tanks for bless, bye cho!');
            }
        } else {
            $sex = 0;
        }

        /**
         * set default url to ?new
         */
        if (!isset($_GET['w'])) {
            redirect('/index/mate?w=grade', 0, '缺少参数, 跳转到指定页面 :)...');
        }

    	if ($_GET['w'] == "academy") {

    		/**
    		 * show specialty name
    		 */
    		$OpSpecialty = M("OpSpecialty");
    		$resultsSpecialty = $OpSpecialty->where("academy = $number")->select();
    		$this->assign('resultsSpecialty',$resultsSpecialty);
            if (!empty($number)) {
            	if (!empty($_GET['specialty'])) {
            		if (preg_match("/[0-9]/", $_GET['specialty']) && $_GET['specialty'] > 0) {
            			$specialty = (int)$_GET['specialty'];
            		} else {
            			$specialty = 0;
            			exit('what are you doing? Tanks for bless, bye cho!');
            		}
            	} else {
            		$specialty = 0;
            	}
            	if (!empty($sex)) {
            		if (!empty($specialty)) {
            			$totalusers = $UserInfo->where("academy_op = $number AND specialty_op = $specialty AND i_user_login.sex = $sex")->join('i_user_login ON i_user_info.uid = i_user_login.uid')->count();
            			$userList = $UserInfo->where("academy_op = $number AND specialty_op = $specialty  AND i_user_login.sex = $sex AND i_user_login.type = 1")
            			->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset, $count)->select();
            		} else {
            			$totalusers = $UserInfo->where("academy_op = $number AND i_user_login.sex = $sex")->join('i_user_login ON i_user_info.uid = i_user_login.uid')->count();
            			$userList = $UserInfo->where("academy_op = $number AND i_user_login.sex = $sex AND i_user_login.type = 1")
            			->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset, $count)->select();
            		}
            	} else {
            		if (!empty($specialty)) {
            			$totalusers = $UserInfo->where("academy_op = $number AND specialty_op = $specialty")->count();
            			$userList = $UserInfo->where("academy_op = $number AND specialty_op = $specialty AND i_user_login.type = 1")
            			->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset, $count)->select();
            		} else {
            			$totalusers = $UserInfo->where("academy_op = $number")->count();
            			$userList = $UserInfo->where("academy_op = $number AND i_user_login.type = 1")
            			->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset, $count)->select();
            		}
            	}
            } else {
            	$totalusers = 150;
            	$userList = $UserInfo->where("i_user_login.type = 1 AND i_user_login.sex = $sex")
            	->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            	->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            	->limit($offset, $count)->select();
            }
    	    $this->assign('userList',$userList);
    	} else if ($_GET['w'] == "grade") {
            if (!empty($number)) {
            	$thisyear = getdate();
            	if ($thisyear['mon'] > 8) {
            		$num = $thisyear['year'] - $number + 1;
            	} else {
            		$num = $thisyear['year'] - $number;
            	}
            	if (!empty($sex)) {
            		if ($number == 5) {
            			$userList = $UserLogin->where("enteryear <= $num AND sex = $sex AND type = 1")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("enteryear <= $num AND sex = $sex AND type = 1")->count();
            		} else {
            			$userList = $UserLogin->where("enteryear = $num AND sex = $sex AND type = 1")
            			->order('i_user_login.icon_fl DESC,i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("enteryear = $num AND sex = $sex AND type = 1")->count();
            		}
            	} else {
            		if ($number == 5) {
            			$userList = $UserLogin->where("enteryear <= $num AND type = 1")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("enteryear <= $num AND type = 1")->count();
            		} else {
            			$userList = $UserLogin->where("enteryear = $num AND type = 1")
            			->order('i_user_login.icon_fl DESC,i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("enteryear = $num AND type = 1")->count();
            		}
            	}
            } else {
            	if (!empty($sex)) {
            		$userList = $UserLogin->where("type = 1 AND sex = $sex AND online != 2")
            		->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            		->limit($offset,$count)->select();
            		$totalusers = 150;
            	} else {
            		$userList = $UserLogin->where("type = 1 AND online != 2")
            		->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            		->limit($offset,$count)->select();
            		$totalusers = 150;
            	}
            }
            $this->assign('userList',$userList);
    	} else if ($_GET['w'] == "new") {
    	    $userList = $UserLogin->where('i_user_login.type = 1')
    	    ->order('i_user_login.uid DESC,i_user_login.icon_fl DESC')
    	    ->limit($offset, $count)->select();
    	    $this->assign('userList',$userList);
    	    $totalusers = 150;
    	} else if ($_GET['w'] == "dormitory") {
            if (!empty($number)) {
            	$totalusers = $UserInfo->where("dormitory_op = $number")->count();
                $userList = $UserInfo->where("dormitory_op = $number AND i_user_login.type = 1")
                ->join('i_user_login ON i_user_info.uid = i_user_login.uid')
                ->order('i_user_login.online DESC,i_user_login.icon_fl DESC')
                ->limit($offset, $count)->select();
            } else {
                $totalusers = 150;
                $userList = $UserInfo->where("i_user_login.type = 1")
                ->join('i_user_login ON i_user_info.uid = i_user_login.uid')
                ->order('i_user_login.online DESC,i_user_login.icon_fl DESC')
                ->limit($offset, $count)->select();
            }
            $this->assign('userList',$userList);
    	}
    	$this->assign('totalusers',$totalusers);
    	$totalPages = ceil($totalusers / $count);
        $this->assign('totalPages',$totalPages);

    	/**
    	 * show academy name
    	 */
    	$OpAcademy = M("OpAcademy");
    	$academy = $OpAcademy->select();
    	$this->assign('academy',$academy);

    	/**
    	 * show dormitory name
    	 */
    	$OpDormitory = M("OpDormitory");
    	$dormitory = $OpDormitory->select();
    	$this->assign('dormitory',$dormitory);
    	$this->display();
    }

    public function group()
    {
        $title = "组织 湖北民族学院帮助主题社交网站";
        $this->assign('title',$title);
        $page = i_page_get_num();
        $count = 10;
        $offset = $count * $page;
        $UserLogin = M("UserLogin");
        $groupList = $UserLogin->where('i_user_login.type = 2')
        ->join('i_user_info ON i_user_info.uid = i_user_login.uid')
        ->limit($offset, $count)
        ->select();
        $this->assign('groupList',$groupList);
        $totalrecords = $UserLogin->where('i_user_login.type = 2')->count();
        $this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }

	public function business()
    {
        $title = "商家 湖北民族学院帮助主题社交网站";
        $this->assign('title',$title);
        $page = i_page_get_num();
        $count = 10;
        $offset = $count * $page;
        $UserLogin = M("UserLogin");
        $groupList = $UserLogin->where('i_user_login.type = 3')
        ->join('i_user_info ON i_user_info.uid = i_user_login.uid')
        ->select();
        $this->assign('groupList',$groupList);
        $totalrecords = $UserLogin->where('i_user_login.type = 3')->count();
        $this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }

    /**
     * invite
     */
    public function invitenums()
    {
    	$userloginid = session('userloginid');
        $this->assign('title','我邀请的朋友 排行');
        $UserInvite = M("UserInvite");
        $totalInviteUserNums = $UserInvite->count();
        $this->assign('totalInviteUserNums',$totalInviteUserNums);
        $selectSql = "SELECT i_user_invite.uid, COUNT(i_user_invite.uid) AS invite_nums, award, nickname, icon_url FROM `i_user_invite`, `i_user_login` WHERE `award` = '1' AND i_user_invite.uid = i_user_login.uid GROUP BY uid  ORDER BY invite_nums DESC";
        $selectSqlResult = $UserInvite->query($selectSql);
        $this->assign('resultUserInvite',$selectSqlResult);
    	$this->display();
    }

}

?>