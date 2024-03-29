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
        $UserLogin = M("UserLogin");
        $SchoolSystem = M("SchoolSystem");
        $recordSchoolInfo = i_school_domain();
        $this->assign('title','我帮圈圈 '.$recordSchoolInfo['school'].' 帮助主题社交网站');
        $this->assign('schoolid',$recordSchoolInfo['id']);
        $this->assign('schoolname',$recordSchoolInfo['school']);
        $this->assign('recordSchoolInfo',$recordSchoolInfo);
        $recordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->find();
    	$indexUserValue = '9999,'.$recordSchoolSystem['index_user'];
    	$indexUserValueArray = explode(",", $indexUserValue);
        $indexUserValueArray = array_unique($indexUserValueArray);
        $i = 0;
        foreach ($indexUserValueArray as $valueIn) {
        	$valueIn = (int)$valueIn;
        	if (!empty($valueIn) && $i < 22) {
        		$sqlValueString .= $valueIn.",";
        		$i++;
        	}
        }
        $sqlValueString = substr($sqlValueString, 0, -1);
    	$allUser = $UserLogin->where("uid IN ($sqlValueString)")->order('logintime DESC')->select();
    	$this->assign('allUser',$allUser);
		$allUserNums = $UserLogin->where("school = $recordSchoolInfo[id]")->count();
    	$this->assign('allUserNums',$allUserNums);
    	
    	/**
    	 * login fast user icon
    	 */
    	if (!empty($_COOKIE['userEmail'])) {
    		$userCookieEmail = trim(addslashes(htmlspecialchars(strip_tags($_COOKIE['userEmail']))));
    		$cookieUserLogin = $UserLogin->where("email = '$userCookieEmail'")->field("uid,email,nickname,icon_url")->find();
    		$this->assign('cookieUserLogin',$cookieUserLogin);
    	}
    	
    	/**
         * index_spread_info
         */
        $indexSpreadInfoVaule = stripslashes($recordSchoolSystem['index_spread_info']);
        $this->assign('indexSpreadInfoVaule',$indexSpreadInfoVaule);
        
        /**
         * index background image
         */
        $indexbgimg = $recordSchoolSystem['image_index'];
        $this->assign('indexbgimg',$indexbgimg);
        
        if ($_SERVER['HTTP_HOST'] == 'hue.ihelpoo.com') {
        	redirect('http://hue.ihelpoo.cn', 0, '跳转页面 :)...');
        }
        
        /**
         * is login weibo & qq
         */
        $this->assign('configIsLoginWeibo',C('IS_LOGIN_WEIBO'));
        $this->assign('configIsLoginQq',C('IS_LOGIN_QQ'));
        if(i_is_mobile()) {
        	$this->display('Mobile:index_index');
    	} else {
    		$this->display();
    	}
    }
    
    public function school()
    {
    	$title = "我帮圈圈 帮助主题社交网站 开通校园列表";
    	$this->assign('title',$title);
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	
    	$SchoolInfo = M("SchoolInfo");
    	if ($_GET['status'] == '2') {
    		$recordsSchoolInfo = $SchoolInfo->where("status = 2")->order("initial ASC")->select();
    	} else {
    		$recordsSchoolInfo = $SchoolInfo->where("status = 1")->order("initial ASC")->select();
    	}
        $this->assign('recordsSchoolInfo', $recordsSchoolInfo);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:index_school');
    	} else {
    		$this->display();
    	}
    }
    
    public function changeschool()
    {
    	$title = "我帮圈圈 快速定位学校 帮助主题社交网站";
    	$this->assign('title',$title);
    	$SchoolInfo = M("SchoolInfo");
    	if ($_GET['status'] == '2') {
    		$recordsSchoolInfo = $SchoolInfo->where("status = 2")->order("initial ASC")->select();
    	} else {
    		$recordsSchoolInfo = $SchoolInfo->where("status = 1")->order("initial ASC")->select();
    	}
        $this->assign('recordsSchoolInfo', $recordsSchoolInfo);
        
        if (!empty($_COOKIE['userLoginSchool'])) {
        	//redirect($_COOKIE['userLoginSchool'], 0, '跳转页面 :)...');
        }
        
        $ip = get_client_ip();
        //$ip = '111.177.117.121';
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $data = file_get_contents($url);
        $dataArray = json_decode($data, true);
        $this->assign('dataArray', $dataArray['data']);
        $ipcity = substr($dataArray['data']['city'], 0, 6);
        
        $OpCity = M("OpCity");
        $schoolOpCity = $OpCity->where("`name` LIKE '%" . $ipcity . "%' AND status = 1")->join("i_school_info ON i_op_city.id = i_school_info.city_op")->select();
        $this->assign('schoolOpCity', $schoolOpCity);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:index_changeschool');
    	} else {
    		$this->display();
    	}
    }
    
    public function hot()
    {
    	$recordSchoolInfo = i_school_domain();
    	$title = "热门 ".$recordSchoolInfo['school']." 帮助主题社交网站";
        $this->assign('title',$title);
        $this->assign('schoolname',$recordSchoolInfo['school']);

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

        if ($_GET['w'] == 'plus') {
            $recordList = $RecordSay->where("school_id = $recordSchoolInfo[id] AND say_type = 0 AND time > $timeWidth")
            ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.plus_co DESC')
       	    ->limit(50)
       	    ->select();
        } else if ($_GET['w'] == 'hit') {
            $recordList = $RecordSay->where("school_id = $recordSchoolInfo[id] AND say_type = 0 AND time > $timeWidth")
            ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.comment_co DESC')
       	    ->limit(50)
       	    ->select();
       	    
       	    /**
       	     * sort order by total count
       	     */
       	    foreach ($recordList as $key => $row) {
       	    	$total_co[$key] = $row['comment_co']+$row['plus_co']+$row['diffusion_co']+$row['hit_co'];
       	    }
       	    array_multisort($total_co, SORT_DESC, $recordList);
        } else if($_GET['w'] == 'comment') {
            $recordList = $RecordSay->where("school_id = $recordSchoolInfo[id] AND say_type = 0 AND time > $timeWidth")
        	->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.comment_co DESC')
   	        ->limit(50)
   	        ->select();
        } else if($_GET['w'] == 'diffusion') {
            $recordList = $RecordSay->where("school_id = $recordSchoolInfo[id] AND say_type = 0 AND time > $timeWidth")
        	->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.diffusion_co DESC')
   	        ->limit(50)
   	        ->select();
        } else if($_GET['w'] == 'help') {
            $recordList = $RecordSay->where("school_id = $recordSchoolInfo[id] AND say_type = 1 AND time > $timeWidth")
            ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.hit_co DESC')
   	        ->limit(50)
   	        ->select();
        } else if($_GET['w'] == 'helpreply') {
            $recordList = $recordList = $RecordSay->where("school_id = $recordSchoolInfo[id] AND say_type = 1 AND time > $timeWidth")
            ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->order('i_record_say.comment_co DESC')
   	        ->limit(50)
   	        ->select();
        } else if($_GET['w'] == 'useractive') {
            $recordList = $UserLogin->where("school = $recordSchoolInfo[id]")->order("active DESC")
   	        ->limit(100)
   	        ->select();
        } else {
       	    redirect('/index/hot?w=hit&t=week', 0, '缺少参数, 跳转到指定页面 :)...');
        }
        $this->assign('recordList',$recordList);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:index_hot');
    	} else {
    		$this->display();
    	}
    }

    public function mate()
    {
    	$recordSchoolInfo = i_school_domain();
    	$title = "大家 ".$recordSchoolInfo['school']." 帮助主题社交网站";
        $this->assign('title',$title);
        $this->assign('schoolname',$recordSchoolInfo['school']);

    	$UserLogin = M("UserLogin");
    	$UserInfo = M("UserInfo");
    	$allUserNums = $UserLogin->where("school = $recordSchoolInfo[id]")->count();
    	$this->assign('allUserNums',$allUserNums);

    	$page = i_page_get_num();
        $count = 15;
        $offset = $count * $page;
        
        $_GET['sex'] = htmlentities($_GET['sex']);
        if (strlen($_GET['sex']) > 3) {
        	$_GET['sex'] = 0;
        }
        $_GET['n'] = htmlentities($_GET['n']);
        if (strlen($_GET['n']) > 3) {
        	$_GET['n'] = 0;
        }
        $_GET['specialty'] = htmlentities($_GET['specialty']);
        if (strlen($_GET['specialty']) > 6) {
        	$_GET['specialty'] = 0;
        }
        
        if (!empty($_GET['n'])) {
            if (preg_match("/[0-9]/", $_GET['n']) && $_GET['n'] > 0) {
                $number = (int)$_GET['n'];
            } else {
                $number = 0;
				exit('what are you doing? Tanks for bless, by cho!');
            }
        } else {
            $number = 0;
        }
        
        if (!empty($_GET['sex'])) {
            if (preg_match("/[0-9]/", $_GET['sex']) && $_GET['sex'] > 0) {
                $sex = (int)$_GET['sex'];
            } else {
                $sex = 0;
				exit('what are you doing? Tanks for bless, by cho!');
            }
        } else {
            $sex = 0;
        }
        
        if (!empty($_GET['specialty'])) {
        	if (preg_match("/[0-9]/", $_GET['specialty']) && $_GET['specialty'] > 0) {
        		$specialty = (int)$_GET['specialty'];
        	} else {
        		$specialty = 0;
        		exit('what are you doing? Tanks for bless, by cho!');
        	}
        } else {
        	$specialty = 0;
        }
        
        /**
         * set default url to ?new
         */
        if (!isset($_GET['w'])) {
            redirect('/index/mate?w=grade', 0, '缺少参数, 跳转到指定页面 :)...');
        }
        $_GET['w'] = htmlentities($_GET['w']);

    	if ($_GET['w'] == "academy") {

    		/**
    		 * show specialty name
    		 */
    		$OpSpecialty = M("OpSpecialty");
    		$resultsSpecialty = $OpSpecialty->where("school = $recordSchoolInfo[id] AND academy = $number")->select();
    		$this->assign('resultsSpecialty',$resultsSpecialty);
            if (!empty($number)) {
            	if (!empty($sex)) {
            		if (!empty($specialty)) {
            			$totalusers = $UserInfo->where("academy_op = $number AND specialty_op = $specialty AND i_user_login.sex = $sex AND (i_user_login.type = 1 OR i_user_login.type = 2 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_info.uid = i_user_login.uid')->count();
            			$userList = $UserInfo->where("academy_op = $number AND specialty_op = $specialty  AND i_user_login.sex = $sex AND (i_user_login.type = 1 OR i_user_login.type = 2 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.school = $recordSchoolInfo[id]")
            			->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset, $count)->select();
            		} else {
            			$totalusers = $UserInfo->where("academy_op = $number AND i_user_login.sex = $sex AND (i_user_login.type = 1 OR i_user_login.type = 2 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_info.uid = i_user_login.uid')->count();
            			$userList = $UserInfo->where("academy_op = $number AND i_user_login.sex = $sex AND (i_user_login.type = 1 OR i_user_login.type = 2 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.school = $recordSchoolInfo[id]")
            			->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset, $count)->select();
            		}
            	} else {
            		if (!empty($specialty)) {
            			$totalusers = $UserInfo->where("academy_op = $number AND specialty_op = $specialty AND (i_user_login.type = 1 OR i_user_login.type = 2 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_info.uid = i_user_login.uid')->count();
            			$userList = $UserInfo->where("academy_op = $number AND specialty_op = $specialty AND (i_user_login.type = 1 OR i_user_login.type = 2 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.school = $recordSchoolInfo[id]")
            			->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset, $count)->select();
            		} else {
            			$totalusers = $UserInfo->where("academy_op = $number AND (i_user_login.type = 1 OR i_user_login.type = 2 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_info.uid = i_user_login.uid')->count();
            			$userList = $UserInfo->where("academy_op = $number AND (i_user_login.type = 1 OR i_user_login.type = 2 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.school = $recordSchoolInfo[id]")
            			->join('i_user_login ON i_user_info.uid = i_user_login.uid')
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset, $count)->select();
            		}
            	}
            } else {
            	$totalusers = 150;
            	if (!empty($sex)) {
	            	$userList = $UserInfo->where("(i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND i_user_login.sex = $sex AND i_user_login.school = $recordSchoolInfo[id]")
	                ->join('i_user_login ON i_user_info.uid = i_user_login.uid')
	                ->order('i_user_login.online DESC,i_user_login.icon_fl DESC')
	                ->limit($offset, $count)->select();
            	} else {
            		$userList = $UserInfo->where("(i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5)  AND i_user_login.school = $recordSchoolInfo[id]")
	                ->join('i_user_login ON i_user_info.uid = i_user_login.uid')
	                ->order('i_user_login.online DESC,i_user_login.icon_fl DESC')
	                ->limit($offset, $count)->select();
            	}
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
            		if ($number == 994) {
            			$userList = $UserLogin->where("sex = $sex AND type = 4 AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("sex = $sex AND type = 4 AND school = $recordSchoolInfo[id]")->count();
            		} else if ($number == 995) {
            			$userList = $UserLogin->where("sex = $sex AND type = 5 AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("sex = $sex AND type = 5 AND school = $recordSchoolInfo[id]")->count();
            		} else if ($number == 996) {
            			$userList = $UserLogin->where("sex = $sex AND type = 6 AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("sex = $sex AND type = 6 AND school = $recordSchoolInfo[id]")->count();
            		} else if ($number == 5) {
            			$userList = $UserLogin->where("enteryear <= $num AND sex = $sex AND (i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("enteryear <= $num AND sex = $sex AND (i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND school = $recordSchoolInfo[id]")->count();
            		} else {
            			$userList = $UserLogin->where("enteryear = $num AND sex = $sex AND (i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC,i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("enteryear = $num AND sex = $sex AND (i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND school = $recordSchoolInfo[id]")->count();
            		}
            	} else {
            		if ($number == 994) {
            			$userList = $UserLogin->where("type = 4 AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("type = 4 AND school = $recordSchoolInfo[id]")->count();
            		} else if ($number == 995) {
            			$userList = $UserLogin->where("type = 5 AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("type = 5 AND school = $recordSchoolInfo[id]")->count();
            		} else if ($number == 996) {
            			$userList = $UserLogin->where("type = 6 AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("type = 6 AND school = $recordSchoolInfo[id]")->count();
            		} else if ($number == 5) {
            			$userList = $UserLogin->where("enteryear <= $num AND (i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("enteryear <= $num AND (i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND school = $recordSchoolInfo[id]")->count();
            		} else {
            			$userList = $UserLogin->where("enteryear = $num AND (i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND school = $recordSchoolInfo[id]")
            			->order('i_user_login.icon_fl DESC,i_user_login.online DESC')
            			->limit($offset,$count)->select();
            			$totalusers = $UserLogin->where("enteryear = $num AND (i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND school = $recordSchoolInfo[id]")->count();
            		}
            	}
            } else {
            	if (!empty($sex)) {
            		$userList = $UserLogin->where("(i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND sex = $sex AND online != 2 AND school = $recordSchoolInfo[id]")
            		->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            		->limit($offset,$count)->select();
            		$totalusers = 150;
            	} else {
            		$userList = $UserLogin->where("(i_user_login.type = 1 OR i_user_login.type = 4 OR i_user_login.type = 5) AND online != 2 AND school = $recordSchoolInfo[id]")
            		->order('i_user_login.icon_fl DESC, i_user_login.online DESC')
            		->limit($offset,$count)->select();
            		$totalusers = 150;
            	}
            }
            $this->assign('userList',$userList);
    	} else if ($_GET['w'] == "new") {
    	    $userList = $UserLogin->where("type = 1 AND school = $recordSchoolInfo[id]")
    	    ->order('uid DESC,icon_fl DESC')
    	    ->limit($offset, $count)->select();
    	    $this->assign('userList',$userList);
    	    $totalusers = 150;
    	} else if ($_GET['w'] == "dormitory") {
            if (!empty($number)) {
            	$totalusers = $UserInfo->where("dormitory_op = $number AND i_user_login.type = 1 AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_info.uid = i_user_login.uid')->count();
                $userList = $UserInfo->where("dormitory_op = $number AND i_user_login.type = 1 AND i_user_login.school = $recordSchoolInfo[id]")
                ->join('i_user_login ON i_user_info.uid = i_user_login.uid')
                ->order('i_user_login.online DESC,i_user_login.icon_fl DESC')
                ->limit($offset, $count)->select();
            } else {
                $totalusers = 150;
                $userList = $UserInfo->where("i_user_login.type = 1 AND i_user_login.school = $recordSchoolInfo[id]")
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
    	$academy = $OpAcademy->where("school = $recordSchoolInfo[id]")->select();
    	$this->assign('academy',$academy);

    	/**
    	 * show dormitory name
    	 */
    	$OpDormitory = M("OpDormitory");
    	$dormitory = $OpDormitory->where("school = $recordSchoolInfo[id]")->select();
    	$this->assign('dormitory',$dormitory);
    	
    	if(i_is_mobile()) {
        	$this->display('Mobile:index_mate');
    	} else {
    		$this->display();
    	}
    }

    public function group()
    {
    	$recordSchoolInfo = i_school_domain();
    	$title = "组织 ".$recordSchoolInfo['school']." 帮助主题社交网站";
        $this->assign('title',$title);
        $this->assign('schoolname',$recordSchoolInfo['school']);
        $page = i_page_get_num();
        $count = 21;
        $offset = $count * $page;
        $UserLogin = M("UserLogin");
        $groupList = $UserLogin->where("i_user_login.type = 2 AND i_user_login.school = $recordSchoolInfo[id]")
        ->join("i_user_info ON i_user_info.uid = i_user_login.uid")
        ->limit($offset, $count)
        ->select();
        $this->assign('groupList',$groupList);
        $totalrecords = $UserLogin->where("i_user_login.type = 2 AND i_user_login.school = $recordSchoolInfo[id]")->count();
        $this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:index_group');
    	} else {
    		$this->display();
    	}
    }

	public function business()
    {
    	$recordSchoolInfo = i_school_domain();
    	$title = "商家 ".$recordSchoolInfo['school']." 帮助主题社交网站";
        $this->assign('title',$title);
        $this->assign('schoolname',$recordSchoolInfo['school']);
        $page = i_page_get_num();
        $count = 21;
        $offset = $count * $page;
        $UserLogin = M("UserLogin");
        $groupList = $UserLogin->where("i_user_login.type = 3 AND i_user_login.school = $recordSchoolInfo[id]")
        ->join('i_user_info ON i_user_info.uid = i_user_login.uid')
        ->select();
        $this->assign('groupList',$groupList);
        $totalrecords = $UserLogin->where("i_user_login.type = 3 AND i_user_login.school = $recordSchoolInfo[id]")->count();
        $this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
        
        if(i_is_mobile()) {
        	$this->display('Mobile:index_business');
    	} else {
    		$this->display();
    	}
    }
    
    public function app()
    {
    	$recordSchoolInfo = i_school_domain();
    	$title = "应用 ".$recordSchoolInfo['school']." 帮助主题社交网站";
        $this->assign('title',$title);
        $this->assign('schoolname',$recordSchoolInfo['school']);
        
        $page = i_page_get_num();
        $count = 30;
        $offset = $count * $page;
        $SchoolAd = M("SchoolAd");
        $appSchoolAd = $SchoolAd->where("type = '1' AND sid = $recordSchoolInfo[id]")->order("time DESC")->limit($offset, $count)->select();
        $this->assign('appSchoolAd', $appSchoolAd);
        
        $totalrecords = $SchoolAd->where("type = '1' AND sid = $recordSchoolInfo[id]")->count();
        $this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }
    
    public function applyverify()
    {
    	$recordSchoolInfo = i_school_domain();
    	$userloginid = session('userloginid');
    	$title = "申请校园组织、校园周边商家认证 ".$recordSchoolInfo['school']." 帮助主题社交网站";
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$this->assign('title',$title);
    	if ($this->isPost()) {
    		$UserApplyverify = M("UserApplyverify");
	    	$verify_type = trim(addslashes(htmlspecialchars(strip_tags($_POST["verify_type"]))));
	    	$name = trim(addslashes(htmlspecialchars(strip_tags($_POST["name"]))));
	    	$mobile = trim(addslashes(htmlspecialchars(strip_tags($_POST["mobile"]))));
	    	$qq = trim(addslashes(htmlspecialchars(strip_tags($_POST["qq"]))));
	    	$remark = trim(addslashes(htmlspecialchars(strip_tags($_POST["remark"]))));
	    	$isexistUserApplyverify = $UserApplyverify->where("name = '$name' AND mobile = '$mobile'")->find();
	    	if (!empty($isexistUserApplyverify['id'])) {
	    		$this->ajaxReturn(0, "已经提交过了，请勿重复提交", "wrong");
	    	}
	    	if (!empty($name) && !empty($mobile) && !empty($verify_type)) {
		    	$newuserApplyverifyData = array(
			    	'id' => '',
			    	'uid' => $userloginid,
			    	'school_id' => $recordSchoolInfo['id'],
			    	'verify_status' => 0,
			    	'verify_type' => $verify_type,
			    	'name' => $name,
			    	'mobile' => $mobile,
			    	'qq' => $qq,
			    	'remark' => $remark,
			    	'idcard' => '',
			    	'time' => time()
		    	);
		    	$UserApplyverify->add($newuserApplyverifyData);
		    	$this->ajaxReturn(0, "提交成功", "yes");
	    	} else {
	    		$this->ajaxReturn(0, "出错了", "wrong");
	    	}
    	}
    	$this->display();
    }

    /**
     * invite
     */
    public function invitenums()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$userloginid = session('userloginid');
        $this->assign('title','我邀请的朋友 排行');
        $UserInvite = M("UserInvite");
        $selectSql = "SELECT i_user_invite.uid, COUNT(i_user_invite.uid) AS invite_nums, award, nickname, icon_url, school FROM `i_user_invite`, `i_user_login` WHERE `award` = '1' AND `school` = $recordSchoolInfo[id] AND i_user_invite.uid = i_user_login.uid GROUP BY uid  ORDER BY invite_nums DESC";
        $selectSqlResult = $UserInvite->query($selectSql);
        $this->assign('resultUserInvite',$selectSqlResult);
    	$this->display();
    }

}

?>