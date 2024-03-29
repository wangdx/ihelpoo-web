<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class RooterAction extends Action {

    protected function _initialize() {
    	$adminuserloginid = session('adminuserloginid');
    	if (!empty($adminuserloginid)) {
    		$adminuserloginname = session('adminuserloginname');
    		$this->assign('adminuserloginid',$adminuserloginid);
    		$this->assign('adminuserloginname',$adminuserloginname);
    	}

    	function logincheck()
    	{
    		$adminuserloginid = session('adminuserloginid');
    		if (empty($adminuserloginid)) {
    			redirect('/rooter', 3, '还没有登录呢...');
    		} else {
    			$adminuserloginid = session('adminuserloginid');
    			$adminuserloginname = session('adminuserloginname');
    			return array(
    				'uid' => $adminuserloginid,
    				'name' => $adminuserloginname,
    			);
    		}
    	}
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
    	$this->assign('title','我帮圈圈 管理平台');
    	$adminuserloginid = session('adminuserloginid');
        if ($adminuserloginid) {
        	redirect('/rooter/main', 1, '已经登录...');
        }
    	if ($this->isPost()) {
	    	$AdminUser = M("AdminUser");
	        $validate = array(
	            array('rooter', 'require', 'rooter不能为空'),
	            array('password', 'require', '密码不能为空'),
	            array('cypher', 'require', 'cypher不能为空'),
	        );
	        $AdminUser->setProperty("_validate", $validate);
	        $result = $AdminUser->create();
	        if (!$result) {
	            exit($AdminUser->getError());
	        } else {
	            $rooter = trim(addslashes(htmlspecialchars(strip_tags($_POST["rooter"]))));
	            $password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));
	            $cypher = trim(addslashes(htmlspecialchars(strip_tags($_POST["cypher"]))));
	            $password = md5($password);
	            $cypher = md5($cypher);
	            $recordAdminUser = $AdminUser->where("name = '$rooter'")->find();
	            if (!empty($recordAdminUser['uid'])) {
	            	if ($password != $recordAdminUser['password']) {
                	    redirect('/rooter', 2, 'password wrong...');
                	}
                	$thisyear = getdate();
                    $cypherNum = "help".$thisyear['mon'].$thisyear['mday'];
                    if ($cypher != md5($cypherNum)) {
                	    redirect('/rooter', 2, 'cypher wrong...');
                	}
                	session('adminuserloginid',$recordAdminUser['uid']);
                	session('adminuserloginname',$recordAdminUser['name']);
                	
                	/**
                	 * admin user operating record
                	 */
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
                		'id' => '',
                		'uid' => $recordAdminUser['uid'],
                		'record' => '登录后台',
                		'time' => time(),
                	
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                    redirect('/rooter/main', 3, '登录成功...');
	            }
	        }
    	}
        $this->display();
    }

    public function quit()
    {
    	$this->assign('title','安全退出');
    	$adminuserloginid = session('adminuserloginid');
        session('adminuserloginid', null);
        session('adminuserloginname', null);
        
        /**
         * admin user operating record
         */
        if (!empty($adminuserloginid)) {
	        $AdminUserrecord = M("AdminUserrecord");
	        $newAdminUserrecordData = array(
	        	'id' => '',
	        	'uid' => $adminuserloginid,
	        	'record' => '退出',
	        	'time' => time(),
	        );
	        $AdminUserrecord->add($newAdminUserrecordData);
        }
        $this->display();
    }


    /**
     * system management
     */

    public function main()
    {
    	$admin = logincheck();
    	$this->assign('title','管理中心');
    	$this->display();
    }

    public function orderusericon()
    {
    	$admin = logincheck();
    	$this->assign('title','头像排序 首页用户排序');
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
    				if ($icon_fl > 9) {
    					$icon_fl = 9;
    				}
    				$newUserIconFlArray = array(
    					'uid' => $userLoginRecord['uid'],
    					'icon_fl' => $icon_fl,
    				);
    				$UserLogin->save($newUserIconFlArray);
    				
    				/**
    				 * admin user operating record
    				 */
    				if (!empty($admin['uid'])) {
    					$AdminUserrecord = M("AdminUserrecord");
    					$newAdminUserrecordData = array(
				        	'id' => '',
				        	'uid' => $admin['uid'],
				        	'record' => '修改头像排序 uid:'.$userLoginRecord['uid'].' +icon_fl:'.$icon_fl,
				        	'time' => time(),
    					);
    					$AdminUserrecord->add($newAdminUserrecordData);
    				}
    				
    				$this->ajaxReturn(0,'排序提前('.$icon_fl.')','yes');
    			} else if ($way = 'down') {
    				$icon_fl--;
    				if ($icon_fl < 1) {
    					$icon_fl = 1;
    				}
    				$newUserIconFlArray = array(
    					'uid' => $userLoginRecord['uid'],
    					'icon_fl' => $icon_fl,
    				);
    				$UserLogin->save($newUserIconFlArray);
    				
    				/**
    				 * admin user operating record
    				 */
    				if (!empty($admin['uid'])) {
    					$AdminUserrecord = M("AdminUserrecord");
    					$newAdminUserrecordData = array(
				        	'id' => '',
				        	'uid' => $admin['uid'],
				        	'record' => '修改头像排序 uid:'.$userLoginRecord['uid'].' -icon_fl:'.$icon_fl,
				        	'time' => time(),
    					);
    					$AdminUserrecord->add($newAdminUserrecordData);
    				}
    				
    				$this->ajaxReturn(0,'排序置后('.$icon_fl.')','yes');
    			}
    		}
    	}
    	$userLoginRecords = $UserLogin->order("icon_fl DESC, logintime DESC")->limit($offset,$count)->select();
    	$this->assign('userLoginRecords',$userLoginRecords);
    	$totalusers = $UserLogin->count();
    	$this->assign('totalusers',$totalusers);
    	$totalPages = ceil($totalusers / $count);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }
    
    public function applyverify()
    {
    	$admin = logincheck();
    	$this->assign('title','校园组织、周边商家用户申请');
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
        $UserApplyverify = M("UserApplyverify");
        if (!empty($_GET['undo'])) {
        	$recordUserApplyverify = $UserApplyverify->where("verify_status = 0")->order("i_user_applyverify.time DESC")
	        ->join('i_school_info ON i_user_applyverify.school_id = i_school_info.id')->limit($offset,$count)->select();
			$totalrecords = $UserApplyverify->count();
        } else {
	        $recordUserApplyverify = $UserApplyverify->order("i_user_applyverify.time DESC")
	        ->join('i_school_info ON i_user_applyverify.school_id = i_school_info.id')->limit($offset,$count)->select();
			$totalrecords = $UserApplyverify->count();
        }
    	$this->assign('recordUserApplyverify', $recordUserApplyverify);
    	$this->assign('totalrecords', $totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages', $totalPages);
    	$this->display();
    }

    public function indexbgimg()
    {
    	$this->assign('title','上传图片');
    	$admin = logincheck();
    	Vendor('Ihelpoo.Upyun');
        $upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
        $imageStorageUrl = image_storage_url();
        $SchoolAlbum = M("SchoolAlbum");
    	if ($this->isPost()) {
    		$schoolid = $_POST['schoolid'];
    		if (!empty($_FILES)) {
    			if ($_FILES["uploadimage"]["error"] > 0) {
    				redirect('/rooter/indexbgimg', 3, 'file error...'.$_FILES["uploadimage"]["error"]);
    			} else {
    				$imageOldName = $_FILES["uploadimage"]["name"];
    				$imageType = $_FILES["uploadimage"]["type"];
    				$imageSize = $_FILES["uploadimage"]["size"];
    				$imageTmpName = $_FILES["uploadimage"]["tmp_name"];
    				$tempRealSize = getimagesize($imageTmpName);
    				$imageRealWidth = $tempRealSize['0'];
    				$imageRealHeight = $tempRealSize['1'];
    			}

    			/**
    			 * $tempRealSize = getimagesize($_FILES["uploadedimg"]["tmp_name"]);
    			 * $logoRealWidth = $tempRealSize['0'];
    			 * $logoRealHeight = $tempRealSize['1'];
    			 */
    			if ($imageSize > 800000) {
    				redirect('/rooter/indexbgimg', 3, 'error...上传图片太大, 最大能上传单张 3.5MB');
    			} else if ($imageType == 'image/jpeg') {
    				
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
        			 * admin user operating record
        			 */
        			if (!empty($admin['uid'])) {
        				$AdminUserrecord = M("AdminUserrecord");
        				$newAdminUserrecordData = array(
							'id' => '',
							'uid' => $admin['uid'],
							'record' => '上传图片'.$newfilepath,
							'time' => time(),
        				);
        				$AdminUserrecord->add($newAdminUserrecordData);
        			}
        			redirect('/rooter/indexbgimg', 1, 'success...');
    			} else {
    				redirect('/rooter/indexbgimg', 3, 'error...上传图片格式错误, 目前仅支持.jpg');
    			}
    		}
    	}
    	
    	/**
    	 * delete image
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		if (!empty($suredelid)) {
    			$deleteSchoolAlbum = $SchoolAlbum->where("id = $suredelid")->find();
    			if (!empty($deleteSchoolAlbum['id'])) {
    				
	    			/**
        			 * admin user operating record
        			 */
        			if (!empty($admin['uid'])) {
        				$AdminUserrecord = M("AdminUserrecord");
        				$newAdminUserrecordData = array(
							'id' => '',
							'uid' => $admin['uid'],
							'record' => '删除图片 size:'.$deleteSchoolAlbum['size'].' id:'.$suredelid,
							'time' => time(),
        				);
        				$AdminUserrecord->add($newAdminUserrecordData);
        			}
	    			
	    			$urlFilename = str_ireplace("$imageStorageUrl", "", $deleteSchoolAlbum['url']);
    				$isStorageDeleteFlag = $upyun->delete($urlFilename);
    				if ($isStorageDeleteFlag) {
    					$SchoolAlbum->where("id = $suredelid")->delete();
    					redirect('/rooter/indexbgimg', 1, '删除图片成功 ok...');
    				}
    			}
    		}
    	}
    	
    	$SchoolInfo = M("SchoolInfo");
		$recordSchoolInfo = $SchoolInfo->select();
		$this->assign('recordSchoolInfo',$recordSchoolInfo);
		
		$schoolid = (int)$_GET['schoolid'];
    	$this->assign('schoolid',$schoolid);
    	if (!empty($schoolid)) {
			$page = i_page_get_num();
			$count = 10;
			$offset = $page * $count;
			$recordSchoolAlbum = $SchoolAlbum->where("school_id = $schoolid")->order("time DESC")->limit($offset, $count)->select();
			$this->assign('recordSchoolAlbum',$recordSchoolAlbum);
			$totalRecordNums = $SchoolAlbum->where("school_id = $schoolid")->count();
			$this->assign('totalRecordNums', $totalRecordNums);
			$totalPages = ceil($totalRecordNums / $count);
			$this->assign('totalPages', $totalPages);
    	}
    	$this->display();
    }
    
    
    /**
     * school management
     */
    public function schoolapplyverify()
    {
    	$admin = logincheck();
    	$this->assign('title','申请开通学校');
    	
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
        $SchoolApplyverify = M("SchoolApplyverify");
        
        if (!empty($_GET['statuschange'])) {
        	$statuschangeid = (int)$_GET['statuschange'];
        	$recordSchoolApplyverify = $SchoolApplyverify->find($statuschangeid);
        	if (!empty($recordSchoolApplyverify['id'])) {
        		$updateStatus = array(
        			'id' => $recordSchoolApplyverify['id'],
        			'verify_status' => '2',
        		);
        		$SchoolApplyverify->save($updateStatus);
        		
        		/**
        		 * admin user operating record
        		 */
        		if (!empty($admin['uid'])) {
        			$AdminUserrecord = M("AdminUserrecord");
        			$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '标记为处理 schoolapply id:'.$recordSchoolApplyverify['id'],
						'time' => time(),
        			);
        			$AdminUserrecord->add($newAdminUserrecordData);
        		}
        		
        		//TODO
        		/**
        		 * if !empty uid send system msg to him, told wo have read this, well connect him soon
        		 */
        		redirect('/rooter/schoolapplyverify', 1, 'update status type ok...');
        	} else if (!empty($recordSchoolApplyverify['id']) && ($recordSchoolApplyverify['verify_status'] == '2' || $recordSchoolApplyverify['verify_status'] == '1')) {
        		$updateStatus = array(
        			'id' => $recordSchoolApplyverify['id'],
        			'verify_status' => '0',
        		);
        		$SchoolApplyverify->save($updateStatus);
        		
        		/**
        		 * admin user operating record
        		 */
        		if (!empty($admin['uid'])) {
        			$AdminUserrecord = M("AdminUserrecord");
        			$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '标记为未处理 schoolapply id:'.$recordSchoolApplyverify['id'],
						'time' => time(),
        			);
        			$AdminUserrecord->add($newAdminUserrecordData);
        		}
        		redirect('/rooter/schoolapplyverify', 1, 'update status type ok...');
        	}
        }
        
        if (!empty($_GET['detail'])) {
        	$detailid = $_GET['detail'];
        	$detailSchoolApplyverify = $SchoolApplyverify->find($detailid);
        	$this->assign('detailSchoolApplyverify', $detailSchoolApplyverify);
        } else {
        	$recordSchoolApplyverify = $SchoolApplyverify->order("time DESC")->limit($offset,$count)->select();
        	$this->assign('recordSchoolApplyverify', $recordSchoolApplyverify);
        	$totalrecords = $SchoolApplyverify->count();
        	$this->assign('totalrecords', $totalrecords);
        	$totalPages = ceil($totalrecords / $count);
        	$this->assign('totalPages', $totalPages);
        }
    	$this->display();
    }
    
    public function schoolinfo()
    {
    	$admin = logincheck();
    	$this->assign('title','开通学校');
    	$SchoolInfo = M("SchoolInfo");
    	
    	if (!empty($_POST['provinceAjax'])) {
            $selectProvinceId = (int)$_POST['provinceAjax'];
            $OpCity = M("OpCity");
            $selectCityObj = $OpCity->where("prov_id = $selectProvinceId")->select();
            echo '<select id="city" name="city">';
            foreach ($selectCityObj as $selectCity) {
                echo "<option value='$selectCity[id]'>$selectCity[name]</option>";
            }
            echo "</select>";
            exit();
        }
    	
    	if ($this->isPost()) {
    		$id = (int)$_POST['id'];
    		$school = $_POST['school'];
    		$initial = $_POST['initial'];
    		$city = $_POST['city'];
    		$domain = $_POST['domain'];
    		$domain_main = $_POST['domain_main'];
    		$remark = $_POST['remark'];
    		$status = (int)$_POST['status'];
    		if ($status > 5) {
    			$status = 5;
    		}
    		if (!empty($id)) {
    			
    			/**
    			 * update
    			 */
    			$updateSchoolData = array(
    				'id' => $id,
    				'school' => $school,
    				'initial' => $initial,
    				'city_op' => $city,
    				'domain' => $domain,
    				'domain_main' => $domain_main,
    				'remark' => $remark,
    				'status' => $status,
    			);
    			$SchoolInfo->save($updateSchoolData);
    			
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '更新学校信息 学校:'.$school.'-主域名:'.$domain_main.'-次域名:'.$domain.'-备注:'.$remark.'-状态:'.$status,
					    'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			redirect('/rooter/schoolinfo', 1, '更新学校信息成功 ok...');
    		} else {
    			
    			/**
    			 * insert
    			 */
    			$newSchoolData = array(
    				'id' => '',
    				'school' => $school,
    				'initial' => $initial,
    				'city_op' => $city,
    				'domain' => $domain,
    				'domain_main' => $domain_main,
    				'remark' => $remark,
    				'time' => time(),
    				'status' => 1,
    			);
    			$newSchoolId = $SchoolInfo->add($newSchoolData);
    			
    			$WebStatus = M("WebStatus");
    			$newWebStatuslData = array(
    				'sid' => $newSchoolId,
    				'time' => time(),
    			);
    			$WebStatus->add($newWebStatuslData);
    			
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '添加新学校信息 学校:'.$school.'-主域名:'.$domain_main.'-次域名:'.$domain.'-备注:'.$remark,
					    'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			redirect('/rooter/schoolinfo', 1, '添加新学校信息成功 ok...');
    		}
    	}
    	
    	$page = i_page_get_num();
        $count = 10;
        $offset = $page * $count;
        $recordSchoolInfo = $SchoolInfo->order("status DESC,id ASC")->limit($offset,$count)->select();
        $this->assign('recordSchoolInfo', $recordSchoolInfo);

        /**
         * page link
         */
        $totalReocrdNums = $SchoolInfo->count();
        $this->assign('totalRecordNums', $totalReocrdNums);
        $totalPages = ceil($totalReocrdNums / $count);
        $this->assign('totalPages', $totalPages);
        
        
        /**
         * province
         */
        $OpProvince = M("OpProvince");
        $listOpProvince = $OpProvince->select();
        $OpCity = M("OpCity");
        $listOpCity = $OpCity->select();
        $this->assign('listopprovince', $listOpProvince);
        $this->assign('listopcity', $listOpCity);
    	
    	$this->display();
    }
    
    public function schoolsystem()
    {
    	$admin = logincheck();
    	$this->assign('title','学校配置');
    	$SchoolSystem = M("SchoolSystem");
    	$schoolid = (int)$_GET['schoolid'];
    	$this->assign('schoolid',$schoolid);
    	
    	if ($this->isPost()) {
    		$sid = (int)$_POST['sid'];
    		$index_user = $_POST['index_user'];
    		$index_spread_info = $_POST['index_spread_info'];
    		$about = $_POST['about'];
    		$image_index = $_POST['image_index'];
    		$image_mobile = $_POST['image_mobile'];
    		
    		if (!empty($sid) && !empty($index_user) && !empty($image_index) && !empty($image_mobile)) {
    			
    			/**
    			 * insert
    			 */
    			$newSchoolData = array(
	    			'id' => '',
	    			'sid' => $sid,
	    			'index_user' => $index_user,
	    			'index_spread_info' => $index_spread_info,
	    			'about' => $about,
	    			'image_index' => $image_index,
	    			'image_mobile' => $image_mobile,
	    			'time' => time()
    			);
    			$SchoolSystem->add($newSchoolData);
    			 
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '更新学校配置信息 i_school_system 学校id:'.$sid.'-index_user:'.$index_user.'-index_spread_info:'.$index_spread_info.'-about:'.$about.'-image_index:'.$image_index.'-image_mobile:'.$image_mobile,
					    'time' => time()
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			redirect('/rooter/schoolsystem', 1, '更新学校配置信息成功 ok...');
    		}
    	}
    	
		$SchoolInfo = M("SchoolInfo");
		$recordSchoolInfo = $SchoolInfo->select();
		$this->assign('recordSchoolInfo',$recordSchoolInfo);
		if (!empty($schoolid)) {
    		$resultSchoolSystem = $SchoolSystem->where("i_school_system.sid = $schoolid")->join('i_school_info ON i_school_info.id = i_school_system.sid')->order("i_school_system.time DESC")->find();
    		$this->assign('resultSchoolSystem',$resultSchoolSystem);
    		
    		$page = i_page_get_num();
    		$count = 20;
    		$offset = $page * $count;
    		$recordSchoolSystem = $SchoolSystem->where("i_school_system.sid = $schoolid")->join('i_school_info ON i_school_info.id = i_school_system.sid')
    		->field('i_school_info.id,i_school_info.school,i_school_system.sid,i_school_system.total_users,i_school_system.index_user,i_school_system.about,i_school_system.image_index,i_school_system.image_mobile,i_school_system.time')
    		->order("i_school_system.time DESC")
    		->limit($offset,$count)->select();
    		$this->assign('recordSchoolSystem', $recordSchoolSystem);
    		
    		/**
    		 * page link
    		 */
    		$totalReocrdNums = $SchoolSystem->where("sid = $schoolid")->count();
    		$this->assign('totalRecordNums', $totalReocrdNums);
    		$totalPages = ceil($totalReocrdNums / $count);
    		$this->assign('totalPages', $totalPages);
    	}
    	
    	$this->display();
    }

    public function schoolwebmaster()
    {
    	$admin = logincheck();
    	$this->assign('title','站长管理');
    	$SchoolWebmaster = M("SchoolWebmaster");
    	
    	if ($this->isPost()) {
    		$uid = (int)$_POST['uid'];
    		$sid = (int)$_POST['sid'];
    		$description = $_POST['description'];
    		$year = $_POST['year'];
    		$position = $_POST['position'];
    		$star = $_POST['star'];
    		if (!empty($uid) && !empty($sid)) {
    			/**
    			 * insert
    			 */
    			$newSchoolWebmasterData = array(
    				'id' => '',
    				'uid' => $uid,
    				'sid' => $sid,
    				'description' => $description,
    				'year' => $year,
    				'position' => $position,
    				'star' => $star
    			);
    			$SchoolWebmaster->add($newSchoolWebmasterData);
    			
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '添加站长 schoolid:'.$sid.'-userid:'.$uid,
					    'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			redirect('/rooter/schoolwebmaster', 1, '添加站长信息成功 ok...');
    		}
    	}
    	
    	/**
    	 * delete webmaster
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		if (!empty($suredelid)) {
    			$deleteSchoolWebmaster = $SchoolWebmaster->where("id = $suredelid")->find();
    			
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '删除站长 schoolid:'.$deleteSchoolWebmaster['sid'].'-userid:'.$deleteSchoolWebmaster['uid'],
					    'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			
    			$SchoolWebmaster->where("id = $suredelid")->delete();
    			redirect('/rooter/schoolwebmaster', 1, '删除站长成功 ok...');
    		}
    	}
    	
    	$schoolid = (int)$_GET['schoolid'];
    	$this->assign('schoolid',$schoolid);
    	$page = i_page_get_num();
	    $count = 15;
	    $offset = $page * $count;
    	if (!empty($schoolid)) {
	        $recordSchoolWebmaster = $SchoolWebmaster->where("sid = $schoolid")
	        ->join('i_school_info ON i_school_info.id = i_school_webmaster.sid')
	        ->join('i_user_login ON i_user_login.uid = i_school_webmaster.uid')
    		->field('i_school_info.school,i_user_login.nickname,i_school_webmaster.id,i_school_webmaster.uid,i_school_webmaster.sid,i_school_webmaster.description,i_school_webmaster.year,i_school_webmaster.position,i_school_webmaster.star')
	        ->order("id DESC")->limit($offset,$count)->select();
	
	        /**
	         * page link
	         */
	        $totalReocrdNums = $SchoolWebmaster->where("sid = $schoolid")->count();
    	} else {
    		$recordSchoolWebmaster = $SchoolWebmaster->join('i_school_info ON i_school_info.id = i_school_webmaster.sid')
	        ->join('i_user_login ON i_user_login.uid = i_school_webmaster.uid')
    		->field('i_school_info.school,i_user_login.nickname,i_school_webmaster.id,i_school_webmaster.uid,i_school_webmaster.sid,i_school_webmaster.description,i_school_webmaster.year,i_school_webmaster.position,i_school_webmaster.star')
    		->order("id DESC")->limit($offset,$count)->select();
	
	        /**
	         * page link
	         */
	        $totalReocrdNums = $SchoolWebmaster->count();
    	}
    	$this->assign('recordSchoolWebmaster', $recordSchoolWebmaster);
    	$this->assign('totalRecordNums', $totalReocrdNums);
	    $totalPages = ceil($totalReocrdNums / $count);
	    $this->assign('totalPages', $totalPages);
    	
	    $SchoolInfo = M("SchoolInfo");
		$recordSchoolInfo = $SchoolInfo->select();
		$this->assign('recordSchoolInfo',$recordSchoolInfo);
	    
    	$this->display();
    }

    public function schoolopacademy()
    {
    	$admin = logincheck();
    	$this->assign('title','学院管理');
    	$OpAcademy = M("OpAcademy");
    	$schoolid = (int)$_GET['schoolid'];
    	$this->assign('schoolid',$schoolid);
    	
    	if ($this->isPost()) {
    		$academyid = (int)$_POST['academyid'];
    		$schoolpostid = (int)$_POST['sid'];
    		$name = $_POST['name'];
    		if (!empty($name) && !empty($schoolpostid)) {
	    		if (empty($academyid)) {
	    			
		    		$newOpAcademy = array(
		    			'name' => $name,
		    			'school' => $schoolpostid,
		    		);
		    		$OpAcademy->add($newOpAcademy);
		    		
		    		/**
		    		 * admin user operating record
		    		 */
	    			if (!empty($admin['uid'])) {
	    				$AdminUserrecord = M("AdminUserrecord");
	    				$newAdminUserrecordData = array(
						    'id' => '',
						    'uid' => $admin['uid'],
						    'record' => '添加学院 schoolid:'.$schoolpostid.'-学院:'.$name,
						    'time' => time(),
	    				);
	    				$AdminUserrecord->add($newAdminUserrecordData);
	    			}
		    		redirect('/rooter/schoolopacademy', 1, '添加学院成功 ok...');
	    		} else {
	    			$updateOpAcademy = array(
		    			'id' => $academyid,
		    			'name' => $name,
		    			'school' => $schoolid,
		    		);
		    		$OpAcademy->save($updateOpAcademy);
		    		
		    		/**
		    		 * admin user operating record
		    		 */
	    			if (!empty($admin['uid'])) {
	    				$AdminUserrecord = M("AdminUserrecord");
	    				$newAdminUserrecordData = array(
						    'id' => '',
						    'uid' => $admin['uid'],
						    'record' => '更新学院 schoolid:'.$schoolid.'-学院:'.$name,
						    'time' => time(),
	    				);
	    				$AdminUserrecord->add($newAdminUserrecordData);
	    			}
		    		redirect('/rooter/schoolopacademy', 1, '更新学院成功 ok...');
	    		}
    		}
    	}
    	
    	/**
    	 * delete academy
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		if (!empty($suredelid)) {
    			$deleteOpAcademy = $OpAcademy->where("id = $suredelid")->find();
    			
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '删除学院 schoolid:'.$deleteOpAcademy['school'].'-name:'.$deleteOpAcademy['name'],
					    'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			
    			$OpAcademy->where("id = $suredelid")->delete();
    			redirect('/rooter/schoolopacademy', 1, '删除学院成功 ok...');
    		}
    	}
    	
    	$SchoolInfo = M("SchoolInfo");
		$recordSchoolInfo = $SchoolInfo->select();
		$this->assign('recordSchoolInfo',$recordSchoolInfo);
		
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

    public function schoolopspecialty()
    {
    	$admin = logincheck();
    	$this->assign('title','专业管理');
    	$OpSpecialty = M("OpSpecialty");
    	$schoolid = (int)$_GET['schoolid'];
    	$this->assign('schoolid',$schoolid);
    	$academyid = (int)$_GET['academyid'];
    	$this->assign('academyid',$academyid);
    	
    	if ($this->isPost()) {
    		$specialtyid = (int)$_POST['specialtyid'];
    		$schoolpostid = (int)$_POST['sid'];
    		$academypostid = (int)$_POST['acedemy'];
    		$name = $_POST['name'];
    		if (!empty($name) && !empty($schoolpostid) && !empty($academypostid)) {
	    		if (empty($specialtyid)) {
	    			
		    		$newOpSpecialty = array(
		    			'name' => $name,
		    			'academy' => $academypostid,
		    			'school' => $schoolpostid
		    		);
		    		$OpSpecialty->add($newOpSpecialty);
		    		
		    		/**
		    		 * admin user operating record
		    		 */
	    			if (!empty($admin['uid'])) {
	    				$AdminUserrecord = M("AdminUserrecord");
	    				$newAdminUserrecordData = array(
						    'id' => '',
						    'uid' => $admin['uid'],
						    'record' => '添加专业 schoolid:'.$schoolpostid.'-专业:'.$name,
						    'time' => time(),
	    				);
	    				$AdminUserrecord->add($newAdminUserrecordData);
	    			}
		    		redirect('/rooter/schoolopspecialty', 1, '添加专业成功 ok...');
	    		} else {
	    			$updateOpSpecialty = array(
		    			'id' => $specialtyid,
		    			'name' => $name,
	    				'academy' => $academypostid,
		    			'school' => $schoolid
		    		);
		    		$OpSpecialty->save($updateOpSpecialty);
		    		
		    		/**
		    		 * admin user operating record
		    		 */
	    			if (!empty($admin['uid'])) {
	    				$AdminUserrecord = M("AdminUserrecord");
	    				$newAdminUserrecordData = array(
						    'id' => '',
						    'uid' => $admin['uid'],
						    'record' => '更新专业 schoolid:'.$schoolid.'-专业:'.$name,
						    'time' => time(),
	    				);
	    				$AdminUserrecord->add($newAdminUserrecordData);
	    			}
		    		redirect('/rooter/schoolopspecialty', 1, '更新专业成功 ok...');
	    		}
    		}
    	}
    	
    	/**
    	 * delete academy
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		if (!empty($suredelid)) {
    			$deleteOpSpecialty = $OpSpecialty->where("id = $suredelid")->find();
    			
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '删除专业 schoolid:'.$deleteOpSpecialty['school'].'-name:'.$deleteOpSpecialty['name'],
					    'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			
    			$OpSpecialty->where("id = $suredelid")->delete();
    			redirect('/rooter/schoolopspecialty', 1, '删除专业成功 ok...');
    		}
    	}
    	
    	$SchoolInfo = M("SchoolInfo");
		$recordSchoolInfo = $SchoolInfo->select();
		$this->assign('recordSchoolInfo', $recordSchoolInfo);
		
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

    public function schoolopdormitory()
    {
    	$admin = logincheck();
    	$this->assign('title','寝室管理');
    	$OpDormitory = M("OpDormitory");
    	$schoolid = (int)$_GET['schoolid'];
    	$this->assign('schoolid',$schoolid);
    	
    	if ($this->isPost()) {
    		$dormitoryid = (int)$_POST['dormitoryid'];
    		$schoolpostid = (int)$_POST['sid'];
    		$name = $_POST['name'];
    		$type = $_POST['type'];
    		if (!empty($name) && !empty($schoolpostid) && !empty($type)) {
	    		if (empty($dormitoryid)) {
		    		$newOpDormitory = array(
		    			'name' => $name,
		    			'type' => $type,
		    			'school' => $schoolpostid
		    		);
		    		$OpDormitory->add($newOpDormitory);
		    		
		    		/**
		    		 * admin user operating record
		    		 */
	    			if (!empty($admin['uid'])) {
	    				$AdminUserrecord = M("AdminUserrecord");
	    				$newAdminUserrecordData = array(
						    'id' => '',
						    'uid' => $admin['uid'],
						    'record' => '添加寝室 schoolid:'.$schoolpostid.'-寝室:'.$name,
						    'time' => time(),
	    				);
	    				$AdminUserrecord->add($newAdminUserrecordData);
	    			}
		    		redirect('/rooter/schoolopdormitory', 1, '添加寝室成功 ok...');
	    		} else {
	    			$updateOpDormitory = array(
		    			'id' => $schoolpostid,
		    			'name' => $name,
		    			'type' => $type,
		    			'school' => $schoolid
		    		);
		    		$OpDormitory->save($updateOpDormitory);
		    		
		    		/**
		    		 * admin user operating record
		    		 */
	    			if (!empty($admin['uid'])) {
	    				$AdminUserrecord = M("AdminUserrecord");
	    				$newAdminUserrecordData = array(
						    'id' => '',
						    'uid' => $admin['uid'],
						    'record' => '更新寝室 schoolid:'.$schoolid.'-寝室:'.$name,
						    'time' => time(),
	    				);
	    				$AdminUserrecord->add($newAdminUserrecordData);
	    			}
		    		redirect('/rooter/schoolopdormitory', 1, '更新寝室成功 ok...');
	    		}
    		}
    	}
    	
    	/**
    	 * delete academy
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		if (!empty($suredelid)) {
    			$deleteOpDormitory = $OpDormitory->where("id = $suredelid")->find();
    			
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '删除寝室 schoolid:'.$deleteOpDormitory['school'].'-name:'.$deleteOpDormitory['name'],
					    'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			
    			$OpDormitory->where("id = $suredelid")->delete();
    			redirect('/rooter/schoolopdormitory', 1, '删除寝室成功 ok...');
    		}
    	}
    	
    	$SchoolInfo = M("SchoolInfo");
		$recordSchoolInfo = $SchoolInfo->select();
		$this->assign('recordSchoolInfo',$recordSchoolInfo);
		
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

    public function schoolrecord()
    {
    	$admin = logincheck();
    	$this->assign('title','校园管理记录');
    	$SchoolRecord = M("SchoolRecord");
    	$page = i_page_get_num();
        $count = 30;
        $offset = $page * $count;
        $schoolid = (int)$_GET['schoolid'];
    	if (!empty($schoolid)) {
	        $recordsWebmasterUserrecord = $SchoolRecord->where("i_school_record.sid = $schoolid")->join('i_user_login ON i_user_login.uid = i_school_record.uid')
	        ->join('i_school_webmaster ON i_school_webmaster.uid = i_school_record.uid')
	        ->join('i_school_info ON i_school_webmaster.sid = i_school_info.id')
	        ->field('i_school_record.sid,i_school_info.school,i_user_login.nickname,i_user_login.uid,i_school_webmaster.position,i_school_record.record,i_school_record.sys_id,i_school_record.time')
	        ->order("i_school_record.time DESC")->limit($offset,$count)->select();
	        $totalrecords = $SchoolRecord->where("sid = $schoolid")->count();
    	} else {
    		$recordsWebmasterUserrecord = $SchoolRecord->join('i_user_login ON i_user_login.uid = i_school_record.uid')
	        ->join('i_school_webmaster ON i_school_webmaster.uid = i_school_record.uid')
	        ->join('i_school_info ON i_school_webmaster.sid = i_school_info.id')
	        ->field('i_school_info.school,i_user_login.nickname,i_user_login.uid,i_school_webmaster.position,i_school_record.record,i_school_record.sys_id,i_school_record.time')
	        ->order("i_school_record.time DESC")->limit($offset,$count)->select();
	        $totalrecords = $SchoolRecord->count();
    	}
    	$this->assign('recordsWebmasterUserrecord',$recordsWebmasterUserrecord);
    	$this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
        $SchoolInfo = M("SchoolInfo");
		$recordSchoolInfo = $SchoolInfo->select();
		$this->assign('recordSchoolInfo',$recordSchoolInfo);
    	$this->display();
    }
    
    /**
     * user management
     */

    public function user()
    {
        $admin = logincheck();
    	$this->assign('title', '用户管理');
        $UserLogin = M("UserLogin");
        $userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));

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
        	 * admin user operating record
        	 */
        	if (!empty($admin['uid'])) {
        		$AdminUserrecord = M("AdminUserrecord");
        		$newAdminUserrecordData = array(
				    'id' => '',
				    'uid' => $admin['uid'],
				    'record' => 'user search 搜索 searchWords:'.$searchWords,
				    'time' => time(),
        		);
        		$AdminUserrecord->add($newAdminUserrecordData);
        	}
        	
        	if (!empty($userLoginRecord['uid'])) {
        		redirect('/rooter/user/'.$userLoginRecord['uid'], 0, 'ok...');
        	} else {
        		redirect('/rooter/user?uid=empty', 0, 'empty...');
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
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => 'user reset pw 重置密码. uid:'.$isUserExist['uid'].' password:'.md5($_POST['password']),
					    'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
                
                redirect('/rooter/user', 1, 'update user password ok...');
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
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => 'change user record limit. uid:'.$isUserExist['uid'].' record_limit:'.$newRecordLimit,
					    'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
                
                redirect('/rooter/user', 1, 'update user record limit ok...');
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
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => 'change user type. uid:'.$isUserExist['uid'].' type:'.$newUserType,
					    'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
                
                redirect('/rooter/user', 1, 'update user type ok...');
            }
        }
        
        if (!empty($_POST['school']) && !empty($_POST['uid'])) {
        	$newUserSchool = (int)$_POST['school'];
			$isUserExist = $UserLogin->find($_POST['uid']);
            if ($isUserExist['uid']) {
            	$setUserType = array(
            		'uid' => $isUserExist['uid'],
            	    'school' => $newUserSchool,
            	);
                $UserLogin->save($setUserType);
                
            	/**
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => 'change user school. uid:'.$isUserExist['uid'].' school_id:'.$newUserSchool,
					    'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
                
                redirect('/rooter/user', 1, 'update user type ok...');
            }
        }

        if (!empty($userId)) {
        	$recordUserLogin = $UserLogin->find($userId);
        	$recordUserLogin['logintime'] = date("Y-m-d H:i:s", $recordUserLogin['logintime']);
        	$recordUserLogin['lastlogintime'] = date("Y-m-d H:i:s", $recordUserLogin['lastlogintime']);
        	$recordUserLogin['creat_ti'] = date("Y-m-d H:i:s", $recordUserLogin['creat_ti']);
        	$this->assign('recordUserLogin',$recordUserLogin);

        	$UserInfo = M("UserInfo");
        	$recordUserInfo = $UserInfo->find($userId);
        	$this->assign('recordUserInfo',$recordUserInfo);

        	$UserStatus = M("UserStatus");
        	$recordUserStatus = $UserStatus->find($userId);
        	$this->assign('recordUserStatus',$recordUserStatus);

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
        		'userAlbumNums' => $userAlbumNums,
        		'userAlbumSize' => round($userAlbumSize/(1024*1024),2)."MB",
        		'userMsgCommentNums' => $userMsgCommentNums,
        		'userMsgActiveNums' => $userMsgActiveNums,
        		'userMsgAtNums' => $userMsgAtNums,
        		'userTalkNums' => $userTalkNums,
        	);
        	$this->assign('userOtherInfo',$userOtherInfo);

        	/**
        	 * user shop
        	 */
        	$UserShop = M("UserShop");
        	$recordUserShop = $UserShop->find($userId);
        	if (!empty($recordUserShop['uid'])) {
        		$this->assign('recordUserShop',$recordUserShop);
        	}
        }
        $this->display();
    }

    public function realnameallowmf()
    {
        $admin = logincheck();
    	$this->assign('title', '用户管理');
        $AdminRealnamemf = M("AdminRealnamemf");
        $page = i_page_get_num();
        $count = 20;
        $offset = $page * $count;
        $recordAdminRealnamemf = $AdminRealnamemf->order("time DESC")->limit($offset,$count)->select();
        $this->assign('adminRealnamemf',$recordAdminRealnamemf);

        /**
         * page link
         */
        $totalReocrdNums = $AdminRealnamemf->count();
        $this->assign('totalRecordNums', $totalReocrdNums);
        $totalPages = ceil($totalReocrdNums / $count);
        $this->assign('totalPages', $totalPages);

        /**
         * post
         */
        if (!empty($_GET['sendmail'])) {
            $uid = $_GET['sendmail'];
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
                i_savenotice('10000', $uid, 'setting/realfirst', '');

                /**
                 * update i_admin_realnalemf.allow
                 */
                $recordAdminRealnamemf = $AdminRealnamemf->where("uid = $uid AND allow != '1'")->find();
                $dataAf = array(
                	'id' => $recordAdminRealnamemf['id'],
                    'allow' => 1
                );
                $isUpdateAdminRealnamemf = $AdminRealnamemf->save($dataAf);

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
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => 'realnameallowmf 允许修改真实姓名. uid:'.$uid.' realname_re:0',
					    'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
                
                if ($isUpdateAdminRealnamemf && $isUpdateUserInfo) {
                	redirect('/rooter/realnameallowmf', 3, 'update user modify ok...');
                } else {
                	redirect('/rooter/realnameallowmf', 3, 'update info... isUpdateAdminRealnamemf:'.$isUpdateAdminRealnamemf.'; isUpdateUserInfo:'.$isUpdateUserInfo);
                }
            }
        }
        $this->display();
    }

    public function userhonor()
    {
        $admin = logincheck();
    	$this->assign('title', '授予荣誉奖励活跃');
        if (!empty($_POST['get_user_level'])) {
        	$UserLogin = M("UserLogin");
            $userAll = $UserLogin->select();
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
             * admin user operating record
             */
            if (!empty($admin['uid'])) {
            	$AdminUserrecord = M("AdminUserrecord");
            	$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => 'userhonor get_user_level 查询. level:'.$_POST['get_user_level'].' '.$i.'人',
					'time' => time(),
            	);
            	$AdminUserrecord->add($newAdminUserrecordData);
            }
        }

        if (!empty($_POST['get_user_info'])) {
        	$UserLogin = M("UserLogin");
        	$userArray = explode(";", $_POST['get_user_info']);
        	$userString = NULL;
        	foreach ($userArray as $user) {
        	    $userString .= $user.",";
        	}
        	$userString = substr($userString, 0, -1);

        	$userList = $UserLogin->where("i_user_login.uid in (".$userString.")")
        	->join('i_user_info ON i_user_info.uid = i_user_login.uid')
        	->join('i_op_academy ON i_user_info.academy_op = i_op_academy.id')
        	->join('i_op_specialty ON i_user_info.specialty_op = i_op_specialty.id')
        	->join('i_op_dormitory ON i_user_info.dormitory_op = i_op_dormitory.id')
        	->join('i_op_city ON i_user_info.city_op = i_op_city.id')
        	->field('i_user_login.uid,email,nickname,sex,birthday,enteryear,ip,logintime,realname,mobile,qq,weibo,i_op_academy.name as nameacademy,i_op_specialty.name as namespecialty,i_op_dormitory.name as namedormitory,i_op_city.name as namecity')
        	->select();
        	$this->assign('userList', $userList);
        	
        	/**
        	 * admin user operating record
        	 */
        	if (!empty($admin['uid'])) {
        		$AdminUserrecord = M("AdminUserrecord");
        		$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => 'userhonor get_user_info 查询用户详细. userString:'.$userString,
					'time' => time(),
        		);
        		$AdminUserrecord->add($newAdminUserrecordData);
        	}
        }

        if (!empty($_POST['honor_content'])) {
            $UserHonor = M("UserHonor");
            $userArray = explode(";", $_POST['user_ids']);

            /**
             * message to owner
        	 */
            $msgSystemType = 'helprooter/userhonor';
            $i = 0;
            foreach ($userArray as $user) {
                $data = array(
                    'id' => '',
                    'uid' => $user,
                    'content' => $_POST['honor_content'],
                    'time' => time()
                );
                $UserHonor->add($data);

                /**
                 * insert into system message
                 * "你获得了我帮圈圈荣誉，快来看看吧";
                 */
                i_savenotice('10000', $user, $msgSystemType, $user);
        	    $i++;
            }
            
        	/**
        	 * admin user operating record
        	 */
        	if (!empty($admin['uid'])) {
        		$AdminUserrecord = M("AdminUserrecord");
        		$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '授予我帮圈圈荣誉: 共'.$i.'人, content'.$_POST['honor_content'],
					'time' => time(),
        		);
        		$AdminUserrecord->add($newAdminUserrecordData);
        	}
        	
            redirect('/rooter/userhonor', 3, '授予荣誉成功 共'.$i.'人...');
        }
        
        if (!empty($_POST['active_nums']) && !empty($_POST['active_reason'])) {
        	$activeNums = $_POST['active_nums'];
        	$activeReason = $_POST['active_reason'];
        	
        	/**
        	 * msg active
        	 */
        	$MsgActive = M("MsgActive");
        	$UserLogin = M("UserLogin");
            $userArray = explode(";", $_POST['user_ids']);

            /**
             * message to owner
        	 */
            $contentToOwnerMsgSystem = "你获得了我帮圈圈奖励的活跃 :)";
            $i = 0;
            foreach ($userArray as $user) {
            	$recordUserLogin = $UserLogin->find($user);
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


                i_savenotice('10000', $user, 'helprooter/user:active', '');

        	    $i++;
            }
            
        	/**
        	 * admin user operating record
        	 */
        	if (!empty($admin['uid'])) {
        		$AdminUserrecord = M("AdminUserrecord");
        		$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '奖励我帮圈圈活跃: 共'.$i.'人, nums:'.$activeNums.', reason:'.$activeReason,
					'time' => time(),
        		);
        		$AdminUserrecord->add($newAdminUserrecordData);
        	}
            redirect('/rooter/userhonor', 3, '奖励我帮圈圈活跃 共'.$i.'人...');
        }
        
        $this->display();
    }


    public function userinvite()
    {
    	$admin = logincheck();
    	$this->assign('title', '邀请用户 奖励');
    	$UserInvite = M("UserInvite");

    	if ($this->isPost()) {
    		$id = (int)$_POST['id'];
    		$award = (int)$_POST['award'];
    		$recordUserInvite = $UserInvite->find($id);
    		if (!empty($recordUserInvite['id'])) {
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
                    i_savenotice("10000", $recordUserInvite['uid'], 'root/userinvite:success', '');
	            } else {
	            	$msgContent = "你邀请的用户无效:(，暂时不赠送活跃";
                    i_savenotice("10000", $recordUserInvite['uid'], 'root/userinvite:success', '');//TODO bounce
	            }
	            
	            /**
	             * admin user operating record
	             */
	            if (!empty($admin['uid'])) {
	            	$AdminUserrecord = M("AdminUserrecord");
	            	$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '邀请用户认定, uid:'.$recordUserInvite['uid'].', content:'.$msgContent,
						'time' => time(),
	            	);
	            	$AdminUserrecord->add($newAdminUserrecordData);
	            }
	            
    			redirect('/rooter/userinvite', 1, 'ok...');
    		}
    	}

    	$page = i_page_get_num();
        $count = 20;
        $this->assign('count', $count);
        $offset = $page * $count;
        $resultsUserInvite = $UserInvite->order('time DESC')->limit($offset,$count)->select();
        $this->assign('resultsUserInvite', $resultsUserInvite);

        $totalRecords = $UserInvite->count();
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
        $admin = logincheck();
    	$this->assign('title', '信息管理');
        $RecordSay = M("RecordSay");
        if (!empty($_POST['recordid'])) {
        	$recordid = $_POST['recordid'];
        	$resultRecordSay = $RecordSay->where("sid = $recordid")->find();
        	$this->assign('recordDelete', $resultRecordSay);
        	
        	/**
        	 * admin user operating record
        	 */
        	if (!empty($admin['uid'])) {
        		$AdminUserrecord = M("AdminUserrecord");
        		$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '查询记录, sid:'.$_POST['recordid'].' content:'.$resultRecordSay['content'],
					'time' => time(),
        		);
        		$AdminUserrecord->add($newAdminUserrecordData);
        	}
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
        	 * admin user operating record
        	 */
        	if (!empty($admin['uid'])) {
        		$AdminUserrecord = M("AdminUserrecord");
        		$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '删除记录, sid:'.$sid,
					'time' => time(),
        		);
        		$AdminUserrecord->add($newAdminUserrecordData);
        	}
        	redirect('/rooter/record', 3, '删除记录成功 涉及4个表...');
        }
        $this->display();
    }


    /**
     * email
     */
    public function emailtowebmaster()
    {
    	$admin = logincheck();
    	$this->assign('title', '站长邮件通知');
    	if ($this->isPost()) {
    	    $noticecontent = $_POST['noticecontent'];
    	    if (empty($_POST['emailstrings']) || empty($noticecontent)) {
    	        redirect('/rooter/emailtowebmaster', 3, 'uidstrings or content is empty!');
    	    }
    	    $emailstrings = explode(";", $_POST['emailstrings']);

    	    /**
             * send welcome email
             */
            Vendor('Ihelpoo.Email');
            $emailObj = new Email();
            $emailsended = null;
            $i = 0;
            foreach ($emailstrings as $toemail) {
            	if (!empty($toemail)) {
            		$isSend = $emailObj->towebmaster($toemail, $noticecontent);
	            	if ($isSend) {
	                    $emailsended .= $toemail." ok<br />";
	                    $i++;
	                }
            	}
            }
    	
	    	/**
	    	 * admin user operating record
	    	 */
	    	if (!empty($admin['uid'])) {
	    		$AdminUserrecord = M("AdminUserrecord");
	    		$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '发布站长通知邮件 ok:'.$i.' content:'.$noticecontent,
					'time' => time(),
	    		);
	    		$AdminUserrecord->add($newAdminUserrecordData);
	    	}
	    	$this->ajaxReturn($emailsended,'emailsended ok','ok');
    	}
    	$this->display();
    }
    
    public function emailinvite()
    {
        $admin = logincheck();
    	$this->assign('title', '邮件邀请');
    	if ($this->isPost()) {
    	    if (empty($_POST['emails'])) {
    	        redirect('/rooter/emailinvite', 3, 'emails is empty!');
    	    }
    	    $emails = explode(";", $_POST['emails']);

    	    /**
             * send welcome email
             */
            Vendor('Ihelpoo.Email');
            $emailObj = new Email();
            $emailsended = null;
            $i = 0;
            foreach ($emails as $emailin) {
            	if ($i < 200) {
            		$emailin = trim($emailin);
            		$isSend = $emailObj->invite($emailin);
            		if ($isSend) {
            			$emailsended .= $emailin." ok<br />";
            			$i++;
            		}
            	}
            }
            $this->assign('emailsended', $emailsended);
            
            /**
             * admin user operating record
             */
            if (!empty($admin['uid'])) {
            	$AdminUserrecord = M("AdminUserrecord");
            	$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '邮件邀请 emailinvite, ok:'.$i,
					'time' => time(),
            	);
            	$AdminUserrecord->add($newAdminUserrecordData);
            }
    	}
    	$this->display();
    }

    /**
     * invite long time not login user If they had new msg
     */
    public function reinviteltnl()
    {
        $admin = logincheck();
    	$this->assign('title', '邮件提醒消息');
        if (isset($_GET['autorun'])) {
            $UserLogin = M("UserLogin");
            $MsgComment = M("MsgComment");
            //MsgSystem
            $MsgAt = M("MsgAt");
            $TalkContent = M("TalkContent");
            $timewidth = time() - 604800;
            $userLongNotLogin = $UserLogin->where("logintime < $timewidth")->select();
            if (!empty($userLongNotLogin)) {

            	/**
            	 * send email
            	 */
            	Vendor('Ihelpoo.Email');
            	$emailObj = new Email();
            	$i =0;
                foreach ($userLongNotLogin as $userNotLogin) {
                	$userNotLoginUid = $userNotLogin['uid'];
                    $msgCommentNums = $MsgComment->where("uid = $userNotLoginUid AND deliver = '0'")->count();
                    $msgSystemNums = 0;
                    $msgAtNums = $MsgAt->where("touid = $userNotLoginUid AND deliver = '0'")->count();
                    $newTalkNums = $TalkContent->where("touid = $userNotLoginUid AND deliver = '0'")->count();
                    if ($msgCommentNums != 0 || $msgSystemNums != 0 || $msgAtNums != 0 || $newTalkNums != 0) {
                    	$messageNums = $msgCommentNums + $msgSystemNums + $msgAtNums + $newTalkNums;
                        $isSend = $emailObj->longtimeNotlogin($userNotLogin['email'], $userNotLogin['nickname'], $messageNums);
                        if ($isSend) {
                            $sendReinviteArray[] = array(
                                'nickname' => $userNotLogin['nickname'],
                                'email' => $userNotLogin['email'],
                            );
                            $i++;
                        }
                    }
                }
                $this->assign('sendReinviteArray', $sendReinviteArray);
                
                /**
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '邮件提醒消息 reinviteltnl, ok:'.$i,
						'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
            }
        }
        $this->display();
    }

    public function emailnotice()
    {
    	$admin = logincheck();
    	$this->assign('title', '邮件通知消息');
    	if ($this->isPost()) {
    		$noticecontent = $_POST['noticecontent'];
    	    if (empty($_POST['uidstrings']) || empty($noticecontent)) {
    	        redirect('/rooter/emailnotice', 3, 'uidstrings or content is empty!');
    	    }
    	    $uidstrings = explode(";", $_POST['uidstrings']);

    	    /**
             * send welcome email
             */
            Vendor('Ihelpoo.Email');
            $emailObj = new Email();
            $emailsended = null;
            $UserLogin = M("UserLogin");
            $i = 0;
            foreach ($uidstrings as $uid) {
            	$recordUserLogin = $UserLogin->find($uid);
            	if (!empty($recordUserLogin['email'])) {
            		$isSend = $emailObj->mallNotice($recordUserLogin['email'], $recordUserLogin['nickname'], $noticecontent);
	            	if ($isSend) {
	                    $emailsended .= $recordUserLogin['email']." ok<br />";
	                    $i++;
	                }
            	}
            }
            $this->assign('emailsended', $emailsended);
            
            /**
             * admin user operating record
             */
            if (!empty($admin['uid'])) {
            	$AdminUserrecord = M("AdminUserrecord");
            	$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '邮件邀请公告 emailnotice, ok:'.$i.' content:'.substr($noticecontent, 0, 20).'...',
					'time' => time(),
            	);
            	$AdminUserrecord->add($newAdminUserrecordData);
            }
    	}
    	
    	$this->display();
    }
    
    /**
     * about CMS
     */
    public function about()
    {
    	$admin = logincheck();
    	$this->assign('title','关于我们 文章管理');
        $CmsArtical = M("CmsArtical");
        $cmsArticalRecords = $CmsArtical->order('time DESC')->select();
        $this->assign('cmsArticalRecords',$cmsArticalRecords);

        /**
         * edit artical
         */
        if ($_GET['artical'] == 'edit' || $_GET['artical'] == 'delete') {
        	$articalid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        	$cmsArticalRecord = $CmsArtical->find($articalid);
        	$this->assign('cmsArticalRecord',$cmsArticalRecord);
        }

        /**
         * delete artical
         */
        if ($_GET['artical'] == 'suredelete') {
        	$articalid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        	$cmsArticalRecord = $CmsArtical->find($articalid);
        	
        	/**
        	 * admin user operating record
        	 */
        	if (!empty($admin['uid'])) {
        		$AdminUserrecord = M("AdminUserrecord");
        		$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '删除文章 about CMS, articalid:'.$articalid. 'title:'.$cmsArticalRecord['title'],
					'time' => time(),
        		);
        		$AdminUserrecord->add($newAdminUserrecordData);
        	}
        	
        	if (empty($cmsArticalRecord['id'])) {
        		redirect('/rooter/about', 1, 'failed artical is not exist :( ...');
        	} else {
        		$CmsArtical->where("id = $cmsArticalRecord[id]")->delete();
        		redirect('/rooter/about', 1, 'delete ok  ...');
        	}
        	$this->assign('cmsArticalRecord',$cmsArticalRecord);
        }

        /**
         * new artical
         */
        if ($this->isPost()) {
        	$id = (int)$_POST['id'];
        	$title = $_POST['title'];
        	$content = $_POST['content'];
        	if (!empty($id)) {
        		$editArtical = array(
	        		'id' => $id,
	        		'title' => $title,
	        		'content' => $content,
	        	);
	        	$isEditFlag = $CmsArtical->save($editArtical);
	        	
	        	/**
	        	 * admin user operating record
	        	 */
	        	if (!empty($admin['uid'])) {
	        		$AdminUserrecord = M("AdminUserrecord");
	        		$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '修改文章 about CMS, articalid:'.$isEditFlag. 'title:'.$title,
						'time' => time(),
	        		);
	        		$AdminUserrecord->add($newAdminUserrecordData);
	        	}
	        	
	        	if ($isEditFlag) {
	        		redirect('/rooter/about', 1, 'edit ok...');
	        	} else {
	        		redirect('/rooter/about', 1, 'edit failed...');
	        	}
        	} else {
	        	$newArtical = array(
	        		'id' => '',
	        		'uid' => $admin['uid'],
	        		'title' => $title,
	        		'content' => $content,
	        		'time' => time(),
	        		'hit' => 0,
	        	);
	        	$isAddFlag = $CmsArtical->add($newArtical);
	        	
	        	/**
	        	 * admin user operating record
	        	 */
	        	if (!empty($admin['uid'])) {
	        		$AdminUserrecord = M("AdminUserrecord");
	        		$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '添加文章 about CMS, articalid:'.$isAddFlag. 'title:'.$title,
						'time' => time(),
	        		);
	        		$AdminUserrecord->add($newAdminUserrecordData);
	        	}
	        	
	        	if ($isAddFlag) {
	        		redirect('/rooter/about', 1, 'ok...');
	        	} else {
	        		redirect('/rooter/about', 1, 'failed...');
	        	}
        	}
        }
        $this->display();
    }

    /**
     * mall
     */
    public function mallshop()
    {
    	$admin = logincheck();
    	$this->assign('title','小店管理');
    	$page = i_page_get_num();
        $count = 10;
        $offset = $page * $count;
    	$UserShop = M("UserShop");
    	$userId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
		
    	/**
    	 * search
    	 */
    	if (!empty($_GET['searchuid'])) {
    		$searchuid = $_GET['searchuid'];
    		redirect('/rooter/mallshop/'.$searchuid, 0, 'ok...');
    	}
    	
    	/**
    	 * post change shop status
    	 */
    	if ($this->isPost()) {
    		$uid = (int)$_POST['uid'];
    		$status = (int)$_POST['status'];
    		$shoptype = (int)$_POST['shop_type'];
    		$recordUserShop = $UserShop->find($uid);
    		if ($shoptype != $recordUserShop['shop_type']) {
	    		if (!empty($uid) && !empty($shoptype)) {
	    			$updateShopStatus = array(
	    				'uid' => $uid,
	    				'shop_type' => $shoptype,
	    			);
	    			$UserShop->save($updateShopStatus);
	    			
	    			/**
	    			 * change user type
	    			 */
	    			$UserLogin = M("UserLogin");
	    			if ($shoptype == 3) {
	    				$newUserLoginType = array(
	    					'uid' => $uid,
	    					'type' => 3
	    				);
	    			} else {
	    				$newUserLoginType = array(
	    					'uid' => $uid,
	    					'type' => 1
	    				);
	    			}
	    			$UserLogin->save($newUserLoginType);
	    			
	    			/**
		             * admin user operating record
		             */
		            if (!empty($admin['uid'])) {
		            	$AdminUserrecord = M("AdminUserrecord");
		            	$newAdminUserrecordData = array(
							'id' => '',
							'uid' => $admin['uid'],
							'record' => '小店管理, uid:'.$uid. 'change shop type:'.$shoptype,
							'time' => time(),
		            	);
		            	$AdminUserrecord->add($newAdminUserrecordData);
		            }
		            
	    			redirect('/rooter/mallshop/'.$uid, 1, 'ok...');
	    		}
    		}
    		
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

		            	/**
		            	 * insert priority data
		            	 */
		            	$priorityUid = 12921;
		            	$UserPriority = M("UserPriority");
		            	$isPriorityExist = $UserPriority->where("uid = $uid AND pid = $priorityUid")->find();
		            	if (empty($isPriorityExist['id'])) {
		            		$priorityInsertData = array(
					            'id' => '',
					            'uid' => $uid,
					            'pid' => $priorityUid,
					        	'pid_type' => 2,
					            'time' => time(),
		            		);
		            		$isPriorityDataInserted = $UserPriority->add($priorityInsertData);
		            		
		            		/**
		            		 * update i_user_info follow fans nums
		            		 */
		            		$UserInfo = M("UserInfo");
		            		$userInfoPriority = $UserInfo->find($uid);
		            		$userInfoPrioritied = $UserInfo->find($priorityUid);
		            		$newUserInfoPriorityData = array(
					        	'uid' => $uid,
					        	'follow' => $userInfoPriority['follow'] + 1,
		            		);
		            		$UserInfo->save($newUserInfoPriorityData);
		            		$newUserInfoPrioritiedData = array(
					        	'uid' => $priorityUid,
					        	'fans' => $userInfoPrioritied['fans'] + 1,
		            		);
		            		$UserInfo->save($newUserInfoPrioritiedData);
		            	}
                        i_savenotice('10000', $uid, 'system/shopaudited', '');
                        $msgContent = '资料审核通过，您的小店开通了!';
		            } else {
                        i_savenotice('10000', $uid, 'system/shopreaudit', ''); //TODO bounce notice
                        $msgContent = '资料重新审核中，您的小店暂时关闭!';
		            }

		            /**
		             * admin user operating record
		             */
		            if (!empty($admin['uid'])) {
		            	$AdminUserrecord = M("AdminUserrecord");
		            	$newAdminUserrecordData = array(
							'id' => '',
							'uid' => $admin['uid'],
							'record' => '小店管理, uid:'.$uid. 'content:'.$msgContent,
							'time' => time(),
		            	);
		            	$AdminUserrecord->add($newAdminUserrecordData);
		            }
		            
	    			redirect('/rooter/mallshop/'.$uid, 1, 'ok...');
	    		}
    		}
    	}

    	/**
    	 * list
    	 */
    	if (!empty($userId)) {
    		$userShopRecord = $UserShop->find($userId);
    		$this->assign('userShopRecord',$userShopRecord);
    	} else {
	    	$userShopRecords = $UserShop->order("status DESC, time ASC")->limit($offset,$count)->select();
	    	$this->assign('userShopRecords',$userShopRecords);
	    	$totalshops = $UserShop->count();
	    	$this->assign('totalshops',$totalshops);
	    	$totalPages = ceil($totalshops / $count);
	        $this->assign('totalPages',$totalPages);
    	}
    	$this->display();
    }
    
    public function mallcommodity()
    {
    	$admin = logincheck();
    	$this->assign('title','商品管理');
    	if ($this->isPost()) {
    		$RecordCommodity = M("RecordCommodity");
    		if (!empty($_POST['search'])) {
        		$searchWords = (int)trim(htmlspecialchars(strip_tags($_POST['search'])));
        		$resultRecordCommodity = $RecordCommodity->where("cid = $searchWords")->find();
        		$this->assign('resultRecordCommodity', $resultRecordCommodity);
        		
        		$UserShop = M("UserShop");
        		$recordUserShop = $UserShop->where("uid = $resultRecordCommodity[shopid]")->find();
        		$this->assign('recordUserShop', $recordUserShop);
        		
	    		/**
	        	 * admin user operating record
	        	 */
	        	if (!empty($admin['uid'])) {
	        		$AdminUserrecord = M("AdminUserrecord");
	        		$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => 'search 搜索商品: '.$resultRecordCommodity['name'],
					    'time' => time(),
	        		);
	        		$AdminUserrecord->add($newAdminUserrecordData);
	        	}
    		}
    		
    		if (!empty($_POST['cid'])) {
    			$newStatusRecordCommodity = array(
    				'cid' => (int)$_POST['cid'],
    				'status' => (int)$_POST['status']
    			);
    			$RecordCommodity->save($newStatusRecordCommodity);
    			
    			/**
	        	 * admin user operating record
	        	 */
	        	if (!empty($admin['uid'])) {
	        		$AdminUserrecord = M("AdminUserrecord");
	        		$newAdminUserrecordData = array(
					    'id' => '',
					    'uid' => $admin['uid'],
					    'record' => '更新商品状态 cid:'.$_POST['cid'].' status:'.$_POST['status'],
					    'time' => time(),
	        		);
	        		$AdminUserrecord->add($newAdminUserrecordData);
	        	}
	        	redirect('/rooter/mallcommodity/', 1, 'update commodity status ok...');
    		}
    	}
    	$this->display();
    }
    
    public function mallcommodityassess()
    {
    	$admin = logincheck();
    	$this->assign('title','交易管理');
    	$statusMarks = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$this->assign('statusMarks', $statusMarks);
    	
    	$page = i_page_get_num();
        $count = 10;
        $offset = $page * $count;
    	$RecordCommodityassess = M("RecordCommodityassess");
    	if ($statusMarks == 1) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where('status = 1')->order('start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where('status = 1')->count();
    	} else if ($statusMarks == 2) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where('status = 2')->order('start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where('status = 2')->count();
    	} else if ($statusMarks == 3) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where('status = 3')->order('start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where('status = 3')->count();
    	} else if ($statusMarks == 4) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where('status = 4')->order('start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where('status = 4')->count();
    	} else if ($statusMarks == 5) {
    		$resultRecordCommodityassess = $RecordCommodityassess->where('status = 5')->order('start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->where('status = 5')->count();
    	} else {
    		$resultRecordCommodityassess = $RecordCommodityassess->order('start_ti DESC')->limit($offset,$count)->select();
    		$totalrecords = $RecordCommodityassess->count();
    	}
    	$this->assign('resultRecordCommodityassess', $resultRecordCommodityassess);
    	$this->assign('totalrecords', $totalrecords);
    	$totalPages = ceil($totalrecords / $count);
    	$this->assign('totalPages', $totalPages);
    	$this->display();
    }

    public function mallindex()
    {
    	$admin = logincheck();
    	$this->assign('title','mall首页管理');
    	$MallIndeximg = M("MallIndeximg");

    	/**
    	 * delete record
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelId = (int)$_GET['suredel'];
    		$deleteRecord = $MallIndeximg->find($suredelId);

    		if (!empty($deleteRecord['middle_img'])) {
    			$deleteRecordUrl = $deleteRecord['middle_img'];
    		} else if (!empty($deleteRecord['right_img'])) {
    			$deleteRecordUrl = $deleteRecord['right_img'];
    		} else if (!empty($deleteRecord['center_img'])) {
    			$deleteRecordUrl = $deleteRecord['center_img'];
    		}
    		$storage = new SaeStorage();
    		$urlFilename = str_ireplace("http://ihelpoo-public.stor.sinaapp.com/", "", $deleteRecordUrl);
    		
    		$isStorageDeleteFlag = $storage->delete("public", $urlFilename);
    		var_dump($isStorageDeleteFlag);
    		
    		/**
    		 * admin user operating record
    		 */
    		if (!empty($admin['uid'])) {
    			$AdminUserrecord = M("AdminUserrecord");
    			$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => 'mallindex, 删除图片 suredelId:'.$suredelId. 'deleteRecordUrl:'.$deleteRecordUrl,
					'time' => time(),
    			);
    			$AdminUserrecord->add($newAdminUserrecordData);
    		}
    		
    		if ($isStorageDeleteFlag) {
    			$MallIndeximg->where("id = $suredelId")->delete();
    			redirect('/rooter/mallindex', 1, 'deltete success...');
    		} else {
    			redirect('/rooter/mallindex', 1, 'deltete failed...');
    		}
    	}

    	/**
    	 * change index image
    	 */
    	if ($this->isPost()) {
    		if (empty($_POST['id'])) {
    			redirect('/rooter/mallindex', 3, 'failed...id empty');
    		}
    		$newMallIndeximgData['id'] = (int)$_POST['id'];
    		if (!empty($_POST['middle_url'])) {
    			$newMallIndeximgData['middle_url'] = $_POST['middle_url'];
    		} else if (!empty($_POST['right_url'])) {
    			$newMallIndeximgData['right_url'] = $_POST['right_url'];
    		} else if (!empty($_POST['center_url'])) {
    			$newMallIndeximgData['center_url'] = $_POST['center_url'];
    		}
    		$newMallIndeximgData['order'] = (int)$_POST['order'];
    		$isUpdateUrl = $MallIndeximg->save($newMallIndeximgData);
    		
    		/**
    		 * admin user operating record
    		 */
    		if (!empty($admin['uid'])) {
    			$AdminUserrecord = M("AdminUserrecord");
    			$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => 'mallindex, change 图片 id:'.$_POST['id']. ' newMallIndeximgData middle_url:'.$newMallIndeximgData['middle_url'],
					'time' => time(),
    			);
    			$AdminUserrecord->add($newAdminUserrecordData);
    		}
    		
    		if ($isUpdateUrl) {
    			redirect('/rooter/mallindex', 1, 'success...');
    		} else {
    			redirect('/rooter/mallindex', 3, 'failed...');
    		}
    	}

    	$resultsMallIndeximg = $MallIndeximg->order("time DESC")->select();
    	$this->assign('resultsMallIndeximg', $resultsMallIndeximg);
    	$this->display();
    }

	public function mallindeximg()
    {
    	$admin = logincheck();
    	if ($this->isPost()) {
    		$imageShowType = $_POST['image_type'];
    		if (!empty($_FILES)) {
    			if ($_FILES["uploadimage"]["error"] > 0) {
    				redirect('/rooter/mallindex', 3, 'file error...'.$_FILES["uploadimage"]["error"]);
    			} else {
    				$imageOldName = $_FILES["uploadimage"]["name"];
    				$imageType = $_FILES["uploadimage"]["type"];
    				$imageSize = $_FILES["uploadimage"]["size"];
    				$imageTmpName = $_FILES["uploadimage"]["tmp_name"];
    			}

    			/**
    			 * $tempRealSize = getimagesize($_FILES["uploadedimg"]["tmp_name"]);
    			 * $logoRealWidth = $tempRealSize['0'];
    			 * $logoRealHeight = $tempRealSize['1'];
    			 */
    			if ($imageSize > 3670016) {
    				redirect('/rooter/mallindex', 3, 'error...上传图片太大, 最大能上传单张 3.5MB');
    			} else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {
    				import("@.ORG.UploadFile");
    				$config=array(
		                'allowExts'=>array('jpeg','jpg','gif','png','bmp'),
		                'savePath'=>'./Public/mall/',
		                'saveRule'=>'mallindex'.$imageShowType.time(),
    				);
    				$upload = new UploadFile($config);
    				$upload->imageClassPath="@.ORG.Image";
    				$upload->thumb=false;
    				if (!$upload->upload()) {
    					$uploadErrorInfo = $upload->getErrorMsg();
    					redirect('/rooter/mallindex', 3, 'error...'.$uploadErrorInfo);
    				} else {
    					$info = $upload->getUploadFileInfo();
    					$storage = new SaeStorage();
    					$newfilepath = $storage->getUrl("public", "mall/".$info[0]['savename']);

    					/**
    					 * insert into i_user_album
    					 */
    					$MallIndeximg = M("MallIndeximg");
    					$newIndeximgData['id'] = null;
    					$newIndeximgData['order'] = 1;
    					$newIndeximgData['time'] = time();
    					if ($imageShowType == 'middlie3') {
    						$newIndeximgData['middle_img'] = $newfilepath;
    					} else if ($imageShowType == 'right2') {
    						$newIndeximgData['right_img'] = $newfilepath;
    					} else if ($imageShowType == 'center1') {
    						$newIndeximgData['center_img'] = $newfilepath;
    					}
    					$isUploadFlag = $MallIndeximg->add($newIndeximgData);
    					
    					/**
    					 * admin user operating record
    					 */
    					if (!empty($admin['uid'])) {
    						$AdminUserrecord = M("AdminUserrecord");
    						$newAdminUserrecordData = array(
								'id' => '',
								'uid' => $admin['uid'],
								'record' => 'mallindeximg, upload image id:'.$isUploadFlag. ' newfilepath:'.$newfilepath,
								'time' => time(),
    						);
    						$AdminUserrecord->add($newAdminUserrecordData);
    					}
    					
    					if ($isUploadFlag) {
    						redirect('/rooter/mallindex', 1, 'success...');
    					} else {
    						redirect('/rooter/mallindex', 3, 'failed...');
    					}
    				}
    			} else {
    				redirect('/rooter/mallindex', 3, 'error...上传图片格式错误, 目前仅支持.jpg .png .gif');
    			}
    		}
    	}
    	$this->display();
    }

    public function mallcooperation()
    {
    	$admin = logincheck();
    	$this->assign('title','cooperation 管理');
    	$MallCooperation = M("MallCooperation");

    	/**
    	 * delete record
    	 */
    	if (!empty($_GET['suredel'])) {
    		$suredelId = (int)$_GET['suredel'];
    		$MallCooperation->where("id = $suredelId")->delete();
    		
    		/**
    		 * admin user operating record
    		 */
    		if (!empty($admin['uid'])) {
    			$AdminUserrecord = M("AdminUserrecord");
    			$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => 'mallcooperation deltete, suredel id:'.$suredelId,
					'time' => time(),
    			);
    			$AdminUserrecord->add($newAdminUserrecordData);
    		}
    		redirect('/rooter/mallcooperation', 1, 'deltete success...');
    	}

    	/**
    	 * post
    	 */
    	if ($this->isPost()) {
    		$id = (int)$_POST['id'];
    		$name = $_POST['name'];
    		$url = $_POST['url'];
    		$order = (int)$_POST['order'];
    		if (empty($id)) {
    			$newCooperationData = array(
    				'id' => '',
    				'name' => $name,
    				'url' => $url,
    				'order' => $order,
    				'time' => time(),
    			);
    			$MallCooperation->add($newCooperationData);
    			
    			/**
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => 'mallcooperation new add, name:'.$name.' url:'.$url,
						'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			redirect('/rooter/mallcooperation', 1, 'success...new add');
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
    			 * admin user operating record
    			 */
    			if (!empty($admin['uid'])) {
    				$AdminUserrecord = M("AdminUserrecord");
    				$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => 'mallcooperation update, name:'.$name.' url:'.$url,
						'time' => time(),
    				);
    				$AdminUserrecord->add($newAdminUserrecordData);
    			}
    			redirect('/rooter/mallcooperation', 1, 'success...update');
    		}
    	}
    	$resultsMallCooperation = $MallCooperation->select();
    	$this->assign('resultsMallCooperation', $resultsMallCooperation);
    	$this->display();
    }
    
    /**
     * activity 
     */
    public function activity()
    {
    	$admin = logincheck();
    	$this->assign('title','活动管理');
    	$ActivityItem = M("ActivityItem");
    	
    	/**
    	 * change activity status
    	 */
    	if (!empty($_GET['aid'])) {
    		$aid = (int)$_GET['aid'];
    		$recordActivityItem = $ActivityItem->where("aid = $aid")->find();
    		if (empty($recordActivityItem['aid'])) {
    			redirect('/rooter/activity', 2, 'error aid empty...');
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
                i_savenotice('10000', $recordActivityItem['sponsor_uid'], 'system/activityaudited', '');
	            
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
		            	'school_id' => $recordActivityItem['school_id']
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
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '活动通过 activity status success, aid:'.$recordActivityItem['aid'].' uid:'.$recordActivityItem['sponsor_uid'].' subject:'.$recordActivityItem['subject'],
						'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
                
    			redirect('/rooter/activity', 2, '通过 change activity status success...');
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
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '活动不通过 activity status failed, aid:'.$recordActivityItem['aid'].' uid:'.$recordActivityItem['sponsor_uid'].' subject:'.$recordActivityItem['subject'],
						'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
                
    			redirect('/rooter/activity', 2, '不通过 change activity status failed...');
    		}
    	}
    	
    	/**
    	 * lists
    	 */
    	$page = i_page_get_num();
        $count = 30;
        $offset = $page * $count;
    	
    	$recordsActivityItem = $ActivityItem->order("time DESC")->limit($offset,$count)->select();
    	$this->assign('recordsActivityItem',$recordsActivityItem);
    	$totalrecords = $ActivityItem->count();
    	$this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
    	$this->display();
    }
    
    public function userfillaccount()
    {
    	$admin = logincheck();
    	$adminuid = $admin['uid'];
    	$this->assign('title','填充账号');
    	$UserLogin = M("UserLogin");
    	$recordsUserLogin = $UserLogin->where("email = ''")->select();
    	$this->assign('recordsUserLogin',$recordsUserLogin);
    	$this->display();
    }
    
    public function suggestion()
    {
    	$admin = logincheck();
    	$adminuid = $admin['uid'];
    	$this->assign('title','意见建议');
    	$DataSuggestion = M("DataSuggestion");
    	
    	if (!empty($_POST['replyid'])) {
    		$replyid = (int)$_POST['replyid'];
    		$replycontent = $_POST['replycontent'];
    		$recordDataSuggestion = $DataSuggestion->find($replyid);
    		$updateReply = array(
    			'id' => $replyid,
    			'ihelpoo_reply' => $replycontent,
    			'ihelpoo_reply_uid' => $adminuid,
    			'ihelpoo_reply_time' => time()
    		);
    		$DataSuggestion->save($updateReply);
    		
    		if (!empty($recordDataSuggestion['ihelpoo_reply'])) {
    			/**
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '修改建议回复:'.$recordDataSuggestion['suggestion'].' oldreply:'.$recordDataSuggestion['ihelpoo_reply'].' newreply:'.$replycontent,
						'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
    			
    			$this->ajaxReturn(0,'修改成功','yes');
    		} else {
    			
    			/**
		    	 * send msg && email to suggester
		    	 */
		    	$SchoolInfo = M("SchoolInfo");
		    	$recordSchoolInfo = $SchoolInfo->find($recordDataSuggestion['school_id']);
		    	$recordSchoolInfoDomain = empty($recordSchoolInfo['domain_main']) ? $recordSchoolInfo['domain'] : $recordSchoolInfo['domain_main'];
		    	
    			/**
    			 * new reply
    			 * send msg to webmaster
    			 */
    			$SchoolWebmaster = M("SchoolWebmaster");
		    	$recordSchoolWebmaster = $SchoolWebmaster->where("sid = $recordDataSuggestion[school_id]")->join('i_user_login ON i_school_webmaster.uid = i_user_login.uid')
		    	->field("i_user_login.uid,i_user_login.email,i_user_login.nickname")
		    	->select();
		    	$emailtitle = "建议回复处理";
		    	$emailcontent = "我帮圈圈团队回复了用户建议:<br />“".$recordDataSuggestion['suggestion']."”。<br />希望校园团队安排人员对该建议及时回复并做好相关处理工作。<a href='http://".$recordSchoolInfoDomain."/about/suggestion'>详情</a>";;
		    	foreach ($recordSchoolWebmaster as $schoolWebmaster) {
		    		i_send($schoolWebmaster['email'], $emailtitle, $emailcontent);
		    	}
		    	
		    	if (!empty($recordDataSuggestion['uid'])) {
		    		if (i_check_email($recordDataSuggestion['contact'])) {
		    			$emailtitlesuggester = "建议回复";
		    			$emailcontentsuggester = "我帮圈圈团队回复了您的建议:<br />“".$recordDataSuggestion['suggestion']."”。<br />回复内容:“".$replycontent."”。<a href='http://".$recordSchoolInfoDomain."/about/suggestion'>详情</a>";
		    			i_send($recordDataSuggestion['contact'], $emailtitlesuggester, $emailcontentsuggester);
		    		}
		    		
		    		$UserLogin = M("UserLogin");
		    		$recordUserLogin = $UserLogin->find($recordDataSuggestion['uid']);
		    		if (!empty($recordUserLogin['uid'])) {
		    			/**
		    			 * send msg system
		    			 * is url handeled
		    			 * 我帮圈圈团队回复了你的建议<a href='http://".$recordSchoolInfoDomain."/about/suggestion'>详情</a>
		    			 */
                          i_savenotice("10000",$recordUserLogin['uid'], "system/suggestion:reply", "");//TODO 最后一个参数，回复内容的id
		    		}
		    		
		    	}
    			
    			/**
                 * admin user operating record
                 */
                if (!empty($admin['uid'])) {
                	$AdminUserrecord = M("AdminUserrecord");
                	$newAdminUserrecordData = array(
						'id' => '',
						'uid' => $admin['uid'],
						'record' => '回复建议:'.$recordDataSuggestion['suggestion'].' reply:'.$replycontent,
						'time' => time(),
                	);
                	$AdminUserrecord->add($newAdminUserrecordData);
                }
    			
    			$this->ajaxReturn(0,'回复成功','yes');
    		}
    	}
    	
    	if (!empty($_POST['suredel'])) {
    		$suredel = (int)$_POST['suredel'];
    		$recordDataSuggestion = $DataSuggestion->find($suredel);
    		
    		/**
    		 * admin user operating record
    		 */
    		if (!empty($admin['uid'])) {
    			$AdminUserrecord = M("AdminUserrecord");
    			$newAdminUserrecordData = array(
					'id' => '',
					'uid' => $admin['uid'],
					'record' => '删除建议:'.$recordDataSuggestion['suggestion'].' reply:'.$recordDataSuggestion['ihelpoo_reply'],
					'time' => time(),
    			);
    			$AdminUserrecord->add($newAdminUserrecordData);
    		}
    		
    		$DataSuggestion->where("id = $suredel")->delete();
    		$this->ajaxReturn(0,'删除成功','yes');
    	}
    	
    	$page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
    	$recordsDataSuggestion = $DataSuggestion->join('i_school_info ON i_data_suggestion.school_id = i_school_info.id')
    	->join('i_user_login ON i_data_suggestion.uid = i_user_login.uid')
    	->field('i_data_suggestion.id,i_user_login.uid,i_data_suggestion.suggester,i_data_suggestion.contact,i_data_suggestion.suggestion,i_data_suggestion.time,i_data_suggestion.ihelpoo_reply,i_data_suggestion.ihelpoo_reply_uid,i_data_suggestion.ihelpoo_reply_time,i_data_suggestion.school_reply,i_data_suggestion.school_reply_uid,i_data_suggestion.school_reply_time,i_data_suggestion.school_id,nickname,sex,birthday,enteryear,type,online,active,icon_url,i_school_info.school,i_school_info.domain,i_school_info.domain_main')
    	->order("i_data_suggestion.time DESC")->select();
    	$totalrecords = $DataSuggestion->count();
    	$this->assign('recordsDataSuggestion',$recordsDataSuggestion);
    	$this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
    	$this->display();
    }
    
    public function useroperatingrecord()
    {
    	$admin = logincheck();
    	$adminuid = $admin['uid'];
    	$this->assign('title','操作记录');
    	$AdminUserrecord = M("AdminUserrecord");
    	$AdminUser = M("AdminUser");
    	$recordAdminUser = $AdminUser->find($adminuid);
    	$page = i_page_get_num();
        $count = 30;
        $offset = $page * $count;
    	if ($recordAdminUser['priority'] == '1') {
    		$recordsAdminUserrecord = $AdminUserrecord->join('i_admin_user ON i_admin_userrecord.uid = i_admin_user.uid')->field('i_admin_userrecord.id,i_admin_userrecord.uid,i_admin_user.name,i_admin_userrecord.record,i_admin_userrecord.time')->order("time DESC")->limit($offset,$count)->select();
    		$totalrecords = $AdminUserrecord->count();
    	} else {
    		$recordsAdminUserrecord = $AdminUserrecord->where("i_admin_user.uid = $adminuid")->join('i_admin_user ON i_admin_userrecord.uid = i_admin_user.uid')->field('i_admin_userrecord.id,i_admin_userrecord.uid,i_admin_user.name,i_admin_userrecord.record,i_admin_userrecord.time')->order("time DESC")->limit($offset,$count)->select();
    		$totalrecords = $AdminUserrecord->where("uid = $adminuid")->count();
    	}
    	$this->assign('recordsAdminUserrecord',$recordsAdminUserrecord);
    	$this->assign('totalrecords',$totalrecords);
    	$totalPages = ceil($totalrecords / $count);
        $this->assign('totalPages',$totalPages);
    	$this->display();
    }
    
}

?>