<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class MallsetAction extends Action {

    protected function _initialize()
    {
        $userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
    		
    		/**
    		 * UserShop
    		 */
    		$UserShop = M("UserShop");
    		$userloginedrecordUserShop = $UserShop->find($userloginid);
    		if (!empty($userloginedrecordUserShop)) {
    			$this->assign('userloginedrecordUserShop',$userloginedrecordUserShop);
    		}
    		
    		/**
    		 * 
    		 */
    		$recordSchoolInfo = i_school_domain();
    		$this->assign('schoolname',$recordSchoolInfo['school']);
    	} else {
    		redirect('/user/notlogin', 0, '你还没有登录呢...');
    	}
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
    	$userloginid = session('userloginid');
    	$this->assign('title','小店设置 - 买卖');

    	/**
    	 * UserShop
    	 */
    	$UserShop = M("UserShop");
    	$userloginedrecordUserShop = $UserShop->find($userloginid);
    	if (empty($userloginedrecordUserShop)) {
    		redirect('/mall/', 3, '你还没有自己的小店呢...');
    	}
    	$this->assign('recordUserShop', $userloginedrecordUserShop);

    	$UserInfo = M("UserInfo");
    	$recordUserInfo = $UserInfo->find($userloginid);
    	$this->assign('recordUserInfo',$recordUserInfo);

    	/**
    	 * shop catogory
    	 */
    	$RecordCommoditycategory = M("RecordCommoditycategory");
    	$resultRecordCommoditycategory = $RecordCommoditycategory->where("parent_id = '0' AND shop_id = '0'")->select();
    	$this->assign('resultRecordCommoditycategory',$resultRecordCommoditycategory);

    	if ($this->isPost()) {
    		$address = trim(addslashes(htmlspecialchars(strip_tags($_POST["address"]))));
    		$imww = trim(addslashes(htmlspecialchars(strip_tags($_POST["imww"]))));
    		$shopcategory = 1;
    		$dataUserShop = array(
    			'uid' => $userloginid,
    			'category' => $shopcategory,
    			'imww' => $imww,
    			'address' => $address
    		);
    		$isUserShopExist = $UserShop->find($userloginid);
    		if ($isUserShopExist) {
    		    $UserShop->save($dataUserShop);
    		} else {
    			$UserShop->add($dataUserShop);
    		}
    		redirect('/mallset', 1, '修改成功...');
    	}
    	$this->display();
    }

    public function logo()
    {
    	$userloginid = session('userloginid');
    	$this->assign('title','小店logo修改 - 买卖');
    	$UserAlbum = M("UserAlbum");
    	
    	/**
    	 * UserShop
    	 */
    	$UserShop = M("UserShop");
    	$userloginedrecordUserShop = $UserShop->find($userloginid);
    	if (empty($userloginedrecordUserShop)) {
    		redirect('/mall/', 3, '你还没有自己的小店呢...');
    	}
    	$this->assign('recordUserShop', $userloginedrecordUserShop);
    	
    	if ($this->isPost()) {

    		/**
    		 * album default size controll
    		 */
    		$totalAlbumSize = $UserAlbum->where("uid = $userloginid")->sum('size');
    		$UserLogin = M("UserLogin");
    		$recordUserLogin = $UserLogin->find($userloginid);
    		$userLevel = i_degree($recordUserLogin['active']);
    		$totalAlbumDefaultSize = i_configure_album_size($userLevel);
    		if ($totalAlbumSize >= $totalAlbumDefaultSize) {
    			$this->ajaxReturn(0,'相册容量不够了,请联系我帮圈圈扩容','error');
    		}

    		if (!empty($_FILES)) {
    			if ($_FILES["uploadlogo"]["error"] > 0) {
    				$this->ajaxReturn(0,'上传图片失败, info'.$_FILES["uploadlogo"]["error"],'error');
    			} else {
    				$imageOldName = $_FILES["uploadlogo"]["name"];
    				$imageType = $_FILES["uploadlogo"]["type"];
    				$imageType = trim($imageType);
    				$imageSize = $_FILES["uploadlogo"]["size"];
    				$imageTmpName = $_FILES["uploadlogo"]["tmp_name"];
    			}

    			/**
    			 * $tempRealSize = getimagesize($_FILES["uploadlogo"]["tmp_name"]);
    			 * $logoRealWidth = $tempRealSize['0'];
    			 * $logoRealHeight = $tempRealSize['1'];
    			 */
    			if ($imageSize > 3670016) {
    				$this->ajaxReturn(0,'上传图片太大, 最大能上传单张 3.5MB','error');
    			}  else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {
    					
    				/**
    				 * storage in upyun
    				 */
    				Vendor('Ihelpoo.Upyun');
    				$upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
    				$fh = fopen($imageTmpName, 'rb');
    				$shoplogoname = 'logo'.$userloginid.time().'.jpg';
    				$storageTempFilename = '/shop/'.$userloginid.'/'.$shoplogoname;
    				$rsp = $upyun->writeFile($storageTempFilename, $fh, True);
    				fclose($fh);
    				$imageStorageUrl = image_storage_url();
    				$newfilepath = $imageStorageUrl.$storageTempFilename;

    				$opts = array(
    				UpYun::X_GMKERL_TYPE    => 'fix_max',
    				UpYun::X_GMKERL_VALUE   => 150,
    				UpYun::X_GMKERL_QUALITY => 95,
    				UpYun::X_GMKERL_UNSHARP => True
    				);
    				$fh = fopen($imageTmpName, 'rb');
    				$storageThumbTempFilename = '/shop/'.$userloginid.'/thumb_'.$shoplogoname;
    				$rsp = $upyun->writeFile($storageThumbTempFilename, $fh, True, $opts);
    				fclose($fh);
    					

    				/**
    				 * insert into i_user_album
    				 */
    				$newAlbumIconData = array(
        				'uid' => $userloginid,
        				'type' => 3,
        				'url' => $newfilepath,
        				'size' => $imageSize,
        				'time' => time()
    				);
    				$UserAlbum->add($newAlbumIconData);

    				/**
    				 * update i_user_shop
    				 */
    				$newUserShopData = array(
        				'uid' => $userloginid,
        				'shop_banner' => $shoplogoname
    				);
    				$UserShop->save($newUserShopData);

    				/**
    				 * ajax return
    				 */
    				$this->ajaxReturn($newfilepath,'上传成功','uploaded');
    			} else {
    				$this->ajaxReturn(0,'上传图片格式错误, 目前仅支持.jpg .png .gif','error');
    			}
    		}
    	}
    	$this->display();
    }


    public function cate()
    {
    	$userloginid = session('userloginid');
    	$this->assign('title','分类管理 - 买卖');
    	$RecordCommoditycategory = M("RecordCommoditycategory");
    	
    	/**
    	 * UserShop
    	 */
    	$UserShop = M("UserShop");
    	$userloginedrecordUserShop = $UserShop->find($userloginid);
    	if (empty($userloginedrecordUserShop)) {
    		redirect('/mall/', 3, '你还没有自己的小店呢...');
    	}
    	
    	/**
    	 * delete category
    	 */
    	if (!empty($_GET['suredel'])) {
    		$deleteCategoryId = (int)$_GET['suredel'];
    		$RecordCommodity = M("RecordCommodity");
    		$isHasCommodity = $RecordCommodity->where("category_id = $deleteCategoryId")->find();
    		if (!empty($isHasCommodity)) {
    			redirect('/mallset/cate', 3, '该分类下有商品, 删除失败...');
    		}
    		$deleteRecordCommoditycategory = $RecordCommoditycategory->where("cate_id = $deleteCategoryId AND shop_id = $userloginid")->find();
    		if (!empty($deleteRecordCommoditycategory['cate_id'])) {
    			$deleteParentRecordCommoditycategory = $RecordCommoditycategory->where("parent_id = $deleteRecordCommoditycategory[cate_id]")->find();
    			if (!empty($deleteParentRecordCommoditycategory['cate_id'])) {
    				redirect('/mallset/cate', 3, '该分类存在子分类, 删除失败...');
    			} else {
    				$isDeleteCategoryFlag = $RecordCommoditycategory->where("cate_id = $deleteCategoryId AND shop_id = $userloginid")->delete();
    				if ($isDeleteCategoryFlag) {
    					redirect('/mallset/cate', 1, '删除分类成功...');
    				}
    			}
    		}
    		redirect('/mallset/cate', 3, '删除出错啦:(...');
    	}

    	/**
    	 * category select
    	 */
    	$resultRecordCommoditycategory = $RecordCommoditycategory->where("shop_id = $userloginid AND parent_id = 0")->select();
    	$this->assign('resultRecordCommoditycategory',$resultRecordCommoditycategory);

    	/**
    	 * my category
    	 */
    	$resultRecordCommoditycategory = $RecordCommoditycategory->where("shop_id = $userloginid AND parent_id = 0")->select();
    	foreach ($resultRecordCommoditycategory as $parentcategory) {
    		$childrenRecordCommoditycategory = $RecordCommoditycategory->where("parent_id = $parentcategory[cate_id]")->select();
    		$childrenCategory = NULL;
    		if (!empty($childrenRecordCommoditycategory)) {
    			foreach ($childrenRecordCommoditycategory as $childcategory) {
	    			$childrenCategory[] = array(
	    				'id' => $childcategory['cate_id'],
    					'name' => $childcategory['cate_name'],
	    			);
    			}
    		}
    		$myCategoryArray[] = array(
    			'id' => $parentcategory['cate_id'],
    			'name' => $parentcategory['cate_name'],
    			'children' => $childrenCategory,
    		);
    	}
    	$this->assign('myCategoryArray',$myCategoryArray);

    	/**
    	 * public category
    	 */
    	$parentRecordCommoditycategory = $RecordCommoditycategory->where("shop_id = 0 AND parent_id = 0")->select();
    	foreach ($parentRecordCommoditycategory as $parentcategory) {
    		$childrenRecordCommoditycategory = $RecordCommoditycategory->where("parent_id = $parentcategory[cate_id]")->select();
    		$childrenCategory = NULL;
    		if (!empty($childrenRecordCommoditycategory)) {
    			foreach ($childrenRecordCommoditycategory as $childcategory) {
	    			$childrenCategory[] = array(
	    				'id' => $childcategory['cate_id'],
    					'name' => $childcategory['cate_name'],
	    			);
    			}
    		}
    		$publicCategoryArray[] = array(
    			'id' => $parentcategory['cate_id'],
    			'name' => $parentcategory['cate_name'],
    			'children' => $childrenCategory,
    		);
    	}
    	$this->assign('publicCategoryArray',$publicCategoryArray);

    	/**
    	 * add category
    	 */
    	if ($this->isPost()) {
    		$newcategory = trim(addslashes(htmlspecialchars(strip_tags($_POST["newcategory"]))));
    		$parentcategory = (int)trim(htmlspecialchars(strip_tags($_POST["parentcategory"])));
    		if (!empty($newcategory)) {
    			$isCategoryExist = $RecordCommoditycategory->where("(cate_name = '$newcategory' AND shop_id = 0) OR (cate_name = '$newcategory' AND shop_id = $userloginid)")->find();
    			if (!empty($isCategoryExist)) {
    				redirect('/mallset/cate', 3, '分类已经存在...');
    			}
    			$dataRecordCommoditycategory = array(
	    			'cate_id' => '',
	    			'shop_id' => $userloginid,
	    			'cate_name' => $newcategory,
	    			'parent_id' => $parentcategory,
	    			'time' => time()
    			);
    			$RecordCommoditycategory->add($dataRecordCommoditycategory);
    			redirect('/mallset/cate', 1, '添加分类成功...');
    		}
    	}
    	$this->display();
    }

    public function add()
    {
    	$userloginid = session('userloginid');
    	$this->assign('title','发布商品 - 买卖');
    	$recordSchoolInfo = i_school_domain();

    	/**
    	 * UserShop
    	 */
    	$UserShop = M("UserShop");
    	$userloginedrecordUserShop = $UserShop->find($userloginid);
    	if (empty($userloginedrecordUserShop)) {
    		redirect('/mall/explanation', 3, '你还没有自己的小店呢，点击同意开通后就能发布商品了...');
    	}
    	if ($userloginedrecordUserShop['status'] != 2) {
    		redirect('/mall/', 3, '由于多次违规，你的小店在重新审核中，暂时不能发布商品...');
    	}
    	$this->assign('recordUserShop', $userloginedrecordUserShop);
    	
    	/**
    	 * select category
    	 */
    	$RecordCommoditycategory = M("RecordCommoditycategory");
    	$resultRecordCommoditycategory = $RecordCommoditycategory->where("(shop_id = $userloginid OR shop_id = 0) AND parent_id = 0")->select();
    	foreach ($resultRecordCommoditycategory as $parentcategory) {
    		$childrenRecordCommoditycategory = $RecordCommoditycategory->where("parent_id = $parentcategory[cate_id]")->select();
    		$childrenCategory = NULL;
    		if (!empty($childrenRecordCommoditycategory)) {
    			foreach ($childrenRecordCommoditycategory as $childcategory) {
	    			$childrenCategory[] = array(
	    				'id' => $childcategory['cate_id'],
    					'name' => $childcategory['cate_name'],
	    			);
    			}
    		}
    		$selectCategoryArray[] = array(
    			'id' => $parentcategory['cate_id'],
    			'name' => $parentcategory['cate_name'],
    			'children' => $childrenCategory,
    		);
    	}
    	$this->assign('selectCategoryArray',$selectCategoryArray);

    	$commodityId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$RecordCommodity = M("RecordCommodity");
    	$UserAlbum = M("UserAlbum");
    	if (!empty($commodityId)) {
	    	$resultRecordCommodity = $RecordCommodity->find($commodityId);
	    	$this->assign('resultRecordCommodity',$resultRecordCommodity);
	    	if ($resultRecordCommodity['shopid'] != $userloginid) {
	    		redirect('/mall/', 3, '你没有编辑该商品的权限...');
	    	}
    	}
    	if ($this->isPost()) {

    		/**
    		 * album default size controll
    		 */
    		$totalAlbumSize = $UserAlbum->where("uid = $userloginid")->sum('size');
    		$UserLogin = M("UserLogin");
    		$recordUserLogin = $UserLogin->find($userloginid);
    		$userLevel = i_degree($recordUserLogin['active']);
    		$totalAlbumDefaultSize = i_configure_album_size($userLevel);
    		if ($totalAlbumSize >= $totalAlbumDefaultSize) {
    			$this->ajaxReturn(0,'相册容量不够了,请联系我帮圈圈扩容','error');
    		}

    		/**
        	 * upload image file
        	 */
    		if (!empty($_FILES["uploadedimg"])) {
        		if ($_FILES["uploadedimg"]["error"] > 0) {
        			$this->ajaxReturn(0,'上传图片失败, info'.$_FILES["uploadedimg"]["error"],'error');
        		} else {
        			$imageOldName = $_FILES["uploadedimg"]["name"];
        			$imageType = $_FILES["uploadedimg"]["type"];
        			$imageType = trim($imageType);
        			$imageSize = $_FILES["uploadedimg"]["size"];
        			$imageTmpName = $_FILES["uploadedimg"]["tmp_name"];
        		}

        		$tempRealSize = getimagesize($_FILES["uploadedimg"]["tmp_name"]);
    			$imageRealWidth = $tempRealSize['0'];
    			$imageRealHeight = $tempRealSize['1'];
    			if ($imageRealWidth < 310 || $imageRealHeight < 310 ) {
    				$this->ajaxReturn(0,'上传图片太小了，尺寸必须大于310px * 310px','error');
    			}
        		if ($imageSize > 3670016) {
        			$this->ajaxReturn(0,'上传图片太大, 最大能上传单张 3.5MB','error');
        		}  else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {
        			 
        			/**
        			 * storage in upyun
        			 */
        			Vendor('Ihelpoo.Upyun');
        			$upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
        			$fh = fopen($imageTmpName, 'rb');
        			$shopitemname = 'goods'.time().'.jpg';
        			$storageTempFilename = '/shop/'.$userloginid.'/'.$shopitemname;
        			$rsp = $upyun->writeFile($storageTempFilename, $fh, True);
        			fclose($fh);
        			$imageStorageUrl = image_storage_url();
        			$newfilepath = $imageStorageUrl.$storageTempFilename;

        			$opts = array(
        			UpYun::X_GMKERL_TYPE    => 'fix_max',
        			UpYun::X_GMKERL_VALUE   => 160,
        			UpYun::X_GMKERL_QUALITY => 95,
        			UpYun::X_GMKERL_UNSHARP => True
        			);
        			$fh = fopen($imageTmpName, 'rb');
        			$storageThumbTempFilename = '/shop/'.$userloginid.'/thumb_'.$shopitemname;
        			$rsp = $upyun->writeFile($storageThumbTempFilename, $fh, True, $opts);
        			fclose($fh);

        			/**
        			 * insert into i_user_album
        			 */
        			$newAlbumIconData = array(
        				'uid' => $userloginid,
        				'type' => 3,
        				'url' => $newfilepath,
        				'size' => $imageSize,
        				'time' => time()
        			);
        			$UserAlbum->add($newAlbumIconData);

        			/**
        			 * ajax return
        			 */
        			$this->ajaxReturn($newfilepath,'上传成功','uploaded');
        		} else {
        			$this->ajaxReturn(0,'上传图片格式错误, 目前仅支持.jpg .png .gif','error');
        		}
        		exit();
        	}

        	/**
        	 *
        	 * insert data into i_record_commodity
        	 */
    		$validate = array(
	    		array('name', 'require', '商品名称不能为空'),
	            array('price', 'number', '价格格式错误'),
	            array('good_nums', 'number', '商品数量格式错误'),
	            array('good_type', 'number', '商品类型格式错误'),
	            array('category_id', 'number', 'category_id 格式错误'),
	            array('detail', 'require', '商品详细不能为空'),
	            array('image', 'require', '商品缩略图不能为空')
    		);
    		$RecordCommodity->setProperty("_validate", $validate);
    		$result = $RecordCommodity->create();
    		if (!$result) {
    			$errorinfo = $RecordCommodity->getError();
    			$this->ajaxReturn(0,$errorinfo,'error');
    		} else {
    			$name = trim(addslashes(htmlspecialchars(strip_tags($_POST["name"]))));
    			$price = (int)trim(htmlspecialchars(strip_tags($_POST["price"])));
    			$good_nums = (int)trim(addslashes(htmlspecialchars(strip_tags($_POST["good_nums"]))));
    			$good_type = (int)trim(addslashes(htmlspecialchars(strip_tags($_POST["good_type"]))));
    			$category_id = (int)trim(htmlspecialchars(strip_tags($_POST["category_id"])));
    			$cid = (int)trim(htmlspecialchars(strip_tags($_POST["cid"])));
    			$detail = trim(addslashes($_POST["detail"]));
    			$image = trim(addslashes(htmlspecialchars(strip_tags($_POST["image"]))));
    			if (!empty($_POST["buyway1"])) {
    				$buyway = ';1';
    			}
    			if (!empty($_POST["buyway2"])) {
    				$buyway .= ';2';
    			}
    			if (!empty($_POST["buyway3"])) {
    				$buyway .= ';3';
    			}
    			$newRecordCommodity = array(
    				'cid' => '',
    				'shopid' => $userloginid,
    				'name' => $name,
    				'price' => $price,
    				'good_nums' => $good_nums,
    				'good_type' => $good_type,
    				'buyway' => $buyway,
    				'detail' => $detail,
    				'image' => $image,
    				'category_id' => $category_id,
    				'status' => 1,
    				'school_id' => $recordSchoolInfo['id']
    			);
    			if (!empty($cid)) {
    				$newRecordCommodity['cid'] = $cid;
    				$newRecordCommodity['update_ti'] = time();
    				$RecordCommodity->save($newRecordCommodity);
    			} else {
    				$newRecordCommodity['time'] = time();
    				$newRecordCommodity['update_ti'] = time();
    				$cid = $RecordCommodity->add($newRecordCommodity);
    				
    				/**
		             * add default dynamic record.
		             */
		            $recordDynamicContent = "我刚刚在本小店发布了新商品“".$name."”, 快来看看吧 :) <a href='".__ROOT__."/mall/item/".$cid."' target='_blank'><span class='post_link'></span></a>";
		            $RecordSay = M("RecordSay");
		            $RecordDynamic = M("RecordDynamic");
		            $newRecordSayData = array(
		            	'uid' => $userloginid,
		            	'say_type' => 2,
		            	'content' => $recordDynamicContent,
		            	'time' => time(),
		            	'from' => '买卖动态'
		            );
		            $newRecordSayId = $RecordSay->add($newRecordSayData);
		            $newRecordDynamicData = array(
		            	'sid' => $newRecordSayId,
		            	'type' => 'newgoods',
		            );
		            $RecordDynamic->add($newRecordDynamicData);
		            
		            /**
		             * update commodity_co
		             */
		            $updateCommodityCount = array(
	    				'uid' => $userloginid,
	    				'commodity_co' => $userloginedrecordUserShop['commodity_co'] + 1,
		            );
		            $UserShop->save($updateCommodityCount);
    			}
    		}
    		redirect('/mallset/add?succ='.$cid, 0, '发布商品成功...');
    		$this->ajaxReturn($cid,'发布商品成功...','yes');
    	}
    	$this->display();
    }

    public function commodity()
    {
    	$userloginid = session('userloginid');
    	$this->assign('title','商品管理 - 买卖');
    	$RecordCommodity = M("RecordCommodity");
    	$recordSchoolInfo = i_school_domain();

    	/**
    	 * UserShop
    	 */
    	$UserShop = M("UserShop");
    	$userloginedrecordUserShop = $UserShop->find($userloginid);
    	if (empty($userloginedrecordUserShop)) {
    		redirect('/mall/', 3, '你还没有自己的小店呢...');
    	}
    	 
    	/**
    	 * delete commodity
    	 * need password
    	 */
    	if (!empty($_GET['suredel'])) {
    		$commodityid = (int)$_GET['suredel'];
    		$deleteRecord = $RecordCommodity->find($commodityid);
    	    if (!$deleteRecord['cid']) {
    			redirect('/mallset/commodity', 3, '删除出错');
    		}

    		/**
    		 * delete i_record_commodity
    		 */
    		$RecordCommodity->where("cid = $commodityid")->delete();

    		/**
    		 * delete i_record_commodityassess
    		 */
    		$RecordCommodityassess = M("RecordCommodityassess");
    		$RecordCommodityassess->where("cid = $commodityid")->delete();

    		
    		/**
    		 * update commodity_co
    		 */
    		$updateCommodityCount = array(
	    		'uid' => $userloginid,
	    		'commodity_co' => $userloginedrecordUserShop['commodity_co'] - 1,
    		);
    		$UserShop->save($updateCommodityCount);
		            
    		redirect('/mallset/commodity', 3, '删除成功...');

    		/**
    		 * delete storage image
    		$url = $deleteRecord['image'];
    		$delPath = substr($url, 39);
    		$thumburl = str_ireplace("goods", "thumb_goods", $url);
    		$delThumbpath = substr($thumburl, 39);
    		$storage = new SaeStorage();
    		$delFlag = $storage->delete('public',$delPath);
    		$delThumbFlag = $storage->delete('public',$delThumbpath);
    		if ($delFlag && $delThumbFlag) {
    			redirect('/mallset/commodity', 3, '删除成功...');
    		} else {
    			redirect('/mallset/commodity', 3, '删除成功...未找到商品图片');
    		}
    		*/
    	}

    	$page = i_page_get_num();
    	$count = 15;
        $offset = $page * $count;
    	$resultsRecordCommodity = $RecordCommodity->where("shopid = $userloginid AND school_id = $recordSchoolInfo[id]")
    	->field('detail',true)
    	->order("time DESC")->limit($offset,$count)->select();
    	$this->assign('resultsRecordCommodity',$resultsRecordCommodity);

        /**
         * page link
         */
        $totalCommodityNums = $RecordCommodity->where("shopid = $userloginid")->count();

        /**
         * update shop commodity nums in i_user_shop commodity_co
         */
        $updateUserShopCommodityco = array(
        	'uid' => $userloginid,
        	'commodity_co' => $totalCommodityNums
        );
        $UserShop->save($updateUserShopCommodityco);
        $this->assign('totalRecordNums',$totalCommodityNums);
        $totalPages = ceil($totalCommodityNums / $count);
        $this->assign('totalPages',$totalPages);
        $this->display();
    }

    /**
     *
     * Ajax action, build for kindediter
     * upload image file
     */
    public function addupload()
    {
    	$userloginid = session('userloginid');
    	$UserAlbum = M("UserAlbum");
    	if ($this->isPost()) {

    		/**
    		 * album default size controll
    		 */
    		$totalAlbumSize = $UserAlbum->where("uid = $userloginid")->sum('size');
    		$UserLogin = M("UserLogin");
    		$recordUserLogin = $UserLogin->find($userloginid);
    		$userLevel = i_degree($recordUserLogin['active']);
    		$totalAlbumDefaultSize = i_configure_album_size($userLevel);
    		if ($totalAlbumSize >= $totalAlbumDefaultSize) {
    			$this->ajaxReturn(0,'相册容量不够了,请联系我帮圈圈扩容','error');
    		}

    		if (!empty($_FILES["imgFile"])) {
        		if ($_FILES["imgFile"]["error"] > 0) {
        			$this->ajaxReturn(0,'上传图片失败, info'.$_FILES["imgFile"]["error"],'error');
        			$data['message'] = '上传图片失败, info'.$_FILES["imgFile"]["error"];
        			$data['error'] = 1;
        			$this->ajaxReturn($data,'JSON');
        		} else {
        			$imageOldName = $_FILES["imgFile"]["name"];
        			$imageType = $_FILES["imgFile"]["type"];
        			$imageType = trim($imageType);
        			$imageSize = $_FILES["imgFile"]["size"];
        			$imageTmpName = $_FILES["imgFile"]["tmp_name"];
        		}

        		$tempRealSize = getimagesize($_FILES["imgFile"]["tmp_name"]);
    			$imageRealWidth = $tempRealSize['0'];
    			$imageRealHeight = $tempRealSize['1'];
    			if ($imageRealWidth < 10 || $imageRealHeight < 10 ) {
    				$data['message'] = '上传图片也太小了吧';
        			$data['error'] = 1;
        			$this->ajaxReturn($data,'JSON');
    			}
        		if ($imageSize > 3670016) {
        			$data['message'] = '上传图片太大, 最大能上传单张 3.5MB';
        			$data['error'] = 1;
        			$this->ajaxReturn($data,'JSON');
        		}  else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {
        			
        			/**
        			 * storage in upyun
        			 */
        			Vendor('Ihelpoo.Upyun');
        			$upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
        			$fh = fopen($imageTmpName, 'rb');
        			$shopitemname = 'goodscontent'.time().'.jpg';
        			$storageTempFilename = '/shop/'.$userloginid.'/'.$shopitemname;
        			$rsp = $upyun->writeFile($storageTempFilename, $fh, True);
        			fclose($fh);
        			$imageStorageUrl = image_storage_url();
        			$newfilepath = $imageStorageUrl.$storageTempFilename;

        			$opts = array(
        			UpYun::X_GMKERL_TYPE    => 'fix_max',
        			UpYun::X_GMKERL_VALUE   => 150,
        			UpYun::X_GMKERL_QUALITY => 95,
        			UpYun::X_GMKERL_UNSHARP => True
        			);
        			$fh = fopen($imageTmpName, 'rb');
        			$storageThumbTempFilename = '/shop/'.$userloginid.'/thumb_'.$shopitemname;
        			$rsp = $upyun->writeFile($storageThumbTempFilename, $fh, True, $opts);
        			fclose($fh);
        			 
        			/**
        			 * insert into i_user_album
        			 */
        			$newAlbumIconData = array(
        				'uid' => $userloginid,
        				'type' => 3,
        				'url' => $newfilepath,
        				'size' => $imageSize,
        				'time' => time()
        			);
        			$UserAlbum->add($newAlbumIconData);

        			/**
        			 * ajax return
        			 */
        			$data['url'] = $newfilepath;
        			$data['error'] = 0;
        			$this->ajaxReturn($data,'JSON');
        		} else {
        			$data['message'] = '上传图片格式错误, 目前仅支持.jpg .png .gif';
        			$data['error'] = 1;
        			$this->ajaxReturn($data,'JSON');
        		}
        		exit();
        	}
        }
    }

    /**
     *
     *
     * shop part
     */
    public function buynow()
    {
    	$userloginid = session('userloginid');

    	/**
    	 * add new delivery address data
    	 */
    	if (!empty($_POST['newaddress'])) {
    		$newaddress = trim(addslashes(htmlspecialchars(strip_tags($_POST['newaddress']))));

    		$UserDeliveryaddress = M("UserDeliveryaddress");
    		$newDeliveryAddressAddredd = array(
    			'id' => '',
    			'uid' => $userloginid,
    			'address' => $newaddress,
    			'is_use' => 0,
    			'time' => time()
    		);
    		$addUserDeliveryaddressId = $UserDeliveryaddress->add($newDeliveryAddressAddredd);
    		if ($addUserDeliveryaddressId) {
    			$this->ajaxReturn($addUserDeliveryaddressId,$newaddress,'ok');
    		}
    	}

    	$commodityId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$buynums = (int)htmlspecialchars(trim($_GET["_URL_"][3]));
    	if (empty($buynums) || $buynums < 0) {
    		$buynums = 1;
    	}
    	if ($commodityId <= 0) {
    		redirect('/mall', 3, '你要购买的商品不存在...3秒后返回买卖首页');
    	}
    	$RecordCommodity = M("RecordCommodity");
    	$resultRecordCommodity = $RecordCommodity->where("cid = $commodityId")->field('detail',true)->find();
    	if (!$resultRecordCommodity['cid']) {
    		redirect('/mall', 3, '你要购买的商品不存在...3秒后返回买卖首页');
    	}
    	
    	if ($resultRecordCommodity['shopid'] == $userloginid) {
    		redirect('/mall/item/'.$resultRecordCommodity['cid'], 3, '自己不能买自己的东西...3秒后返回');
    	}
    	
    	$this->assign('title',"确认购买信息 ".$resultRecordCommodity['name']);
    	$this->assign('resultRecordCommodity',$resultRecordCommodity);

    	if ($resultRecordCommodity['good_nums'] < $buynums) {
    		redirect('/mall/item/'.$resultRecordCommodity['cid'], 3, '购买的商品数量超过了小店能提供的范围...3秒后返回');
    	}
    	
    	/**
    	 * delivery address
    	 */
    	$UserDeliveryaddress = M("UserDeliveryaddress");
    	$resultsUserDeliveryaddress = $UserDeliveryaddress->where("uid = $userloginid")->select();
    	if (empty($resultsUserDeliveryaddress)) {
	    	$UserInfo = M("UserInfo");
	    	$recordUserInfo = $UserInfo->where("uid = $userloginid")->field('dormitory_op,realname,mobile')->find();
	    	if ($recordUserInfo['dormitory_op']) {
	    		$OpDormitory = M("OpDormitory");
	    		$recordOpDormitory = $OpDormitory->where("id = $recordUserInfo[dormitory_op]")->find();
	    	}
	    	$tempDeliveryaddress = $recordOpDormitory['name'].' '.$recordUserInfo['realname'].' 手机:'.$recordUserInfo['mobile'];
	    	$this->assign('tempDeliveryaddress',$tempDeliveryaddress);
    	} else {
    		$this->assign('resultsUserDeliveryaddress',$resultsUserDeliveryaddress);
    	}
    	$this->assign('buynums',$buynums);
    	$this->display();
    }

    public function buypay()
    {
    	$userloginid = session('userloginid');
    	$RecordCommodity = M("RecordCommodity");
    	$RecordCommodityassess = M("RecordCommodityassess");
    	$UserLogin = M("UserLogin");
    	$UserShop = M("UserShop");
    	
    	if ($this->isPost()) {
    		if (!empty($_POST['cid']) && !empty($_POST['deliveryaddressid'])) {
	    		$commodityId = (int)trim(htmlspecialchars(strip_tags($_POST['cid'])));
	    		$buynums = (int)trim(htmlspecialchars(strip_tags($_POST['buynums'])));
	    		$deliveryAddressId = (int)trim(htmlspecialchars(strip_tags($_POST['deliveryaddressid'])));
	    		$remarks = trim(htmlspecialchars(strip_tags($_POST['remarks'])));
	    		$usecoins = trim(htmlspecialchars(strip_tags($_POST['usecoins'])));

	    		/**
	    		 *
	    		 * insert into shop user commodity assess
	    		 */
	    		$resultRecordCommodity = $RecordCommodity->where("cid = $commodityId")->field('detail',true)->find();
	    		if (!$resultRecordCommodity['cid']) {
	    			$this->ajaxReturn(0,'你要购买的商品不存在...','wrong');
	    		}

	    		// more here ...
	    		$newRecordCommodityassess = array(
	    			'id' => '',
	    			'uid' => $userloginid,
	    			'cid' => $resultRecordCommodity['cid'],
	    			'buynums' => $buynums,
	    			'buyprice' => $resultRecordCommodity['price'],
	    			'remarks' => $remarks,
	    			'buyaddressid' => $deliveryAddressId,
	    			'status' => 0,
	    			'start_ti' => time()
	    		);
	    		$newRecordCommodityassessId = $RecordCommodityassess->add($newRecordCommodityassess);
	    		
	    		/**
	    		 * update sales_co
	    		 */
	    		$updateSalesCount = array(
	    			'cid' => $resultRecordCommodity['cid'],
	    			'good_nums' => $resultRecordCommodity['good_nums'] - 1,
	    			'sales_co' => $resultRecordCommodity['sales_co'] + 1,
	    		);
	    		$RecordCommodity->save($updateSalesCount);
	    		
	    		/**
	    		 * update usershop sales_co
	    		 */
	    		$recordUserShop = $UserShop->find($resultRecordCommodity['shopid']);
	    		$addUserShopRevenueCount = $buynums * $resultRecordCommodity['price'];
	    		$updateShopSalesCount = array(
	    			'uid' => $recordUserShop['uid'],
	    			'sales_co' => $recordUserShop['sales_co'] + 1,
	    			'revenue_co' => $recordUserShop['revenue_co'] + $addUserShopRevenueCount,
	    		);
	    		$UserShop->save($updateShopSalesCount);
	    		
	    		/**
	             * send system message.
	             */
	            $MsgSystem = M("MsgSystem");
	            $msgContent = $coinsUserLogin['nickname']." 要购买你的商品，快来看看，确定是否开始交易!";
	            $msgData = array(
                    'id' => '',
                    'uid' => $resultRecordCommodity['shopid'],
                    'type' => 'mallset/buypay',
                    'url_id' => $resultRecordCommodity['cid'],
                    'content' => $msgContent,
                    'time' => time(),
                    'deliver' => 0,
    			);
	            $MsgSystem->add($msgData);
	            
	            /**
	             * send mail
	             */
	            Vendor('Ihelpoo.Email');
	            $emailObj = new Email();
	            $recordToUserLogin = $UserLogin->find($resultRecordCommodity['shopid']);
	            if (!empty($recordToUserLogin['email'])) {
	            	$emailObj->mallInfo($recordToUserLogin['email'], $recordToUserLogin['nickname'], $msgContent);
	            }
    				
    			$url = 'mallset/buypay/'.$newRecordCommodityassessId;
    			$this->ajaxReturn($url,'成功','ok');
    		}

    		if (!empty($_POST['buyway'])) {
    			$caid = (int)trim(htmlspecialchars(strip_tags($_POST['caid'])));
    			$buyway = (int)trim(htmlspecialchars(strip_tags($_POST['buyway'])));
    			if ($buyway <= 0) {
    				$this->ajaxReturn(0,'出错啦','wrong');
    			} else {
    				$updateRecordCommodityassess = array(
    					'id' => $caid,
    					'buyway' => $buyway,
    					'status' => 1,
	    				'start_ti' => time()
    				);
    				$RecordCommodityassess->save($updateRecordCommodityassess);
    				$url = 'mallset/buypay/'.$caid;
    				$this->ajaxReturn($url,'成功','ok');
    			}
    		}
    	}

    	$commodityassessId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if (empty($commodityassessId) || $commodityassessId < 0) {
    		$commodityassessId = 1;
    	}
    	$showRecordCommodityassess = $RecordCommodityassess->find($commodityassessId);
    	if (!$showRecordCommodityassess['cid']) {
    		redirect('/mall', 3, '购买出错(错误代码:buypay1)...3秒后返回买卖首页');
    	}
    	if ($showRecordCommodityassess['uid'] != $userloginid) {
    		redirect('/mall', 3, '购买出错(错误代码:buypay2)...3秒后返回买卖首页');
    	}
    	$showRecordCommodity = $RecordCommodity->where("cid = $showRecordCommodityassess[cid]")->field('detail',true)->find();
    	if (!$showRecordCommodity['cid']) {
    		redirect('/mall', 3, '你要购买的商品不存在...3秒后返回买卖首页');
    	}
    	$this->assign('showRecordCommodity',$showRecordCommodity);
    	$this->assign('showRecordCommodityassess',$showRecordCommodityassess);
    	
    	/**
    	 * shop user info
    	 */
    	$UserInfo = M("UserInfo");
    	$shoperUserInfo = $UserInfo->find($showRecordCommodity['shopid']);
    	$shoperUserShop = $UserShop->find($showRecordCommodity['shopid']);
    	$this->assign('shoperUserInfo',$shoperUserInfo);
    	$this->assign('shoperUserShop',$shoperUserShop);
    	$this->assign('title', "确认交易(付款)方式 ".$showRecordCommodity['name']);
    	$this->display();
    }

    public function buysure()
    {
    	$userloginid = session('userloginid');
    	$RecordCommodity = M("RecordCommodity");
    	$RecordCommodityassess = M("RecordCommodityassess");

    	if ($this->isPost()) {
    		if (!empty($_POST['caid'])) {
    			$caid = (int)trim(htmlspecialchars(strip_tags($_POST['caid'])));
    			if ($caid <= 0) {
    				$this->ajaxReturn(0,'出错啦','wrong');
    			} else {
    				$updateRecordCommodityassess = array(
    					'id' => $caid,
    					'status' => 3,
    					'end_ti' => time(),
    				);
    				$RecordCommodityassess->save($updateRecordCommodityassess);
    				$url = 'mallset/buysure/'.$caid;
    				$this->ajaxReturn($url,'成功','ok');
    			}
    		}
    	}

    	$commodityassessId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if (empty($commodityassessId) || $commodityassessId < 0) {
    		$commodityassessId = 1;
    	}
    	$showRecordCommodityassess = $RecordCommodityassess->find($commodityassessId);
    	if (!$showRecordCommodityassess['cid']) {
    		redirect('/mall', 3, '购买出错(错误代码:buypay1)...3秒后返回买卖首页');
    	}
    	if ($showRecordCommodityassess['uid'] != $userloginid) {
    		redirect('/mall', 3, '购买出错(错误代码:buypay2)...3秒后返回买卖首页');
    	}
    	$showRecordCommodity = $RecordCommodity->where("cid = $showRecordCommodityassess[cid]")->field('detail',true)->find();
    	if (!$showRecordCommodity['cid']) {
    		redirect('/mall', 3, '你要购买的商品不存在...3秒后返回买卖首页');
    	}
    	$this->assign('showRecordCommodity',$showRecordCommodity);
    	$this->assign('showRecordCommodityassess',$showRecordCommodityassess);

    	/**
    	 * shop user info
    	 */
    	$UserInfo = M("UserInfo");
    	$UserShop = M("UserShop");
    	$shoperUserInfo = $UserInfo->find($showRecordCommodity['shopid']);
    	$shoperUserShop = $UserShop->find($showRecordCommodity['shopid']);
    	$this->assign('shoperUserInfo',$shoperUserInfo);
    	$this->assign('shoperUserShop',$shoperUserShop);
    	$this->assign('title', "确认收货 ".$showRecordCommodity['name']);
    	$this->display();
    }

    public function buyassess()
    {
    	$userloginid = session('userloginid');
    	$RecordCommodity = M("RecordCommodity");
    	$RecordCommodityassess = M("RecordCommodityassess");
    	$UserShop = M("UserShop");

    	if ($this->isPost()) {
    		if (!empty($_POST['caid'])) {
    			$caid = (int)trim(htmlspecialchars(strip_tags($_POST['caid'])));
    			$starresult = (int)trim(htmlspecialchars(strip_tags($_POST['starresult'])));
    			$anonymous = (int)trim(htmlspecialchars(strip_tags($_POST['anonymous'])));
    			$assesstextarea = trim(addslashes(htmlspecialchars(strip_tags($_POST['assesstextarea']))));
    			
    			/**
    			 * update assess_co
    			 */
    			$recordRecordCommodityassess = $RecordCommodityassess->find($caid);
    			$recordRecordCommodity = $RecordCommodity->where("cid = $recordRecordCommodityassess[cid]")->field('detail',true)->find();
    			if (empty($recordRecordCommodity['cid'])) {
    				$this->ajaxReturn(0,'出错啦','wrong');
    			} else {
    				$updateAssessCount = array(
    					'cid' => $recordRecordCommodity['cid'],
    					'assess_co' => $recordRecordCommodity['assess_co'] + 1,
    				);
    				$RecordCommodity->save($updateAssessCount);
    				
    				/**
    				 * update usershop assess_co
    				 */
    				$recordUserShop = $UserShop->find($recordRecordCommodity['shopid']);
    				$updateShopSalesCount = array(
		    			'uid' => $recordUserShop['uid'],
    				);
    				if ($starresult <= 2) {
    					$updateShopSalesCount['assess_bad'] = $recordUserShop['assess_good'] + 1;
    				} else if ($starresult == 3) {
    					$updateShopSalesCount['assess_middle'] = $recordUserShop['assess_middle'] + 1;
    				} else if ($starresult >= 4) {
    					$updateShopSalesCount['assess_good'] = $recordUserShop['assess_good'] + 1;
    				}
    				$UserShop->save($updateShopSalesCount);
    			}
    			
    			if ($caid <= 0) {
    				$this->ajaxReturn(0,'出错啦','wrong');
    			} else {
    				$updateRecordCommodityassess = array(
    					'id' => $caid,
    					'anonymous' => $anonymous,
    					'score' => $starresult,
    					'content' => $assesstextarea,
    					'assess_ti' => time(),
    					'status' => 5
    				);
    				$RecordCommodityassess->save($updateRecordCommodityassess);
    				$url = 'mallset/buyassess/'.$caid;
    				
	    			/**
		             * add default dynamic record.
		             */
		            $recordDynamicContent = "我买了“".$recordRecordCommodity['name']."”, ".$assesstextarea." :) <a href='".__ROOT__."/mall/item/".$recordRecordCommodity['cid']."' target='_blank'><span class='post_link'></span></a>";
		            $RecordSay = M("RecordSay");
		            $RecordDynamic = M("RecordDynamic");
		            $newRecordSayData = array(
		            	'uid' => $userloginid,
		            	'say_type' => 2,
		            	'content' => $recordDynamicContent,
		            	'time' => time(),
		            	'from' => '买卖动态'
		            );
		            $newRecordSayId = $RecordSay->add($newRecordSayData);
		            $newRecordDynamicData = array(
		            	'sid' => $newRecordSayId,
		            	'type' => 'shoping',
		            );
		            $RecordDynamic->add($newRecordDynamicData);
    				
    				$this->ajaxReturn($url,'成功','ok');
    			}
    		}
    	}

    	$commodityassessId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if (empty($commodityassessId) || $commodityassessId < 0) {
    		$commodityassessId = 1;
    	}
    	$showRecordCommodityassess = $RecordCommodityassess->find($commodityassessId);
    	if (!$showRecordCommodityassess['cid']) {
    		redirect('/mall', 3, '购买出错(错误代码:buypay1)...3秒后返回买卖首页');
    	}
    	if ($showRecordCommodityassess['uid'] != $userloginid) {
    		redirect('/mall', 3, '购买出错(错误代码:buypay2)...3秒后返回买卖首页');
    	}
    	$showRecordCommodity = $RecordCommodity->where("cid = $showRecordCommodityassess[cid]")->field('detail',true)->find();
    	if (!$showRecordCommodity['cid']) {
    		redirect('/mall', 3, '你要购买的商品不存在...3秒后返回买卖首页');
    	}
    	$this->assign('showRecordCommodity',$showRecordCommodity);
    	$this->assign('showRecordCommodityassess',$showRecordCommodityassess);

    	/**
    	 * shop user info
    	 */
    	$UserInfo = M("UserInfo");
    	$shoperUserInfo = $UserInfo->find($showRecordCommodity['shopid']);
    	$shoperUserShop = $UserShop->find($showRecordCommodity['shopid']);
    	$this->assign('shoperUserInfo',$shoperUserInfo);
    	$this->assign('shoperUserShop',$shoperUserShop);
    	$this->assign('title', "评价 ".$showRecordCommodity['name']);
    	$this->display();
    }

    public function deliveryaddress()
    {
    	$userloginid = session('userloginid');
    	$UserDeliveryaddress = M("UserDeliveryaddress");

    	/**
    	 * delete delivery address
    	 */
    	if (!empty($_GET['suredel'])) {
    		$deliveryaddressid = (int)trim(htmlspecialchars(strip_tags($_GET['suredel'])));
    		$deleteRecord = $UserDeliveryaddress->find($deliveryaddressid);
    		if (!$deleteRecord['id']) {
    			redirect('/mallset/deliveryaddress', 3, '删除出错 3秒后返回');
    		}

    		/**
    		 * delete i_record_commodity
    		 */
    		$UserDeliveryaddress->where("id = $deliveryaddressid")->delete();
    		redirect('/mallset/deliveryaddress', 3, '删除成功... 3秒后返回');
    	}

    	/**
    	 * update delivery address
    	 */
    	if (!empty($_POST['deliveryaddressid'])) {
    		$deliveryaddressid = (int)trim(htmlspecialchars(strip_tags($_POST['deliveryaddressid'])));
    		$editdeliveryaddress = trim(addslashes(htmlspecialchars(strip_tags($_POST['editdeliveryaddress']))));

    		$updateDeliveryaddress = array(
    			'id' => $deliveryaddressid,
    			'address' => $editdeliveryaddress,
    			'time' => time()
    		);

    		/**
    		 * update i_record_commodity
    		 */
    		$UserDeliveryaddress->save($updateDeliveryaddress);
    		redirect('/mallset/deliveryaddress', 3, '修改成功... 3秒后返回');
    	}

    	/**
    	 * delivery address
    	 */
    	$resultsUserDeliveryaddress = $UserDeliveryaddress->where("uid = $userloginid")->select();
    	$this->assign('resultsUserDeliveryaddress',$resultsUserDeliveryaddress);
    	$this->display();
    }

    public function seller()
    {
    	$userloginid = session('userloginid');
    	$RecordCommodity = M("RecordCommodity");
    	$this->assign('title',"我的小店订单");
    	$RecordCommodityassess = M("RecordCommodityassess");

    	if ($this->isPost()) {
    		$MsgSystem = M("MsgSystem");
    		$UserLogin = M("UserLogin");
    		$recordUserLogin = $UserLogin->find($userloginid);
    		
    		/**
    		 * change price ajax part
    		 */
    		if (!empty($_POST['price'])) {
    			$price = trim(addslashes(htmlspecialchars(strip_tags($_POST['price']))));
    			$caid = (int)trim(htmlspecialchars(strip_tags($_POST['caid'])));
    			if ($caid <= 0) {
    				$this->ajaxReturn(0,'出错啦','wrong');
    			} else {
    				$updateRecordCommodityassess = array(
    					'id' => $caid,
    					'buyprice' => $price,
    				);
    				$RecordCommodityassess->save($updateRecordCommodityassess);
    				$this->ajaxReturn(0,'成功','ok');
    			}
    		}

    		/**
    		 * refuse ajax part
    		 */
    		if (!empty($_POST['refusereason'])) {
    			$refusereason = trim(addslashes(htmlspecialchars(strip_tags($_POST['refusereason']))));
    			$caid = (int)trim(htmlspecialchars(strip_tags($_POST['caid'])));
    			if ($caid <= 0) {
    				$this->ajaxReturn(0,'出错啦','wrong');
    			} else {
    				$updateRecordCommodityassess = array(
    					'id' => $caid,
    					'refusereason' => $refusereason,
    					'status' => 5,
    					'end_ti' => time(),
    				);
    				$RecordCommodityassess->save($updateRecordCommodityassess);
    				
    				/**
    				 * send system message.
    				 */
    				$recordRecordCommodityassess = $RecordCommodityassess->find($caid);
     				$msgContent = $recordUserLogin['nickname']." 拒绝了您的购买请求!";
    				$msgData = array(
	                    'id' => '',
	                    'uid' => $recordRecordCommodityassess['uid'],
	                    'type' => 'mallset/seller-refuse',
	                    'url_id' => $recordRecordCommodityassess['cid'],
	                    'content' => $msgContent,
	                    'time' => time(),
	                    'deliver' => 0,
    				);
    				$MsgSystem->add($msgData);
    				
    				/**
    				 * send mail
    				 */
    				Vendor('Ihelpoo.Email');
    				$emailObj = new Email();
    				$recordToUserLogin = $UserLogin->find($recordRecordCommodityassess['uid']);
    				if (!empty($recordToUserLogin['email'])) {
    					$emailObj->mallInfo($recordToUserLogin['email'], $recordToUserLogin['nickname'], $msgContent);
    				}
    				
    				$this->ajaxReturn(0,'成功','ok');
    			}
    		}

    		/**
    		 * accept ajax part
    		 */
    		if (!empty($_POST['caid'])) {
    			$caid = (int)trim(htmlspecialchars(strip_tags($_POST['caid'])));
    			if ($caid <= 0) {
    				$this->ajaxReturn(0,'出错啦','wrong');
    			} else {
    				$updateRecordCommodityassess = array(
    					'id' => $caid,
    					'status' => 2,
    				);
    				$RecordCommodityassess->save($updateRecordCommodityassess);
    				
    				/**
    				 * send system message.
    				 */
    				$recordRecordCommodityassess = $RecordCommodityassess->find($caid);
     				$msgContent = $recordUserLogin['nickname']." 确定了您的购买请求，快快联系购买吧!";
    				$msgData = array(
	                    'id' => '',
	                    'uid' => $recordRecordCommodityassess['uid'],
	                    'type' => 'mallset/seller-sure',
	                    'url_id' => $recordRecordCommodityassess['cid'],
	                    'content' => $msgContent,
	                    'time' => time(),
	                    'deliver' => 0,
    				);
    				$MsgSystem->add($msgData);
    				
    				/**
    				 * send mail
    				 */
    				Vendor('Ihelpoo.Email');
    				$emailObj = new Email();
    				$recordToUserLogin = $UserLogin->find($recordRecordCommodityassess['uid']);
    				if (!empty($recordToUserLogin['email'])) {
    					$emailObj->mallInfo($recordToUserLogin['email'], $recordToUserLogin['nickname'], $msgContent);
    				}
    				$this->ajaxReturn(0,'成功','ok');
    			}
    		}
    	}

    	/**
    	 * page
    	 */
    	$page = i_page_get_num();
    	$count = 10;
        $offset = $page * $count;
    	if ($_GET['step'] == 'needsure') {
    		$joinResultsRecordCommodityassess = $RecordCommodityassess->where("i_record_commodity.shopid = $userloginid AND i_record_commodityassess.status = 1")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")
    		->join("i_user_login ON i_record_commodityassess.uid = i_user_login.uid")
    		->join("i_user_deliveryaddress ON i_record_commodityassess.buyaddressid = i_user_deliveryaddress.id")
    		->field('i_record_commodityassess.id,i_user_login.uid,i_record_commodity.cid,i_record_commodity.name,i_record_commodity.rebate,i_record_commodity.image,anonymous,buynums,buyprice,usecoins,i_record_commodityassess.buyway,buyaddressid,remarks,i_record_commodityassess.type,score,content,diffusion_co,assess_ti,i_record_commodityassess.status,start_ti,end_ti,nickname,address')
    		->order('start_ti DESC')
    		->select();
    		$totalRecordNums = $RecordCommodityassess->where("i_record_commodity.shopid = $userloginid AND i_record_commodityassess.status = 1")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	} else if ($_GET['step'] == 'ontrade') {
    		$joinResultsRecordCommodityassess = $RecordCommodityassess->where("i_record_commodity.shopid = $userloginid AND i_record_commodityassess.status = 2")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")
    		->join("i_user_login ON i_record_commodityassess.uid = i_user_login.uid")
    		->join("i_user_deliveryaddress ON i_record_commodityassess.buyaddressid = i_user_deliveryaddress.id")
    		->field('i_record_commodityassess.id,i_user_login.uid,i_record_commodity.cid,i_record_commodity.name,i_record_commodity.rebate,i_record_commodity.image,anonymous,buynums,buyprice,usecoins,i_record_commodityassess.buyway,buyaddressid,remarks,i_record_commodityassess.type,score,content,diffusion_co,assess_ti,i_record_commodityassess.status,refusereason,start_ti,end_ti,nickname,address')
    		->order('start_ti DESC')
    		->select();
    		$totalRecordNums = $RecordCommodityassess->where("i_record_commodity.shopid = $userloginid AND i_record_commodityassess.status = 2")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	} else if ($_GET['step'] == 'needassess') {
    		$joinResultsRecordCommodityassess = $RecordCommodityassess->where("i_record_commodity.shopid = $userloginid AND i_record_commodityassess.status = 3")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")
    		->join("i_user_login ON i_record_commodityassess.uid = i_user_login.uid")
    		->join("i_user_deliveryaddress ON i_record_commodityassess.buyaddressid = i_user_deliveryaddress.id")
    		->field('i_record_commodityassess.id,i_user_login.uid,i_record_commodity.cid,i_record_commodity.name,i_record_commodity.rebate,i_record_commodity.image,anonymous,buynums,buyprice,usecoins,i_record_commodityassess.buyway,buyaddressid,remarks,i_record_commodityassess.type,score,content,diffusion_co,assess_ti,i_record_commodityassess.status,refusereason,start_ti,end_ti,nickname,address')
    		->order('start_ti DESC')
    		->select();
    		$totalRecordNums = $RecordCommodityassess->where("i_record_commodity.shopid = $userloginid AND i_record_commodityassess.status = 3")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	} else if ($_GET['step'] == 'finish') {
    		$joinResultsRecordCommodityassess = $RecordCommodityassess->where("i_record_commodity.shopid = $userloginid AND i_record_commodityassess.status = 5")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")
    		->join("i_user_login ON i_record_commodityassess.uid = i_user_login.uid")
    		->join("i_user_deliveryaddress ON i_record_commodityassess.buyaddressid = i_user_deliveryaddress.id")
    		->field('i_record_commodityassess.id,i_user_login.uid,i_record_commodity.cid,i_record_commodity.name,i_record_commodity.rebate,i_record_commodity.image,anonymous,buynums,buyprice,usecoins,i_record_commodityassess.buyway,buyaddressid,remarks,i_record_commodityassess.type,score,content,diffusion_co,assess_ti,i_record_commodityassess.status,start_ti,end_ti,nickname,address,refusereason')
    		->order('start_ti DESC')
    		->select();
    		$totalRecordNums = $RecordCommodityassess->where("i_record_commodity.shopid = $userloginid AND i_record_commodityassess.status = 5")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")->count();
    	}

    	/**
    	 * page link
    	 */
        $this->assign('totalRecordNums',$totalRecordNums);
        $totalPages = ceil($totalRecordNums / $count);
        $this->assign('totalPages',$totalPages);
    	$this->assign('joinResultsRecordCommodityassess',$joinResultsRecordCommodityassess);
    	$this->display();
    }

    public function buyer()
    {
    	$userloginid = session('userloginid');
    	$RecordCommodity = M("RecordCommodity");
    	$this->assign('title',"我的交易");
    	$RecordCommodityassess = M("RecordCommodityassess");

    	/**
    	 * page
    	 */
    	$page = i_page_get_num();
    	$count = 10;
        $offset = $page * $count;
      	if ($_GET['step'] == 'ontrade') {
    		$joinResultsRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.uid = $userloginid AND i_record_commodityassess.status = 1")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")
    		->field('i_record_commodityassess.id,uid,i_record_commodity.cid,i_record_commodity.name,i_record_commodity.rebate,i_record_commodity.image,anonymous,buynums,buyprice,usecoins,i_record_commodityassess.buyway,remarks,i_record_commodityassess.type,score,content,diffusion_co,assess_ti,i_record_commodityassess.status,start_ti,end_ti')
    		->order('start_ti DESC')
    		->limit($offset,$count)
    		->select();
    		$totalRecordNums = $RecordCommodityassess->where("i_record_commodityassess.uid = $userloginid AND i_record_commodityassess.status = 1")->count();
    	} else if ($_GET['step'] == 'needsure') {
    		$joinResultsRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.uid = $userloginid AND i_record_commodityassess.status = 2")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")
    		->field('i_record_commodityassess.id,uid,i_record_commodity.cid,i_record_commodity.name,i_record_commodity.rebate,i_record_commodity.image,anonymous,buynums,buyprice,usecoins,i_record_commodityassess.buyway,remarks,i_record_commodityassess.type,score,content,diffusion_co,assess_ti,i_record_commodityassess.status,start_ti,end_ti')
    		->order('start_ti DESC')
    		->limit($offset,$count)
    		->select();
    		$totalRecordNums = $RecordCommodityassess->where("i_record_commodityassess.uid = $userloginid AND i_record_commodityassess.status = 2")->count();
    	}  else if ($_GET['step'] == 'needassess') {
    		$joinResultsRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.uid = $userloginid AND i_record_commodityassess.status = 3")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")
    		->field('i_record_commodityassess.id,uid,i_record_commodity.cid,i_record_commodity.name,i_record_commodity.rebate,i_record_commodity.image,anonymous,buynums,buyprice,usecoins,i_record_commodityassess.buyway,remarks,i_record_commodityassess.type,score,content,diffusion_co,assess_ti,i_record_commodityassess.status,start_ti,end_ti')
    		->order('start_ti DESC')
    		->limit($offset,$count)
    		->select();
    		$totalRecordNums = $RecordCommodityassess->where("i_record_commodityassess.uid = $userloginid AND i_record_commodityassess.status = 3")->count();
    	} else if ($_GET['step'] == 'finish') {
    		$joinResultsRecordCommodityassess = $RecordCommodityassess->where("i_record_commodityassess.uid = $userloginid AND i_record_commodityassess.status = 5")
    		->join("i_record_commodity ON i_record_commodityassess.cid = i_record_commodity.cid")
    		->field('i_record_commodityassess.id,uid,i_record_commodity.cid,i_record_commodity.name,i_record_commodity.rebate,i_record_commodity.image,anonymous,buynums,buyprice,usecoins,i_record_commodityassess.buyway,remarks,i_record_commodityassess.type,score,content,diffusion_co,assess_ti,i_record_commodityassess.status,start_ti,end_ti,refusereason')
    		->order('start_ti DESC')
    		->limit($offset,$count)
    		->select();
    		$totalRecordNums = $RecordCommodityassess->where("i_record_commodityassess.uid = $userloginid AND i_record_commodityassess.status = 5")->count();
    	}
    	$this->assign('joinResultsRecordCommodityassess',$joinResultsRecordCommodityassess);

    	/**
    	 * page link
    	 */
        $this->assign('totalRecordNums',$totalRecordNums);
        $totalPages = ceil($totalRecordNums / $count);
        $this->assign('totalPages',$totalPages);
    	$this->display();
    }

    public function shoppingcart()
    {
    	$userloginid = session('userloginid');
    	$UserShopcart = M("UserShopcart");

    	if (!empty($_GET['suredel'])) {
    		$suredelid = (int)$_GET['suredel'];
    		$UserShopcart->where("id = $suredelid")->delete();
    		redirect('/mallset/shoppingcart', 3, '删除成功... 3秒后页面跳转');
    	}
    	
    	/**
    	 * page
    	 */
    	$page = i_page_get_num();
    	$count = 10;
        $offset = $page * $count;
    	$joinResultsShopcartCommodity = $UserShopcart->where("i_user_shopcart.uid = $userloginid")
    	->join("i_record_commodity ON i_user_shopcart.cid = i_record_commodity.cid")
    	->field('i_record_commodity.cid,shopid,name,price,rebate,buyway,image,i_user_shopcart.id,i_user_shopcart.uid,i_user_shopcart.time')
    	->order('time DESC')
    	->limit($offset,$count)
    	->select();
    	$this->assign('joinResultsShopcartCommodity',$joinResultsShopcartCommodity);

    	/**
    	 * page link
    	 */
        $totalRecordNums = $UserShopcart->where("i_user_shopcart.uid = $userloginid")->count();
        $this->assign('totalRecordNums',$totalRecordNums);
        $totalPages = ceil($totalRecordNums / $count);
        $this->assign('totalPages',$totalPages);
        $this->assign('title',"购物车");
    	$this->display();
    }

}

?>