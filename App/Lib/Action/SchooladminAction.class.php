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
    			$SchoolRecord = M("SchoolRecord");
    			$resultsSchoolRecord = $SchoolRecord->where("uid = $webmasterloginid")->order("time DESC")->find();
    			$timegap = time() - $resultsSchoolRecord['time'];
    			if ($timegap > 1800) {
    				session('webmasterloginid', null);
        			session('webmasterloginname', null);
    				redirect('/schooladmin', 3, '挂起页面时间太长登录失效，请重新登录...');
    			}
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
    	$recordSchoolInfo = i_school_domain();
    	$SchoolSystem = M("SchoolSystem");
    	$recordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->limit(15)->select();
    	$this->assign('recordSchoolSystem',$recordSchoolSystem);
    	$lastrecordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->find();
    	$this->assign('lastrecordSchoolSystem',$lastrecordSchoolSystem);
    	$UserLogin = M("UserLogin");
    	$totalSchoolUsers = $UserLogin->where("school = $recordSchoolInfo[id]")->count();
    	
        /**
         * update system parameter
         */
        if ($this->isPost()) {
        	$index_user = trim(addslashes(htmlspecialchars(strip_tags($_POST['index_user']))));
        	$index_spread_info = trim(addslashes($_POST['index_spread_info']));
        	$image_index = trim(addslashes(htmlspecialchars(strip_tags($_POST['image_index']))));
        	$image_mobile = trim(addslashes(htmlspecialchars(strip_tags($_POST['image_mobile']))));
        	$about = trim(addslashes($_POST['about']));
        	
        	$newSchoolSystem = array(
        		'id' => '',
        		'sid' => $recordSchoolInfo['id'],
        		'total_users' => $totalSchoolUsers,
        		'index_user' => $index_user,
        		'index_spread_info' => $index_spread_info,
        		'about' => $about,
        		'image_index' => $image_index,
        		'image_mobile' => $image_mobile,
        		'time' => time()
        	);
        	$insertSchoolSystemId = $SchoolSystem->add($newSchoolSystem);
        	
        	/**
	         * webmaster user operating record
	         */
	        $SchoolRecord = M("SchoolRecord");
	        $newSchoolRecordData = array(
                'id' => '',
                'sys_id' => $insertSchoolSystemId,
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
    
    public function indexbgimg()
    {
    	$webmaster = logincheck();
    	$this->assign('title','校园参数配置');
    	
    	$recordSchoolInfo = i_school_domain();
    	$schoolid = $recordSchoolInfo['id'];
    	$this->assign('schoolid',$schoolid);
    	
    	Vendor('Ihelpoo.Upyun');
        $upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
        $imageStorageUrl = image_storage_url();
    	if ($this->isPost()) {
    		if (!empty($_FILES)) {
    			if ($_FILES["uploadimage"]["error"] > 0) {
    				redirect('/schooladmin/indexbgimg', 3, 'file error...'.$_FILES["uploadimage"]["error"]);
    			} else {
    				$imageType = $_FILES["uploadimage"]["type"];
    				$imageSize = $_FILES["uploadimage"]["size"];
    				$imageTmpName = $_FILES["uploadimage"]["tmp_name"];
    				$tempRealSize = getimagesize($_FILES["uploadimage"]["tmp_name"]);
    				$imageRealWidth = $tempRealSize['0'];
    				$imageRealHeight = $tempRealSize['1'];
    			}
    			
    			if ($imageSize > 800000) {
    				redirect('/schooladmin/indexbgimg', 3, 'error...上传图片太大, 最大能上传单张 3.5MB');
    			} else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg') {
    				
    				/**
        			 * storage in upyun
        			 */
        			$fh = fopen($imageTmpName, 'rb');
        			$storageFilename = '/school/'.$schoolid.'/'.time().'.jpg';
        			$rsp = $upyun->writeFile($storageFilename, $fh, True);
        			fclose($fh);
        			$newfilepath = $imageStorageUrl.$storageFilename;
        			
        			/**
        			 * insert into i_school_album
        			 */
        			$SchoolAlbum = M("SchoolAlbum");
        			$newAlbumIconData = array(
        				'id' => '',
        				'school_id' => $schoolid,
        				'url' => $newfilepath,
        				'size' => $imageSize,
        				'height' => $imageRealHeight,
        				'width' => $imageRealWidth,
        				'time' => time()
        			);
        			$SchoolAlbum->add($newAlbumIconData);
        			
        			/**
        			 * webmaster user operating record
        			 */
        			$SchoolRecord = M("SchoolRecord");
        			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '上传图片'.$newfilepath,
		                'time' => time()
        			);
        			$SchoolRecord->add($newSchoolRecordData);
        			redirect('/schooladmin/indexbgimg', 1, 'success...');
    			} else {
    				redirect('/schooladmin/indexbgimg', 3, 'error...上传图片格式错误, 目前仅支持.jpg');
    			}
    		}
    	}
    	$page = i_page_get_num();
        $count = 10;
        $offset = $page * $count;
    	$SchoolAlbum = M("SchoolAlbum");
    	$recordSchoolAlbum = $SchoolAlbum->where("school_id = $schoolid")->order("time DESC")->limit($offset, $count)->select();
    	$this->assign('recordSchoolAlbum',$recordSchoolAlbum);
    	$totalRecordNums = $SchoolAlbum->where("school_id = $schoolid")->count();
        $this->assign('totalRecordNums', $totalRecordNums);
        $totalPages = ceil($totalRecordNums / $count);
        $this->assign('totalPages', $totalPages);
    	$this->display();
    }
    
    public function advertisement()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '学院管理');
    	$schoolid = $recordSchoolInfo['id'];
    	$SchoolAd = M("SchoolAd");
    	
    	if ($this->isPost()) {
    		$adtype = (int)$_POST['adtype'];
    		$adcontent = trim(addslashes($_POST['adcontent']));
    		if (!empty($adtype) && !empty($adcontent)) {
    			$newAdArray = array(
    				'id' => '',
    				'sid' => $recordSchoolInfo['id'],
    				'type' => $adtype,
    				'content' => $adcontent,
    				'time' => time()
    			);
    			$SchoolAd->add($newAdArray);
    			
    			/**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
		            'id' => '',
		            'sys_id' => '',
		            'uid' => $webmaster['uid'],
		            'sid' => $recordSchoolInfo['id'],
		            'record' => '添加广告 adtype'.$adtype.' - adconent'.$adcontent,
		            'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    		}
    		redirect('/schooladmin/advertisement', 1, '添加广告成功 ok...');
    	}
    	
    	/**
    	 * delete advertisement
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		if (!empty($suredelid)) {
    			$deleteSchoolAd = $SchoolAd->where("id = $suredelid AND sid = $schoolid")->find();
    			
    			/**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '删除广告 time:'.$deleteSchoolAd['type'].' content'.$deleteSchoolAd['content'],
		                'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    			$SchoolAd->where("id = $suredelid AND sid = $schoolid")->delete();
    			redirect('/schooladmin/advertisement', 1, '删除广告成功 ok...');
    		}
    	}
    	
    	$page = i_page_get_num();
	    $count = 15;
	    $offset = $page * $count;
    	$recordSchoolAd = $SchoolAd->where("sid = $schoolid")->order("time DESC")->limit($offset,$count)->select();
    	$this->assign('recordSchoolAd', $recordSchoolAd);
    	
    	/**
    	 * page link
    	 */
    	$totalReocrdNums = $SchoolAd->where("sid = $schoolid")->count();
    	$this->assign('totalRecordNums', $totalReocrdNums);
    	$totalPages = ceil($totalReocrdNums / $count);
    	$this->assign('totalPages', $totalPages);
		
    	$this->display();
    }
    
    /**
     * school management
     */
    public function academy()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '学院管理');
    	$schoolid = $recordSchoolInfo['id'];
    	$OpAcademy = M("OpAcademy");
    	
    	if ($this->isPost()) {
    		$academyid = (int)$_POST['academyid'];
    		$name = trim(addslashes(htmlspecialchars(strip_tags($_POST['name']))));
    		if (!empty($name) && !empty($schoolid)) {
	    		if (empty($academyid)) {
		    		$newOpAcademy = array(
		    			'name' => $name,
		    			'school' => $schoolid,
		    		);
		    		$OpAcademy->add($newOpAcademy);
		    		
	    			/**
        			 * webmaster user operating record
        			 */
        			$SchoolRecord = M("SchoolRecord");
        			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '添加学院:'.$name,
		                'time' => time()
        			);
        			$SchoolRecord->add($newSchoolRecordData);
		    		redirect('/schooladmin/academy', 1, '添加学院成功 ok...');
	    		} else {
	    			$updateOpAcademy = array(
		    			'id' => $academyid,
		    			'name' => $name,
		    			'school' => $schoolid,
		    		);
		    		$OpAcademy->save($updateOpAcademy);
	    			
	    			/**
        			 * webmaster user operating record
        			 */
        			$SchoolRecord = M("SchoolRecord");
        			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '更新学院:'.$name,
		                'time' => time()
        			);
        			$SchoolRecord->add($newSchoolRecordData);
		    		redirect('/schooladmin/academy', 1, '更新学院成功 ok...');
	    		}
    		}
    	}
    	
    	/**
    	 * delete academy
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		$OpSpecialty = M("OpSpecialty");
    		$isExistOpSpecialty = $OpSpecialty->where("academy = $suredelid")->find();
    		if (!empty($isExistOpSpecialty['id'])) {
    			redirect('/schooladmin/academy', 3, '存在相关专业，不能删除，请先删除该学院下面的专业 exist...');
    		}
    		$UserInfo = M("UserInfo");
    		$isExistUserInfo = $UserInfo->where("academy_op = $suredelid")->find();
    		if (!empty($isExistUserInfo['uid'])) {
    			redirect('/schooladmin/academy', 3, '该学院下面已经有同学了，不能删除，建议修改名字 exist...');
    		}
    		if (!empty($suredelid)) {
    			$deleteOpAcademy = $OpAcademy->where("id = $suredelid AND school = $schoolid")->find();
    			
    			/**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '删除学院 name:'.$deleteOpAcademy['name'],
		                'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    			$OpAcademy->where("id = $suredelid AND school = $schoolid")->delete();
    			redirect('/schooladmin/academy', 1, '删除学院成功 ok...');
    		}
    	}
    	
		$page = i_page_get_num();
	    $count = 15;
	    $offset = $page * $count;
		if (!empty($schoolid)) {
			$recordsOpAcademy = $OpAcademy->where("school = $schoolid")->limit($offset,$count)->select();
			$this->assign('recordsOpAcademy', $recordsOpAcademy);
			
			/**
    		 * page link
    		 */
    		$totalReocrdNums = $OpAcademy->where("school = $schoolid")->count();
    		$this->assign('totalRecordNums', $totalReocrdNums);
    		$totalPages = ceil($totalReocrdNums / $count);
    		$this->assign('totalPages', $totalPages);
		}
    	$this->display();
    }
    
	public function specialty()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','专业管理');
    	$OpSpecialty = M("OpSpecialty");
    	$schoolid = $recordSchoolInfo['id'];
    	$this->assign('schoolid',$schoolid);
    	$academyid = (int)$_GET['academyid'];
    	$this->assign('academyid',$academyid);
    	
    	if ($this->isPost()) {
    		$specialtyid = (int)$_POST['specialtyid'];
    		$schoolpostid = $recordSchoolInfo['id'];
    		$academypostid = (int)$_POST['academy'];
    		$name = trim(addslashes(htmlspecialchars(strip_tags($_POST['name']))));
    		if (!empty($name) && !empty($schoolpostid) && !empty($academypostid)) {
	    		if (empty($specialtyid)) {
		    		$newOpSpecialty = array(
		    			'name' => $name,
		    			'academy' => $academypostid,
		    			'school' => $schoolpostid
		    		);
		    		$OpSpecialty->add($newOpSpecialty);
	    			
	    			/**
	    			 * webmaster user operating record
	    			 */
	    			$SchoolRecord = M("SchoolRecord");
	    			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '添加专业:'.$name,
		                'time' => time()
	    			);
	    			$SchoolRecord->add($newSchoolRecordData);
		    		redirect('/schooladmin/specialty', 1, '添加专业成功 ok...');
	    		} else {
	    			$updateOpSpecialty = array(
		    			'id' => $specialtyid,
		    			'name' => $name,
	    				'academy' => $academypostid,
		    			'school' => $schoolid
		    		);
		    		$OpSpecialty->save($updateOpSpecialty);
		    		
	    			/**
	    			 * webmaster user operating record
	    			 */
	    			$SchoolRecord = M("SchoolRecord");
	    			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '更新专业:'.$name,
		                'time' => time()
	    			);
	    			$SchoolRecord->add($newSchoolRecordData);
		    		redirect('/schooladmin/specialty', 1, '更新专业成功 ok...');
	    		}
    		}
    	}
    	
    	/**
    	 * delete specialty
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		$UserInfo = M("UserInfo");
    		$isExistUserInfo = $UserInfo->where("specialty_op = $suredelid")->find();
    		if (!empty($isExistUserInfo['uid'])) {
    			redirect('/schooladmin/specialty', 3, '该专业下面已经有同学了，不能删除，建议修改名字 exist...');
    		}
    		if (!empty($suredelid)) {
    			$deleteOpSpecialty = $OpSpecialty->where("id = $suredelid AND school = $schoolid")->find();
    			
    			/**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
		            'id' => '',
		            'sys_id' => '',
		            'uid' => $webmaster['uid'],
		            'sid' => $recordSchoolInfo['id'],
		            'record' => '删除专业:'.$deleteOpSpecialty['name'],
		            'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    			
    			$OpSpecialty->where("id = $suredelid AND school = $schoolid")->delete();
    			redirect('/schooladmin/specialty', 1, '删除专业成功 ok...');
    		}
    	}
		
		if (!empty($schoolid)) {
			$OpAcademy = M("OpAcademy");
			$recordOpAcademy = $OpAcademy->where("school = $schoolid")->select();
			$this->assign('recordOpAcademy', $recordOpAcademy);
		}
		
		$page = i_page_get_num();
	    $count = 15;
	    $offset = $page * $count;
		
		if (!empty($schoolid) && !empty($academyid)) {
			$recordsOpSpecialty = $OpSpecialty->where("school = $schoolid && academy = $academyid")->limit($offset,$count)->select();
			$this->assign('recordsOpSpecialty', $recordsOpSpecialty);
			
			/**
    		 * page link
    		 */
    		$totalReocrdNums = $OpSpecialty->where("school = $schoolid && academy = $academyid")->count();
    		$this->assign('totalRecordNums', $totalReocrdNums);
    		$totalPages = ceil($totalReocrdNums / $count);
    		$this->assign('totalPages', $totalPages);
		}
    	$this->display();
    }
    
    public function dormitory()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','寝室管理');
    	$OpDormitory = M("OpDormitory");
    	$schoolid = $recordSchoolInfo['id'];
    	$this->assign('schoolid',$schoolid);
    	
    	if ($this->isPost()) {
    		$dormitoryid = (int)$_POST['dormitoryid'];
    		$schoolpostid = $recordSchoolInfo['id'];
    		$name = trim(addslashes(htmlspecialchars(strip_tags($_POST['name']))));
    		$type = (int)$_POST['type'];
    		if (!empty($name) && !empty($schoolpostid) && !empty($type)) {
	    		if (empty($dormitoryid)) {
		    		$newOpDormitory = array(
		    			'name' => $name,
		    			'type' => $type,
		    			'school' => $schoolpostid
		    		);
		    		$OpDormitory->add($newOpDormitory);
	    			
	    			/**
	    			 * webmaster user operating record
	    			 */
	    			$SchoolRecord = M("SchoolRecord");
	    			$newSchoolRecordData = array(
			            'id' => '',
			            'sys_id' => '',
			            'uid' => $webmaster['uid'],
			            'sid' => $recordSchoolInfo['id'],
			            'record' => '添加寝室:'.$name,
			            'time' => time()
	    			);
	    			$SchoolRecord->add($newSchoolRecordData);
		    		redirect('/schooladmin/dormitory', 1, '添加寝室成功 ok...');
	    		} else {
	    			$updateOpDormitory = array(
		    			'id' => $dormitoryid,
		    			'name' => $name,
		    			'type' => $type,
		    			'school' => $schoolid
		    		);
		    		$OpDormitory->save($updateOpDormitory);
		    		
	    			/**
	    			 * webmaster user operating record
	    			 */
	    			$SchoolRecord = M("SchoolRecord");
	    			$newSchoolRecordData = array(
			            'id' => '',
			            'sys_id' => '',
			            'uid' => $webmaster['uid'],
			            'sid' => $recordSchoolInfo['id'],
			            'record' => '更新寝室:'.$name,
			            'time' => time()
	    			);
	    			$SchoolRecord->add($newSchoolRecordData);
		    		redirect('/schooladmin/dormitory', 1, '更新寝室成功 ok...');
	    		}
    		}
    	}
    	
    	/**
    	 * delete dormitory
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		$UserInfo = M("UserInfo");
    		$isExistUserInfo = $UserInfo->where("dormitory_op = $suredelid")->find();
    		if (!empty($isExistUserInfo['uid'])) {
    			redirect('/schooladmin/dormitory', 3, '该寝室下面已经有同学了，不能删除，建议修改名字 exist...');
    		}
    		if (!empty($suredelid)) {
    			$deleteOpDormitory = $OpDormitory->where("id = $suredelid AND school = $schoolid")->find();

    			/**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
			        'id' => '',
			        'sys_id' => '',
			        'uid' => $webmaster['uid'],
			        'sid' => $recordSchoolInfo['id'],
			        'record' => '删除寝室:'.$deleteOpDormitory['name'],
			        'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    			$OpDormitory->where("id = $suredelid AND school = $schoolid")->delete();
    			redirect('/schooladmin/dormitory', 1, '删除寝室成功 ok...');
    		}
    	}
		
		$page = i_page_get_num();
	    $count = 15;
	    $offset = $page * $count;
		
		if (!empty($schoolid)) {
			$recordsOpDormitory = $OpDormitory->where("school = $schoolid")->limit($offset,$count)->select();
			$this->assign('recordsOpDormitory', $recordsOpDormitory);
			
			/**
    		 * page link
    		 */
    		$totalReocrdNums = $OpDormitory->where("school = $schoolid")->count();
    		$this->assign('totalRecordNums', $totalReocrdNums);
    		$totalPages = ceil($totalReocrdNums / $count);
    		$this->assign('totalPages', $totalPages);
		}
    	$this->display();
    }
    
    
    /**
     * user management
     */
    public function user()
    {
    	$webmaster = logincheck();
    	$this->assign('title', '用户管理');
        $UserLogin = M("UserLogin");
        $userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        $recordSchoolInfo = i_school_domain();
        
        /**
         * search
         */
        if (!empty($_POST['search'])) {
        	$searchWords = trim(addslashes(htmlspecialchars(strip_tags($_POST['search']))));
        	if (preg_match("/@/i", $searchWords)) {
        		$userLoginRecord = $UserLogin->where("email = '$searchWords'")->find();
        	} else if (preg_match("/[0-9]/", $searchWords) && $searchWords > 9999) {
        		$userLoginRecord = $UserLogin->where("uid = '$searchWords'")->find();
        	} else {
        		$userLoginRecord = $UserLogin->where("nickname like '%$searchWords%'")->find();
        	}
        	
        	/**
	         * webmaster user operating record
	         */
	        $SchoolRecord = M("SchoolRecord");
	        $newSchoolRecordData = array(
                'id' => '',
                'sys_id' => '',
                'uid' => $webmaster['uid'],
                'sid' => $recordSchoolInfo['id'],
                'record' => 'user search 搜索 searchWords:'.$searchWords,
                'time' => time()
	         
	        );
	        $SchoolRecord->add($newSchoolRecordData);
        	
        	if (!empty($userLoginRecord['uid'])) {
        		redirect('/schooladmin/user/'.$userLoginRecord['uid'], 0, 'ok...');
        	} else {
        		redirect('/schooladmin/user?uid=empty', 1, 'empty...');
        	}
        }

        if (!empty($_POST['password']) && !empty($_POST['uid'])) {
            $isUserExist = $UserLogin->find($_POST['uid']);
            if ($isUserExist['uid']) {
            	$setPassword = array(
            		'uid' => $isUserExist['uid'],
            	    'password' => md5($_POST['password']),
            	);
                $UserLogin->save($setPassword);
                
                /**
                 * webmaster user operating record
                 */
                $SchoolRecord = M("SchoolRecord");
                $newSchoolRecordData = array(
	                'id' => '',
	                'sys_id' => '',
	                'uid' => $webmaster['uid'],
	                'sid' => $recordSchoolInfo['id'],
	                'record' => 'user reset pw 重置密码. uid:'.$isUserExist['uid'].' password:'.md5($_POST['password']),
	                'time' => time()
                );
                $SchoolRecord->add($newSchoolRecordData);
                
                redirect('/schooladmin/user', 1, 'update user password ok...');
            }
        }

        if (!empty($_POST['recordlimit']) && !empty($_POST['uid'])) {
        	$UserStatus = M("UserStatus");
        	$newRecordLimit = (int)$_POST['recordlimit'];
            $isUserExist = $UserStatus->find($_POST['uid']);
            if ($isUserExist['uid']) {
            	$setRecordLimit = array(
            		'uid' => $isUserExist['uid'],
            	    'record_limit' => $newRecordLimit,
            	);
                $UserStatus->save($setRecordLimit);
                
                /**
                 * webmaster user operating record
                 */
                $SchoolRecord = M("SchoolRecord");
                $newSchoolRecordData = array(
	                'id' => '',
	                'sys_id' => '',
	                'uid' => $webmaster['uid'],
	                'sid' => $recordSchoolInfo['id'],
	                'record' => 'change user record limit. uid:'.$isUserExist['uid'].' record_limit:'.$newRecordLimit,
	                'time' => time()
                );
                $SchoolRecord->add($newSchoolRecordData);
                redirect('/schooladmin/user', 1, 'update user record limit ok...');
            }
        }
        
		if (!empty($_POST['type']) && !empty($_POST['uid'])) {
        	$newUserType = (int)$_POST['type'];
			$isUserExist = $UserLogin->find($_POST['uid']);
            if ($isUserExist['uid']) {
            	$setUserType = array(
            		'uid' => $isUserExist['uid'],
            	    'type' => $newUserType,
            	);
                $UserLogin->save($setUserType);
                
                /**
                 * webmaster user operating record
                 */
                $SchoolRecord = M("SchoolRecord");
                $newSchoolRecordData = array(
	                'id' => '',
	                'sys_id' => '',
	                'uid' => $webmaster['uid'],
	                'sid' => $recordSchoolInfo['id'],
	                'record' => 'change user type. uid:'.$isUserExist['uid'].' type:'.$newUserType,
	                'time' => time()
                );
                $SchoolRecord->add($newSchoolRecordData);
                redirect('/schooladmin/user', 1, 'update user type ok...');
            }
        }

        if (!empty($userId)) {
        	$recordUserLogin = $UserLogin->find($userId);
        	$this->assign('recordUserLogin',$recordUserLogin);
        	if ($recordUserLogin['school'] != $recordSchoolInfo['id']) {
        		redirect('/schooladmin/user', 1, '仅查询到其他学校用户，你无权管理...');
        	}
        	$UserInfo = M("UserInfo");
        	$recordUserInfo = $UserInfo->find($userId);
        	
        	/**
        	 * user album
        	 */
        	$UserAlbum = M("UserAlbum");
        	$userAlbumNums = $UserAlbum->where("uid = $userId")->count();
        	$userAlbumSize = $UserAlbum->where("uid = $userId")->sum('size');

        	/**
        	 * user msg
        	 */
        	$MsgComment = M("MsgComment");
        	$MsgActive = M("MsgActive");
        	$MsgAt = M("MsgAt");
        	$userMsgCommentNums = $MsgComment->where("uid = $userId")->count();
        	$userMsgActiveNums = $MsgActive->where("uid = $userId")->count();
        	$userMsgAtNums = $MsgAt->where("touid = $userId")->count();

        	/**
        	 * user talk
        	 */
        	$TalkContent = M("TalkContent");
        	$userTalkNums = $TalkContent->where("uid = $userId OR touid = $userId")->count();

        	/**
        	 * show
        	 */
        	$userOtherInfo = array(
        		'uid' => $recordUserLogin['uid'],
        		'nickname' => $recordUserLogin['nickname'],
        		'realname' => $recordUserInfo['realname'],
        		'userAlbumNums' => $userAlbumNums,
        		'userAlbumSize' => round($userAlbumSize/(1024*1024),2)."MB",
        		'userMsgCommentNums' => $userMsgCommentNums,
        		'userMsgActiveNums' => $userMsgActiveNums,
        		'userMsgAtNums' => $userMsgAtNums,
        		'userTalkNums' => $userTalkNums,
        	);
        	$this->assign('userOtherInfo',$userOtherInfo);
        }
        $this->display();
    }
    
    public function applyverify()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','校园组织、周边商家用户申请');
    	
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
        $UserApplyverify = M("UserApplyverify");
        
        if (!empty($_GET['statuschange'])) {
        	$statuschangeid = (int)$_GET['statuschange'];
        	$recordUserApplyverify = $UserApplyverify->find($statuschangeid);
        	if (!empty($recordUserApplyverify['id']) && $recordUserApplyverify['verify_status'] == '0') {
        		$updateStatus = array(
        			'id' => $recordUserApplyverify['id'],
        			'verify_status' => '1',
        		);
        		$UserApplyverify->save($updateStatus);
        		
        		/**
        		 * webmaster user operating record
        		 */
        		$SchoolRecord = M("SchoolRecord");
        		$newSchoolRecordData = array(
		            'id' => '',
		            'sys_id' => '',
		            'uid' => $webmaster['uid'],
		            'sid' => $recordSchoolInfo['id'],
		            'record' => '标记为处理 uid:'.$recordUserApplyverify['uid'].' +name:'.$recordUserApplyverify['name'].' +verify_type:'.$recordUserApplyverify['verify_type'],
		            'time' => time()
        		);
        		$SchoolRecord->add($newSchoolRecordData);
        		redirect('/schooladmin/applyverify', 1, 'update status type ok...');
        	} else if (!empty($recordUserApplyverify['id']) && $recordUserApplyverify['verify_status'] == '1'){
        		$updateStatus = array(
        			'id' => $recordUserApplyverify['id'],
        			'verify_status' => '0',
        		);
        		$UserApplyverify->save($updateStatus);
        		
        		/**
        		 * webmaster user operating record
        		 */
        		$SchoolRecord = M("SchoolRecord");
        		$newSchoolRecordData = array(
		            'id' => '',
		            'sys_id' => '',
		            'uid' => $webmaster['uid'],
		            'sid' => $recordSchoolInfo['id'],
		            'record' => '标记为未处理 uid:'.$recordUserApplyverify['uid'].' +name:'.$recordUserApplyverify['name'].' +verify_type:'.$recordUserApplyverify['verify_type'],
		            'time' => time()
        		);
        		$SchoolRecord->add($newSchoolRecordData);
        		redirect('/schooladmin/applyverify', 1, 'update status type ok...');
        	}
        }
        
        $recordUserApplyverify = $UserApplyverify->where("school_id = $recordSchoolInfo[id]")->order("time DESC")->limit($offset,$count)->select();
    	$this->assign('recordUserApplyverify', $recordUserApplyverify);
    	$totalrecords = $UserApplyverify->where("school_id = $recordSchoolInfo[id]")->count();
    	$this->assign('totalrecords', $totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages', $totalPages);
    	$this->display();
    }
    
    public function orderusericon()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','大家页面头像排序');
    	$UserLogin = M("UserLogin");
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
    	if ($this->isPost()) {
    		if (!empty($_POST['userid']) && !empty($_POST['way'])) {
    			$userid = $_POST['userid'];
    			$way = $_POST['way'];
    			$userLoginRecord = $UserLogin->find($userid);
    			$icon_fl = $userLoginRecord['icon_fl'];
    			if ($way == 'up') {
    				$icon_fl++;
    				if ($icon_fl > 3) {
    					$icon_fl = 3;
    				}
    				$newUserIconFlArray = array(
    					'uid' => $userLoginRecord['uid'],
    					'icon_fl' => $icon_fl,
    				);
    				$UserLogin->save($newUserIconFlArray);
    				
    				/**
        			 * webmaster user operating record
        			 */
        			$SchoolRecord = M("SchoolRecord");
        			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '修改头像排序 uid:'.$userLoginRecord['uid'].' +icon_fl:'.$icon_fl,
		                'time' => time()
        			);
        			$SchoolRecord->add($newSchoolRecordData);
    				$this->ajaxReturn(0,'排序提前('.$icon_fl.')','yes');
    			} else if ($way = 'down') {
    				$icon_fl--;
    				if ($icon_fl < 1) {
    					$icon_fl = 0;
    				}
    				$newUserIconFlArray = array(
    					'uid' => $userLoginRecord['uid'],
    					'icon_fl' => $icon_fl,
    				);
    				$UserLogin->save($newUserIconFlArray);
    				
    				/**
        			 * webmaster user operating record
        			 */
        			$SchoolRecord = M("SchoolRecord");
        			$newSchoolRecordData = array(
		                'id' => '',
		                'sys_id' => '',
		                'uid' => $webmaster['uid'],
		                'sid' => $recordSchoolInfo['id'],
		                'record' => '修改头像排序 uid:'.$userLoginRecord['uid'].' -icon_fl:'.$icon_fl,
		                'time' => time()
        			);
        			$SchoolRecord->add($newSchoolRecordData);
    				$this->ajaxReturn(0,'排序置后('.$icon_fl.')','yes');
    			}
    		}
    	}
    	$userLoginRecords = $UserLogin->where("school = $recordSchoolInfo[id]")->order("icon_fl DESC, logintime DESC")->limit($offset,$count)->select();
    	$this->assign('userLoginRecords',$userLoginRecords);
    	$totalusers = $UserLogin->where("school = $recordSchoolInfo[id]")->count();
    	$this->assign('totalusers',$totalusers);
    	$totalPages = ceil($totalusers / $count);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }
    
    public function realnameallowmf()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '用户实名修改');
        $AdminRealnamemf = M("AdminRealnamemf");
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        $recordAdminRealnamemf = $AdminRealnamemf->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_admin_realnamemf.uid")->order("time DESC")->limit($offset,$count)->select();
        $this->assign('adminRealnamemf',$recordAdminRealnamemf);

        /**
         * page link
         */
        $totalReocrdNums = $AdminRealnamemf->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_admin_realnamemf.uid")->count();
        $this->assign('totalRecordNums', $totalReocrdNums);
        $totalPages = ceil($totalReocrdNums / $count);
        $this->assign('totalPages', $totalPages);

        /**
         * post
         */
        if (!empty($_GET['sendmail'])) {
            $uid = (int)$_GET['sendmail'];
            $UserLogin = M("UserLogin");
            $recordUserLogin = $UserLogin->find($uid);
            $toEmail = $recordUserLogin['email'];
            $toNickname = $recordUserLogin['nickname'];

            /**
             * send welcome email, do not throw exception
             */
            Vendor('Ihelpoo.Email');
            $emailObj = new Email();
            $isSend = $emailObj->realNameModifiedAllow($toEmail, $toNickname);
            if ($isSend) {

            	/**
                 * send system message.
                 * "您的真实姓名可以修改了!";
                 */
                i_savenotice('10000', $uid, 'setting/realfirst', '');  //TODO bounce message

                /**
                 * update i_admin_realnalemf.allow
                 */
                $recordAdminRealnamemf = $AdminRealnamemf->where("uid = $uid AND allow != '1'")->select();
                foreach ($recordAdminRealnamemf as $realnamemf) {
	                $dataAf = array(
	                	'id' => $realnamemf['id'],
	                    'allow' => 1
	                );
	                $AdminRealnamemf->save($dataAf);
                }

                /**
                 * update i_user_infoconn
                 */
                $UserInfo = M("UserInfo");
                $dataUserInfoconn = array(
                	'uid' => $uid,
                    'realname_re' => 0
                );
                $isUpdateUserInfo = $UserInfo->save($dataUserInfoconn);
                
                /**
                 * webmaster user operating record
                 */
                $SchoolRecord = M("SchoolRecord");
                $newSchoolRecordData = array(
		            'id' => '',
		            'sys_id' => '',
		            'uid' => $webmaster['uid'],
		            'sid' => $recordSchoolInfo['id'],
		            'record' => 'realnameallowmf 允许修改真实姓名. uid:'.$uid.' realname_re:0',
		            'time' => time()
                );
                $SchoolRecord->add($newSchoolRecordData);
                if ($isUpdateUserInfo) {
                	redirect('/schooladmin/realnameallowmf', 3, 'update user modify ok...');
                } else {
                	redirect('/schooladmin/realnameallowmf', 3, 'update info... isUpdateAdminRealnamemf:'.'; isUpdateUserInfo:'.$isUpdateUserInfo);
                }
            }
        }
        $this->display();
    }
    
    public function userhonor()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '授予荣誉奖励活跃');
    	$UserLogin = M("UserLogin");
        if (!empty($_POST['get_user_level'])) {
            $userAll = $UserLogin->where("school = $recordSchoolInfo[id]")->select();
            $userStrings = NULL;
            $i = 0;
            foreach ($userAll as $user) {
                $userLevel = i_degree($user['active']);
                if ($userLevel >= $_POST['get_user_level']) {
                	$userStrings .= $user['uid'].";";
                	$i ++;
                }
            }
            $userStrings = substr($userStrings, 0, -1);
            $this->assign('userStrings', $userStrings);
            $this->assign('totalUserNums', ">= level".$_POST['get_user_level']." 共".$i."人");
            
            /**
             * webmaster user operating record
             */
            $SchoolRecord = M("SchoolRecord");
            $newSchoolRecordData = array(
		        'id' => '',
		        'sys_id' => '',
		        'uid' => $webmaster['uid'],
		        'sid' => $recordSchoolInfo['id'],
		        'record' => 'userhonor get_user_level 查询. level:'.$_POST['get_user_level'].' '.$i.'人',
		        'time' => time()
            );
            $SchoolRecord->add($newSchoolRecordData);
        }


        if (!empty($_POST['honor_content'])) {
            $UserHonor = M("UserHonor");
            $userArray = explode(";", $_POST['user_ids']);

            /**
             * message to owner
             * "你获得了我帮圈圈荣誉，快来看看吧";
        	 */
            $msgSystemType = 'helpschooladmin/userhonor';
            $i = 0;
            foreach ($userArray as $user) {
            	$resultUserLogin = $UserLogin->find($user);
            	if ($resultUserLogin['school'] == $recordSchoolInfo['id']) {
            		$data = array(
	                    'id' => '',
	                    'uid' => $user,
	                    'content' => $_POST['honor_content'],
	                    'time' => time()
            		);
            		$UserHonor->add($data);

            		/**
            		 * insert into system message
            		 */
            		i_savenotice('10000', $user, $msgSystemType, $user); //TODO bounce message
            		$i++;
            	}
            }
        	
        	/**
             * webmaster user operating record
             */
            $SchoolRecord = M("SchoolRecord");
            $newSchoolRecordData = array(
		        'id' => '',
		        'sys_id' => '',
		        'uid' => $webmaster['uid'],
		        'sid' => $recordSchoolInfo['id'],
		        'record' => '授予我帮圈圈荣誉: 共'.$i.'人, content'.$_POST['honor_content'],
		        'time' => time()
            );
            $SchoolRecord->add($newSchoolRecordData);
            redirect('/schooladmin/userhonor', 3, '授予荣誉成功 共'.$i.'人...');
        }
        
        if (!empty($_POST['active_nums']) && !empty($_POST['active_reason'])) {
        	$activeNums = $_POST['active_nums'];
        	$activeReason = trim(addslashes(htmlspecialchars(strip_tags($_POST['active_reason']))));
        	if ($activeNums > 200) {
        		redirect('/schooladmin/userhonor', 3, '“活跃”最高只能一次奖励200');
        	}
        	
        	/**
        	 * msg active
        	 */
        	$MsgActive = M("MsgActive");
            $userArray = explode(";", $_POST['user_ids']);

            /**
             * message to owner
             * "你获得了我帮圈圈奖励的活跃 :)";
        	 */
            $i = 0;
            $userstring = 0;
            foreach ($userArray as $user) {
            	$recordUserLogin = $UserLogin->find($user);
            	if ($recordUserLogin['school'] == $recordSchoolInfo['id']) {
            		$recordUserLoginActive = $recordUserLogin['active'] == NULL ? 0 : $recordUserLogin['active'];
            		$userStatusData = array(
	    				'uid' => $user,
		                'active' => $recordUserLoginActive + $activeNums,
            		);
            		$UserLogin->save($userStatusData);
            		 
            		/**
            		 * insert into msg active
            		 */
            		$msgActiveArray = array(
						'id' => '',
						'uid' => $user,
						'total' => $recordUserLoginActive,
						'change' => $activeNums,
						'way' => 'add',
						'reason' => $activeReason,
						'time' => time(),
						'deliver' => 0,
            		);
            		$MsgActive->add($msgActiveArray);

            		/**
            		 * insert into system message
            		 */
                    i_savenotice("10000", $user, 'system/active:reward', '');
            		$i++;
            		$userstring .= $user.'-';
            	}
            }
        	
        	/**
             * webmaster user operating record
             */
            $SchoolRecord = M("SchoolRecord");
            $newSchoolRecordData = array(
		        'id' => '',
		        'sys_id' => '',
		        'uid' => $webmaster['uid'],
		        'sid' => $recordSchoolInfo['id'],
		        'record' => '奖励我帮圈圈活跃: 共'.$i.'人, nums:'.$activeNums.', uids:'.$userstring.', reason:'.$activeReason,
		        'time' => time()
            );
            $SchoolRecord->add($newSchoolRecordData);
            redirect('/schooladmin/userhonor', 3, '奖励我帮圈圈活跃 共'.$i.'人...');
        }
        $this->display();
    }
    
    public function userinvite()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '邀请用户 奖励');
    	$UserInvite = M("UserInvite");

    	if ($this->isPost()) {
    		$id = (int)$_POST['id'];
    		$award = (int)$_POST['award'];
    		$recordUserInvite = $UserInvite->find($id);
    		if (!empty($recordUserInvite['id'])) {
    			if ($recordUserInvite['award'] > 0) {
    				redirect('/schooladmin/userinvite', 1, '已经认定过了...');
    			}
    			$updateInviteStatus = array(
    				'id' => $id,
    				'award' => $award,
    			);
    			$UserInvite->save($updateInviteStatus);

    			/**
    			 * send message system
    			 */
	            if ($award == 1) {
	            	$msgContent = "你邀请的用户被认定为有效，加活跃50";
	            	$UserLogin = M("UserLogin");
	            	$recordUserLogin = $UserLogin->find($recordUserInvite['uid']);

	            	/**
	                 * msg active
	                 */
	                $MsgActive = M("MsgActive");
	                $msgActiveArray = array(
		            	'id' => '',
		            	'uid' => $recordUserInvite['uid'],
		            	'total' => $recordUserLogin['active'],
		            	'change' => 50,
		            	'way' => 'add',
		            	'reason' => '成功邀请朋友加入我帮圈圈',
		            	'time' => time(),
		            	'deliver' => 0,
		            );
		            $MsgActive->add($msgActiveArray);
		            $updateUserLoginInfo = array(
                    	'uid' => $recordUserInvite['uid'],
                    	'active' => $recordUserLogin['active'] + 50,
                    );
                	$UserLogin->save($updateUserLoginInfo);
                    i_savenotice("10000", $recordUserInvite['uid'], 'schooladmin/userinvite:success', '');
	            } else {
	            	$msgContent = "你邀请的用户无效:(，暂时不赠送活跃";
                    i_savenotice("10000", $recordUserInvite['uid'], 'schooladmin/userinvite:invalid', '');
	            }

	            /**
	             * webmaster user operating record
	             */
	            $SchoolRecord = M("SchoolRecord");
	            $newSchoolRecordData = array(
			        'id' => '',
			        'sys_id' => '',
			        'uid' => $webmaster['uid'],
			        'sid' => $recordSchoolInfo['id'],
			        'record' => '邀请用户认定, uid:'.$recordUserInvite['uid'].', content:'.$msgContent,
			        'time' => time()
	            );
	            $SchoolRecord->add($newSchoolRecordData);
    			redirect('/schooladmin/userinvite', 1, 'ok...');
    		}
    	}

    	$page = i_page_get_num();
        $count = 20;
        $this->assign('count', $count);
        $offset = $page * $count;
        $resultsUserInvite = $UserInvite->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_user_invite.uid")->order("time DESC")->limit($offset,$count)->select();
        $this->assign('resultsUserInvite', $resultsUserInvite);

        $totalRecords = $UserInvite->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_user_invite.uid")->count();
    	$this->assign('totalRecords',$totalRecords);
    	$totalPages = ceil($totalRecords / $count);
        $this->assign('totalPages',$totalPages);

    	$this->display();
    }
    
    /**
     * record management
     */
    public function record()
    {
        $webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title', '信息管理');
        $RecordSay = M("RecordSay");
        if (!empty($_POST['recordid'])) {
        	$recordid = $_POST['recordid'];
        	$resultRecordSay = $RecordSay->where("sid = $recordid AND school_id = $recordSchoolInfo[id]")->find();
        	if (!empty($resultRecordSay['sid'])) {
        		$this->assign('recordDelete', $resultRecordSay);
        	} else {
        		redirect('/schooladmin/record', 3, '没有找到相关记录或者你没有其他学校记录的删除权限...');
        	}
        	
        	/**
        	 * webmaster user operating record
        	 */
        	$SchoolRecord = M("SchoolRecord");
        	$newSchoolRecordData = array(
			    'id' => '',
			    'sys_id' => '',
			    'uid' => $webmaster['uid'],
			    'sid' => $recordSchoolInfo['id'],
			    'record' => '查询记录, sid:'.$_POST['recordid'].' content:'.$resultRecordSay['content'],
			    'time' => time()
        	);
        	$SchoolRecord->add($newSchoolRecordData);
        }
        if (!empty($_POST['sid'])) {
        	$sid = $_POST['sid'];
        	$RecordHelp = M("RecordHelp");
        	$RecordComment = M("RecordComment");
        	$RecordHelpreply = M("RecordHelpreply");
        	$RecordSay->where("sid = $sid")->delete();
        	$RecordComment->where("sid = $sid")->delete();
        	$RecordHelp->where("sid = $sid")->delete();
        	$RecordHelpreply->where("sid = $sid")->delete();
        	
        	/**
        	 * webmaster user operating record
        	 */
        	$SchoolRecord = M("SchoolRecord");
        	$newSchoolRecordData = array(
			    'id' => '',
			    'sys_id' => '',
			    'uid' => $webmaster['uid'],
			    'sid' => $recordSchoolInfo['id'],
			    'record' => '删除记录, sid:'.$sid,
			    'time' => time()
        	);
        	$SchoolRecord->add($newSchoolRecordData);
        	redirect('/schooladmin/record', 3, '删除记录成功 涉及4个表...');
        }
        $this->display();
    }
    
    /**
     * mall
     */
    public function mallshop()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','小店管理');
    	$page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
    	$UserShop = M("UserShop");
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
		$UserLogin = M("UserLogin");
    		
    	/**
    	 * search
    	 */
    	if (!empty($_GET['searchuid'])) {
    		$searchuid = (int)$_GET['searchuid'];
    		redirect('/schooladmin/mallshop/'.$searchuid, 0, 'ok...');
    	}
    	
    	/**
    	 * post change shop status
    	 */
    	if ($this->isPost()) {
    		$uid = (int)$_POST['uid'];
    		$status = (int)$_POST['status'];
    		$recordUserShop = $UserShop->find($uid);
    		if ($status != $recordUserShop['status']) {
	    		if (!empty($uid) && !empty($status)) {
	    			$updateShopStatus = array(
	    				'uid' => $uid,
	    				'status' => $status,
	    			);
	    			$UserShop->save($updateShopStatus);
	
	    			/**
		             * send system message.
		             */
		            if ($status == 2) {
		            	$msgContent = "资料重新审核通过，您的小店又开通了!";
                        $msgType = 'system/mall:reaudit:ok';
		            } else {
		            	$msgContent = "资料重新审核中，您的小店暂时关闭!";
                        $msgType = 'system/mall:reaudit:no';
		            }
                    i_savenotice('10000', $uid, $msgType, '');

		            
		            /**
		             * webmaster user operating record
		             */
		            $SchoolRecord = M("SchoolRecord");
		            $newSchoolRecordData = array(
					    'id' => '',
					    'sys_id' => '',
					    'uid' => $webmaster['uid'],
					    'sid' => $recordSchoolInfo['id'],
					    'record' => '小店管理, uid:'.$uid. 'content:'.$msgContent,
					    'time' => time()
		            );
		            $SchoolRecord->add($newSchoolRecordData);
	    			redirect('/schooladmin/mallshop/'.$uid, 1, 'ok...');
	    		}
    		}
    	}

    	/**
    	 * list
    	 */
    	if (!empty($userId)) {
    		$recordUserLogin = $UserLogin->find($userId);
    		if ($recordUserLogin['school'] != $recordSchoolInfo['id']) {
    			redirect('/schooladmin/mallshop/'.$searchuid, 3, '无权管理其他学校小店...');
    		}
    		$userShopRecord = $UserShop->find($userId);
    		$this->assign('userShopRecord',$userShopRecord);
    	} else {
	    	$userShopRecords = $UserShop->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_user_shop.uid")
	    	->order("i_user_shop.status DESC, i_user_shop.time ASC")
	    	->field("i_user_shop.status,i_user_shop.uid,i_user_shop.time,i_user_login.nickname,i_user_login.school")
	    	->limit($offset,$count)->select();
	    	$this->assign('userShopRecords',$userShopRecords);
	    	$totalshops = $UserShop->where("i_user_login.school = $recordSchoolInfo[id]")->join("i_user_login ON i_user_login.uid = i_user_shop.uid")->count();
	    	$this->assign('totalshops',$totalshops);
	    	$totalPages = ceil($totalshops / $count);
	        $this->assign('totalPages',$totalPages);
    	}
    	$this->display();
    }
    
    public function mallcommodity()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','商品管理');
    	if ($this->isPost()) {
    		$RecordCommodity = M("RecordCommodity");
    		if (!empty($_POST['search'])) {
        		$searchWords = (int)trim(htmlspecialchars(strip_tags($_POST['search'])));
        		$resultRecordCommodity = $RecordCommodity->where("cid = $searchWords AND school_id = $recordSchoolInfo[id]")->find();
        		$this->assign('resultRecordCommodity', $resultRecordCommodity);
        		
        		$UserShop = M("UserShop");
        		$recordUserShop = $UserShop->where("uid = $resultRecordCommodity[shopid]")->find();
        		$this->assign('recordUserShop', $recordUserShop);
        		
	        	/**
	        	 * webmaster user operating record
	        	 */
	        	$SchoolRecord = M("SchoolRecord");
	        	$newSchoolRecordData = array(
					'id' => '',
					'sys_id' => '',
					'uid' => $webmaster['uid'],
					'sid' => $recordSchoolInfo['id'],
					'record' => 'search 搜索商品: '.$resultRecordCommodity['name'],
					'time' => time()
	        	);
	        	$SchoolRecord->add($newSchoolRecordData);
    		}
    		
    		if (!empty($_POST['cid'])) {
    			$newStatusRecordCommodity = array(
    				'cid' => (int)$_POST['cid'],
    				'status' => (int)$_POST['status']
    			);
    			$RecordCommodity->save($newStatusRecordCommodity);
	        	
	        	/**
	        	 * webmaster user operating record
	        	 */
	        	$SchoolRecord = M("SchoolRecord");
	        	$newSchoolRecordData = array(
					'id' => '',
					'sys_id' => '',
					'uid' => $webmaster['uid'],
					'sid' => $recordSchoolInfo['id'],
					'record' => '更新商品状态 cid:'.$_POST['cid'].' status:'.$_POST['status'],
					'time' => time()
	        	);
	        	$SchoolRecord->add($newSchoolRecordData);
	        	redirect('/schooladmin/mallcommodity/', 1, 'update commodity status ok...');
    		}
    	}
    	$this->display();
    }
    
    public function mallcommodityassess()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','交易管理');
    	$statusMarks = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$this->assign('statusMarks', $statusMarks);
    	
    	$page = i_page_get_num();
        $count = 10;
        $offset = $page * $count;
    	$RecordCommodityassess = M("RecordCommodityassess");
    	if ($statusMarks == 1) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
    		->field("i_record_commodityassess.uid,i_record_commodityassess.cid,i_record_commodityassess.start_ti,i_record_commodityassess.status as status_c,i_record_commodityassess.start_ti,i_record_commodity.school_id")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->order("i_record_commodityassess.start_ti DESC")->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where("i_record_commodityassess.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	} else if ($statusMarks == 2) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.status = 2 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
    		->field("i_record_commodityassess.uid,i_record_commodityassess.cid,i_record_commodityassess.start_ti,i_record_commodityassess.status as status_c,i_record_commodityassess.start_ti,i_record_commodity.school_id")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->order('i_record_commodityassess.start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where("i_record_commodityassess.status = 2 AND i_record_commodity.school_id = $recordSchoolInfo[id]")->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	} else if ($statusMarks == 3) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.status = 3 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
    		->field("i_record_commodityassess.uid,i_record_commodityassess.cid,i_record_commodityassess.start_ti,i_record_commodityassess.status as status_c,i_record_commodityassess.start_ti,i_record_commodity.school_id")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->order('i_record_commodityassess.start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where("i_record_commodityassess.status = 3 AND i_record_commodity.school_id = $recordSchoolInfo[id]")->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	} else if ($statusMarks == 4) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.status = 4 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
    		->field("i_record_commodityassess.uid,i_record_commodityassess.cid,i_record_commodityassess.start_ti,i_record_commodityassess.status as status_c,i_record_commodityassess.start_ti,i_record_commodity.school_id")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->order('i_record_commodityassess.start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where("i_record_commodityassess.status = 4 AND i_record_commodity.school_id = $recordSchoolInfo[id]")->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	} else if ($statusMarks == 5) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.status = 5 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
    		->field("i_record_commodityassess.uid,i_record_commodityassess.cid,i_record_commodityassess.start_ti,i_record_commodityassess.status as status_c,i_record_commodityassess.start_ti,i_record_commodity.school_id")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->order('i_record_commodityassess.start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where("i_record_commodityassess.status = 5 AND i_record_commodity.school_id = $recordSchoolInfo[id]")->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	} else {
    		$resultRecordCommodityassess = $RecordCommodityassess->where("i_record_commodity.school_id = $recordSchoolInfo[id]")
    		->field("i_record_commodityassess.uid,i_record_commodityassess.cid,i_record_commodityassess.start_ti,i_record_commodityassess.status as status_c,i_record_commodityassess.start_ti,i_record_commodity.school_id")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->order('i_record_commodityassess.start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where("i_record_commodity.school_id = $recordSchoolInfo[id]")->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	}
    	$this->assign('resultRecordCommodityassess', $resultRecordCommodityassess);
    	$this->assign('totalrecords', $totalrecords);
    	$totalPages = ceil($totalrecords / $count);
    	$this->assign('totalPages', $totalPages);
    	$this->display();
    }
    
    public function mallcooperation()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','cooperation 管理');
    	$MallCooperation = M("MallCooperation");

    	/**
    	 * delete record
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelId = (int)$_GET['suredel'];
    		$MallCooperation->where("id = $suredelId AND school = $recordSchoolInfo[id]")->delete();
    		
    		/**
    		 * webmaster user operating record
    		 */
    		$SchoolRecord = M("SchoolRecord");
    		$newSchoolRecordData = array(
				'id' => '',
				'sys_id' => '',
				'uid' => $webmaster['uid'],
				'sid' => $recordSchoolInfo['id'],
				'record' => 'mallcooperation deltete, suredel id:'.$suredelId,
				'time' => time()
    		);
    		$SchoolRecord->add($newSchoolRecordData);
    		redirect('/schooladmin/mallcooperation', 1, 'deltete success...');
    	}

    	/**
    	 * post
    	 */
    	if ($this->isPost()) {
    		$id = (int)$_POST['id'];
    		$name = trim(addslashes(htmlspecialchars(strip_tags($_POST['name']))));
    		$url = trim(addslashes(htmlspecialchars(strip_tags($_POST['url']))));
    		$order = (int)$_POST['order'];
    		if (empty($id)) {
    			$newCooperationData = array(
    				'id' => '',
    				'name' => $name,
    				'url' => $url,
    				'order' => $order,
    				'time' => time(),
    				'school' => $recordSchoolInfo['id']
    			);
    			$MallCooperation->add($newCooperationData);
    			
    			/**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
					'id' => '',
					'sys_id' => '',
					'uid' => $webmaster['uid'],
					'sid' => $recordSchoolInfo['id'],
					'record' => 'mallcooperation new add, name:'.$name.' url:'.$url,
					'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    			redirect('/schooladmin/mallcooperation', 1, 'success...new add');
    		} else {
    			$newCooperationData = array(
    				'id' => $id,
    				'name' => $name,
    				'url' => $url,
    				'order' => $order,
    				'time' => time(),
    			);
    			$MallCooperation->save($newCooperationData);
    			
    			/**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
					'id' => '',
					'sys_id' => '',
					'uid' => $webmaster['uid'],
					'sid' => $recordSchoolInfo['id'],
					'record' => 'mallcooperation update, name:'.$name.' url:'.$url,
					'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    			redirect('/schooladmin/mallcooperation', 1, 'success...update');
    		}
    	}
    	$resultsMallCooperation = $MallCooperation->where("school = $recordSchoolInfo[id]")->select();
    	$this->assign('resultsMallCooperation', $resultsMallCooperation);
    	$this->display();
    }
    
    /**
     * activity
     */
    public function activity()
    {
    	$webmaster = logincheck();
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','活动管理');
    	$ActivityItem = M("ActivityItem");
    	
    	/**
    	 * change activity status
    	 */
    	if (!empty($_GET['aid'])) {
    		$aid = (int)$_GET['aid'];
    		$recordActivityItem = $ActivityItem->where("aid = $aid AND school_id = $recordSchoolInfo[id]")->find();
    		if (empty($recordActivityItem['aid'])) {
    			redirect('/schooladmin/activity', 2, 'error aid empty...');
    		}
    		Vendor('Ihelpoo.Email');
    		$emailObj = new Email();
    		$UserLogin = M("UserLogin");
    		$recordUserLogin = $UserLogin->where("uid = $recordActivityItem[sponsor_uid]")->find();
    		if ($_GET['change'] == 'yes') {
    			$updateActivityStatus = array(
    				'aid' => $aid,
    				'status' => 1,
    			);
    			$ActivityItem->save($updateActivityStatus);
    			
    			/**
	             * send system message.
	             */
	            $msgContent = "您组织的活动 “".$recordActivityItem['subject']."” 审核通过了，快来邀请大家参与吧!";
                i_savenotice('10000', $recordActivityItem['sponsor_uid'], 'system/activity:audit:ok', '');
	            
	            /**
	             * send mail
	             */
	            $emailObj->activityVerify($recordUserLogin['email'], $recordUserLogin['nickname'], $msgContent, $aid, $recordActivityItem['subject']);
	            
	            /**
	             * insert into record say
	             */
	            $RecordSay = M("RecordSay");
	            if (empty($recordActivityItem['sid'])) {
		            Vendor('Ihelpoo.Ofunction'); 
		            $ofunction = new Ofunction();
		            $content = "欢迎参加活动 “".$recordActivityItem['subject']."” ".$ofunction->cut_str(strip_tags($recordActivityItem['content']), 100)." <a href='".__ROOT__."/activity/item/".$recordActivityItem['aid']."'>查看活动</a>";
		            $dataRecordSay = array(
	                    'sid' => NULL,
	                    'uid' => $recordUserLogin['uid'],
	                    'say_type' => 0,
	                    'content' => $content,
	                    'authority' => 0,
	                    'time' => time(),
	                    'last_comment_ti' => time(),
	                    'from' => '活动',
	                    'school_id' => $recordSchoolInfo['id']
	                );
	                $sayLastInsertId = $RecordSay->add($dataRecordSay);
	                
	                /**
	                 * update sid
	                 */
	                $updateRecordActivityItemArray = array(
	                	'aid' => $recordActivityItem['aid'],
	                	'sid' => $sayLastInsertId,
	                );
	                $ActivityItem->save($updateRecordActivityItemArray);
	            } else {
	            	$updateRecordSay = array(
	                    'sid' => $recordActivityItem['sid'],
	                    'time' => time(),
	                    'last_comment_ti' => time(),
	            	);
	            	$RecordSay->save($updateRecordSay);
	            }
	            
                /**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
					'id' => '',
					'sys_id' => '',
					'uid' => $webmaster['uid'],
					'sid' => $recordSchoolInfo['id'],
					'record' => '活动通过 activity status success, aid:'.$recordActivityItem['aid'].' uid:'.$recordActivityItem['sponsor_uid'].' subject:'.$recordActivityItem['subject'],
					'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    			redirect('/schooladmin/activity', 2, '通过 change activity status success...');
    		} else if ($_GET['change'] == 'no') {
    			$updateActivityStatus = array(
    				'aid' => $aid,
    				'status' => 2,
    			);
    			$ActivityItem->save($updateActivityStatus);
    			
    			/**
	             * send system message.
	             */
	            $msgContent = "您组织的活动 “".$recordActivityItem['subject']."” 审核未通过，请重新填写，务必合符组织活动规范!";
                i_savenotice('10000', $recordActivityItem['sponsor_uid'], 'system/activity:audit:no', '');
	            
	            /**
	             * send mail
	             */
	            $emailObj->activityVerify($recordUserLogin['email'], $recordUserLogin['nickname'], $msgContent, $aid, $recordActivityItem['subject']);
	            
                /**
    			 * webmaster user operating record
    			 */
    			$SchoolRecord = M("SchoolRecord");
    			$newSchoolRecordData = array(
					'id' => '',
					'sys_id' => '',
					'uid' => $webmaster['uid'],
					'sid' => $recordSchoolInfo['id'],
					'record' => '活动不通过 activity status failed, aid:'.$recordActivityItem['aid'].' uid:'.$recordActivityItem['sponsor_uid'].' subject:'.$recordActivityItem['subject'],
					'time' => time()
    			);
    			$SchoolRecord->add($newSchoolRecordData);
    			redirect('/schooladmin/activity', 2, '不通过 change activity status failed...');
    		}
    	}
    	
    	/**
    	 * lists
    	 */
    	$page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
    	
    	$recordsActivityItem = $ActivityItem->where("school_id = $recordSchoolInfo[id]")->order("time DESC")->limit($offset,$count)->select();
    	$this->assign('recordsActivityItem',$recordsActivityItem);
    	$totalrecords = $ActivityItem->count();
    	$this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
    	$this->display();
    }

    
    /**
     * operating record
     */
    public function operatingrecord()
    {
    	$webmaster = logincheck();
    	$this->assign('title','站长操作记录');
    	$recordSchoolInfo = i_school_domain();
    	$SchoolRecord = M("SchoolRecord");
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
    	$recordsWebmasterUserrecord = $SchoolRecord->where("i_school_record.sid = $recordSchoolInfo[id]")->join('i_user_login ON i_user_login.uid = i_school_record.uid')->join('i_school_webmaster ON i_school_webmaster.uid = i_school_record.uid')->order("time DESC")->limit($offset,$count)->select();
    	$totalrecords = $SchoolRecord->where("sid = $recordSchoolInfo[id]")->count();
    	$this->assign('recordsWebmasterUserrecord',$recordsWebmasterUserrecord);
    	$this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
    	$this->display();
    }

}

?>