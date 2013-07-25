<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class MallAction extends Action {

    protected function _initialize()
    {
        $userloginid = session('userloginid');
    	if (!empty($userloginid)) {
    		i_db_update_activetime($userloginid);
    		$IUserLogin = D("IUserLogin");
    		$userloginedrecord = $IUserLogin->userExists($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);

    		/**
    		 * UserShop
    		 */
    		$UserShop = M("UserShop");
    		$userloginedrecordUserShop = $UserShop->find($userloginid);
    		if (!empty($userloginedrecordUserShop)) {
    			$this->assign('userloginedrecordUserShop',$userloginedrecordUserShop);
    		}
    	}
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('title','买卖首页 '.$recordSchoolInfo['school']);
    	$this->assign('schoolname',$recordSchoolInfo['school']);

    	/**
    	 * show commodity category
    	 */
    	$RecordCommoditycategory = M("RecordCommoditycategory");
    	$resulesRecordCommoditycategory = $RecordCommoditycategory->where("shop_id = '' AND parent_id = '0'")->select();
    	foreach ($resulesRecordCommoditycategory as $parentcategory) {
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
    		$categoryArray[] = array(
    			'id' => $parentcategory['cate_id'],
    			'name' => $parentcategory['cate_name'],
    			'children' => $childrenCategory,
    		);
    	}
    	$this->assign('categoryArray',$categoryArray);

    	/**
    	 * show Cooperation
    	 */
    	$MallCooperation = M("MallCooperation");
    	$resultsMallCooperation = $MallCooperation->where("school = $recordSchoolInfo[id]")->order("'order' DESC")->select();
    	$this->assign('resultsMallCooperation',$resultsMallCooperation);
    	
    	/**
    	 * commodity nums
    	 */
    	$RecordCommodity = M("RecordCommodity");
    	$totalCommodities = $RecordCommodity->where("school_id = $recordSchoolInfo[id]")->count();
    	$totalForbidCommodities = $RecordCommodity->where("status = 0 AND school_id = $recordSchoolInfo[id]")->count();
    	$totalSoldCommodities = $RecordCommodity->where("good_nums = 0 AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	$totalSellCommodities = $RecordCommodity->where("good_nums > 0 AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	$this->assign('totalCommodities',$totalCommodities);
    	$this->assign('totalForbidCommodities',$totalForbidCommodities);
    	$this->assign('totalSoldCommodities',$totalSoldCommodities);
    	$this->assign('totalSellCommodities',$totalSellCommodities);
    	
    	/**
    	 * right list
    	 */
    	$itemcategoryString = htmlspecialchars(trim($_GET["_URL_"][2]));
    	if (empty($itemcategoryString)) {
    		redirect('/mall/index/all', 0, '缺少URL参数 页面跳转...');
    	} else {
    		$this->assign('itemcategoryString', $itemcategoryString);
    	}
    	$page = i_page_get_num();
    	$count = 20;
    	$offset = $page * $count;
    	if ($itemcategoryString == 'all') {
	    	$joinResultsRecordCommodity = $RecordCommodity->where("i_record_commodity.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
	    	->join('i_record_commoditycategory ON i_record_commodity.category_id = i_record_commoditycategory.cate_id')
	    	->join('i_user_login ON i_record_commodity.shopid = i_user_login.uid')
	    	->join('i_user_shop ON i_record_commodity.shopid = i_user_shop.uid')
	    	->field('i_record_commodity.cid,shopid,i_record_commodity.name,i_record_commodity.price,i_record_commodity.rebate,i_record_commodity.buyway,i_record_commodity.image,
		    i_record_commodity.sales_co,i_record_commodity.good_nums,i_record_commodity.good_type,i_record_commodity.assess_co,i_record_commodity.hit,i_record_commodity.time,i_record_commodity.category_id,i_record_commodity.status,
		    i_record_commoditycategory.cate_name,i_record_commoditycategory.parent_id,i_user_shop.uid,i_user_login.nickname,i_user_login.online,i_user_shop.address')
	    	->order("time DESC")
	    	->limit($offset,$count)
	    	->select();
	    	$totalRecordNums = $RecordCommodity->where("status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	} else if ($itemcategoryString == 'new') {
    		$joinResultsRecordCommodity = $RecordCommodity->where("good_type = 1 AND i_record_commodity.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
	    	->join('i_record_commoditycategory ON i_record_commodity.category_id = i_record_commoditycategory.cate_id')
	    	->join('i_user_login ON i_record_commodity.shopid = i_user_login.uid')
	    	->join('i_user_shop ON i_record_commodity.shopid = i_user_shop.uid')
	    	->field('i_record_commodity.cid,shopid,i_record_commodity.name,i_record_commodity.price,i_record_commodity.rebate,i_record_commodity.buyway,i_record_commodity.image,
		    i_record_commodity.sales_co,i_record_commodity.good_nums,i_record_commodity.good_type,i_record_commodity.assess_co,i_record_commodity.hit,i_record_commodity.time,i_record_commodity.category_id,i_record_commodity.status,
		    i_record_commoditycategory.cate_name,i_record_commoditycategory.parent_id,i_user_shop.uid,i_user_login.nickname,i_user_login.online,i_user_shop.address')
	    	->order("time DESC")
	    	->limit($offset,$count)
	    	->select();
	    	$totalRecordNums = $RecordCommodity->where("good_type = 1 AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	} else if ($itemcategoryString == 'secondhand') {
    		$joinResultsRecordCommodity = $RecordCommodity->where("good_type = 2 AND i_record_commodity.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
	    	->join('i_record_commoditycategory ON i_record_commodity.category_id = i_record_commoditycategory.cate_id')
	    	->join('i_user_login ON i_record_commodity.shopid = i_user_login.uid')
	    	->join('i_user_shop ON i_record_commodity.shopid = i_user_shop.uid')
	    	->field('i_record_commodity.cid,shopid,i_record_commodity.name,i_record_commodity.price,i_record_commodity.rebate,i_record_commodity.buyway,i_record_commodity.image,
		    i_record_commodity.sales_co,i_record_commodity.good_nums,i_record_commodity.good_type,i_record_commodity.assess_co,i_record_commodity.hit,i_record_commodity.time,i_record_commodity.category_id,i_record_commodity.status,
		    i_record_commoditycategory.cate_name,i_record_commoditycategory.parent_id,i_user_shop.uid,i_user_login.nickname,i_user_login.online,i_user_shop.address')
	    	->order("time DESC")
	    	->limit($offset,$count)
	    	->select();
	    	$totalRecordNums = $RecordCommodity->where("good_type = 2 AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	} else if ($itemcategoryString == 'sales') {
    		$joinResultsRecordCommodity = $RecordCommodity->where("good_nums > 0 AND i_record_commodity.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
	    	->join('i_record_commoditycategory ON i_record_commodity.category_id = i_record_commoditycategory.cate_id')
	    	->join('i_user_login ON i_record_commodity.shopid = i_user_login.uid')
	    	->join('i_user_shop ON i_record_commodity.shopid = i_user_shop.uid')
	    	->field('i_record_commodity.cid,shopid,i_record_commodity.name,i_record_commodity.price,i_record_commodity.rebate,i_record_commodity.buyway,i_record_commodity.image,
		    i_record_commodity.sales_co,i_record_commodity.good_nums,i_record_commodity.good_type,i_record_commodity.assess_co,i_record_commodity.hit,i_record_commodity.time,i_record_commodity.category_id,i_record_commodity.status,
		    i_record_commoditycategory.cate_name,i_record_commoditycategory.parent_id,i_user_shop.uid,i_user_login.nickname,i_user_login.online,i_user_shop.address')
	    	->order("time DESC")
	    	->limit($offset,$count)
	    	->select();
	    	$totalRecordNums = $RecordCommodity->where("good_nums > 0 AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	} else if ($itemcategoryString == 'hassold') {
    		$joinResultsRecordCommodity = $RecordCommodity->where("good_nums = 0 AND i_record_commodity.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
	    	->join('i_record_commoditycategory ON i_record_commodity.category_id = i_record_commoditycategory.cate_id')
	    	->join('i_user_login ON i_record_commodity.shopid = i_user_login.uid')
	    	->join('i_user_shop ON i_record_commodity.shopid = i_user_shop.uid')
	    	->field('i_record_commodity.cid,shopid,i_record_commodity.name,i_record_commodity.price,i_record_commodity.rebate,i_record_commodity.buyway,i_record_commodity.image,
		    i_record_commodity.sales_co,i_record_commodity.good_nums,i_record_commodity.good_type,i_record_commodity.assess_co,i_record_commodity.hit,i_record_commodity.time,i_record_commodity.category_id,i_record_commodity.status,
		    i_record_commoditycategory.cate_name,i_record_commoditycategory.parent_id,i_user_shop.uid,i_user_login.nickname,i_user_login.online,i_user_shop.address')
	    	->order("time DESC")
	    	->limit($offset,$count)
	    	->select();
	    	$totalRecordNums = $RecordCommodity->where("good_nums = 0 AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	}
    	$this->assign('joinResultsRecordCommodity',$joinResultsRecordCommodity);
    	$totalPages = ceil($totalRecordNums / $count);
    	$this->assign('totalRecordNums',$totalRecordNums);
    	$this->assign('totalPages',$totalPages);
    	$this->display();
    }

    public function category()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$categoryId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$order = htmlspecialchars(trim($_GET["_URL_"][3]));
    	$goodtype = htmlspecialchars(trim($_GET["_URL_"][4]));
    	if (empty($categoryId) || $categoryId < 0) {
    		redirect('/mall', 3, '你选择的分类为空...3秒后返回逛街首页');
    	}
    	$RecordCommoditycategory = M("RecordCommoditycategory");
    	$resultRecordCommoditycategory = $RecordCommoditycategory->where("cate_id = $categoryId")->find();
    	if (empty($resultRecordCommoditycategory)) {
    		redirect('/mall', 3, '你选择的分类不存在...3秒后返回逛街首页');
    	}

    	/**
    	 * category navgater
    	 */
    	$this->assign('nowcategory',$resultRecordCommoditycategory);
    	$categoryString = $resultRecordCommoditycategory['cate_id'].",";
    	if ($resultRecordCommoditycategory['parent_id'] != '0') {
    		$parentRecordCommoditycategory = $RecordCommoditycategory->where("cate_id = $resultRecordCommoditycategory[parent_id]")->find();
    		$this->assign('parentRecordCommoditycategory',$parentRecordCommoditycategory);
    		$chlidrenRecordCommoditycategory = $RecordCommoditycategory->where("parent_id = $parentRecordCommoditycategory[cate_id]")->select();
    	} else {
    		$chlidrenRecordCommoditycategory = $RecordCommoditycategory->where("parent_id = $resultRecordCommoditycategory[cate_id]")->select();
    		$this->assign('parentRecordCommoditycategory',$resultRecordCommoditycategory);

    		/**
    		 * category ids
    		 */
    		foreach ($chlidrenRecordCommoditycategory as $category) {
    			$categoryString .= $category['cate_id'].",";
    		}
    	}
    	$this->assign('chlidrenRecordCommoditycategory',$chlidrenRecordCommoditycategory);
    	$categoryString = substr($categoryString, 0, -1);

    	/**
    	 * paging & sort
    	 */
    	$page = i_page_get_num();
    	$count = 20;
    	$offset = $page * $count;

    	$ordersort = "DESC";
    	if (preg_match("#-#", $order)) {
    		$ordersort = "ASC";
    	}
   		if ($order == 'hit' || $order == '-hit') {
   			$order = 'hit';
    		$orderway = 'hit';
    	} else if ($order == 'sales' || $order == '-sales') {
    		$order = 'sales';
    		$orderway = 'sales_co';
    	} else if ($order == 'time' || $order == '-time') {
    		$order = 'time';
    		$orderway = 'time';
    	} else {
    		$orderway = 'time';
    	}

    	/**
    	 * good type
    	 */
    	$RecordCommodity = M("RecordCommodity");
    	if ($goodtype == 'new') {
    		$goodtype = 'new';
    		$joinResultsRecordCommodity = $RecordCommodity->where("category_id IN ($categoryString) AND good_type = 1 AND i_record_commodity.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
	    	->join('i_record_commoditycategory ON i_record_commodity.category_id = i_record_commoditycategory.cate_id')
	    	->join('i_user_login ON i_record_commodity.shopid = i_user_login.uid')
	    	->join('i_user_shop ON i_record_commodity.shopid = i_user_shop.uid')
	    	->field('i_record_commodity.cid,shopid,i_record_commodity.name,i_record_commodity.price,i_record_commodity.rebate,i_record_commodity.buyway,i_record_commodity.image,
	    	i_record_commodity.sales_co,i_record_commodity.good_nums,i_record_commodity.good_type,i_record_commodity.assess_co,i_record_commodity.hit,i_record_commodity.time,i_record_commodity.category_id,i_record_commodity.status,
	    	i_record_commoditycategory.cate_name,i_record_commoditycategory.parent_id,i_user_login.nickname,i_user_login.online,i_user_shop.address')
	    	->order("$orderway $ordersort")
	    	->limit($offset,$count)
	    	->select();
	    	$totalRecordNums = $RecordCommodity->where("category_id IN ($categoryString) AND good_type = 1 AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	} else if ($goodtype == 'secondhand') {
    		$goodtype = 'secondhand';
    		$joinResultsRecordCommodity = $RecordCommodity->where("category_id IN ($categoryString) AND good_type = 2 AND i_record_commodity.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
	    	->join('i_record_commoditycategory ON i_record_commodity.category_id = i_record_commoditycategory.cate_id')
	    	->join('i_user_login ON i_record_commodity.shopid = i_user_login.uid')
	    	->join('i_user_shop ON i_record_commodity.shopid = i_user_shop.uid')
	    	->field('i_record_commodity.cid,shopid,i_record_commodity.name,i_record_commodity.price,i_record_commodity.rebate,i_record_commodity.buyway,i_record_commodity.image,
	    	i_record_commodity.sales_co,i_record_commodity.good_nums,i_record_commodity.good_type,i_record_commodity.assess_co,i_record_commodity.hit,i_record_commodity.time,i_record_commodity.category_id,i_record_commodity.status,
	    	i_record_commoditycategory.cate_name,i_record_commoditycategory.parent_id,i_user_login.nickname,i_user_login.online,i_user_shop.address')
	    	->order("$orderway $ordersort")
	    	->limit($offset,$count)
	    	->select();
	    	$totalRecordNums = $RecordCommodity->where("category_id IN ($categoryString) AND good_type = 2 AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	} else {
    		$goodtype = 'all';
    		$joinResultsRecordCommodity = $RecordCommodity->where("category_id IN ($categoryString) AND i_record_commodity.status = 1 AND i_record_commodity.school_id = $recordSchoolInfo[id]")
	    	->join('i_record_commoditycategory ON i_record_commodity.category_id = i_record_commoditycategory.cate_id')
	    	->join('i_user_login ON i_record_commodity.shopid = i_user_login.uid')
	    	->join('i_user_shop ON i_record_commodity.shopid = i_user_shop.uid')
	    	->field('i_record_commodity.cid,shopid,i_record_commodity.name,i_record_commodity.price,i_record_commodity.rebate,i_record_commodity.buyway,i_record_commodity.image,
	    	i_record_commodity.sales_co,i_record_commodity.good_nums,i_record_commodity.good_type,i_record_commodity.assess_co,i_record_commodity.hit,i_record_commodity.time,i_record_commodity.category_id,i_record_commodity.status,
	    	i_record_commoditycategory.cate_name,i_record_commoditycategory.parent_id,i_user_shop.uid,i_user_login.nickname,i_user_login.online,i_user_shop.address')
	    	->order("$orderway $ordersort")
	    	->limit($offset,$count)
	    	->select();
	    	$totalRecordNums = $RecordCommodity->where("category_id IN ($categoryString) AND status = 1 AND school_id = $recordSchoolInfo[id]")->count();
    	}
    	$this->assign('joinResultsRecordCommodity',$joinResultsRecordCommodity);

    	/**
    	 * paging
    	 */
    	$totalPages = ceil($totalRecordNums / $count);
    	$this->assign('totalRecordNums',$totalRecordNums);
        $this->assign('totalPages',$totalPages);
        $this->assign('order',$order);
        $this->assign('goodtype',$goodtype);
    	$this->assign('title',$resultRecordCommoditycategory['cate_name'].' '.$recordSchoolInfo['school']);
    	$this->display();
    }
    
    public function shoplist()
    {
    	$recordSchoolInfo = i_school_domain();
    	$this->assign('schoolname',$recordSchoolInfo['school']);
    	$shopcategoryString = htmlspecialchars(trim($_GET["_URL_"][2]));
    	$this->assign('title','小店列表 '.$recordSchoolInfo['school']);
    	$UserShop = M("UserShop");
    	
    	/**
    	 * paging & sort
    	 */
    	$page = i_page_get_num();
    	$count = 20;
    	$offset = $page * $count;

    	/**
    	 * User Shop Lists
    	 * i_user_shop.shop_type = 1 students
    	 * i_user_shop.shop_type = 3 shoper
    	 */
    	
    	if (!empty($shopcategoryString)) {
    		if ($shopcategoryString == 't_1') {
    			$recordsUserShop = $UserShop->where("i_user_shop.status = 2 AND i_user_shop.shop_type = 1 AND i_user_login.school = $recordSchoolInfo[id]")
    			->join('i_user_login ON i_user_shop.uid = i_user_login.uid')
    			->join('i_user_info ON i_user_shop.uid = i_user_info.uid')
    			->field('i_user_shop.uid,i_user_shop.status,i_user_shop.shop_type,i_user_shop.category,i_user_shop.address,i_user_shop.imww,i_user_shop.time,i_user_shop.commodity_co,
	    		i_user_login.nickname,i_user_login.online,i_user_login.icon_url,i_user_login.school,i_user_info.introduction,i_user_info.dormitory_op,i_user_info.mobile,i_user_info.qq,i_user_info.weibo')
    			->limit($offset, $count)
    			->order('commodity_co DESC')
    			->select();
    			$totalRecordNums = $UserShop->where("status = 2 AND shop_type = 1 AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_shop.uid = i_user_login.uid')->count();
    		} else if ($shopcategoryString == 't_2') {
    			$recordsUserShop = $UserShop->where("i_user_shop.status = 2 AND i_user_shop.shop_type = 2 AND i_user_login.school = $recordSchoolInfo[id]")
    			->join('i_user_login ON i_user_shop.uid = i_user_login.uid')
    			->join('i_user_info ON i_user_shop.uid = i_user_info.uid')
    			->field('i_user_shop.uid,i_user_shop.status,i_user_shop.shop_type,i_user_shop.category,i_user_shop.address,i_user_shop.imww,i_user_shop.time,i_user_shop.commodity_co,
	    		i_user_login.nickname,i_user_login.online,i_user_login.icon_url,i_user_login.school,i_user_info.introduction,i_user_info.dormitory_op,i_user_info.mobile,i_user_info.qq,i_user_info.weibo')
    			->limit($offset, $count)
    			->order('commodity_co DESC')
    			->select();
    			$totalRecordNums = $UserShop->where("status = 2 AND shop_type = 2 AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_shop.uid = i_user_login.uid')->count();
    		} else if ($shopcategoryString == 't_3') {
    			$recordsUserShop = $UserShop->where("i_user_shop.status = 2 AND i_user_shop.shop_type = 3 AND i_user_login.school = $recordSchoolInfo[id]")
    			->join('i_user_login ON i_user_shop.uid = i_user_login.uid')
    			->join('i_user_info ON i_user_shop.uid = i_user_info.uid')
    			->field('i_user_shop.uid,i_user_shop.status,i_user_shop.shop_type,i_user_shop.category,i_user_shop.address,i_user_shop.imww,i_user_shop.time,i_user_shop.commodity_co,
	    		i_user_login.nickname,i_user_login.online,i_user_login.icon_url,i_user_login.school,i_user_info.introduction,i_user_info.dormitory_op,i_user_info.mobile,i_user_info.qq,i_user_info.weibo')
    			->limit($offset, $count)
    			->order('commodity_co DESC')
    			->select();
    			$totalRecordNums = $UserShop->where("status = 2 AND shop_type = 3 AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_shop.uid = i_user_login.uid')->count();
    		} else if (is_numeric($shopcategoryString)) {
    			$recordsUserShop = $UserShop->where("i_user_shop.status = 2 AND i_user_info.dormitory_op = $shopcategoryString AND i_user_login.school = $recordSchoolInfo[id]")
    			->join('i_user_login ON i_user_shop.uid = i_user_login.uid')
    			->join('i_user_info ON i_user_shop.uid = i_user_info.uid')
    			->field('i_user_shop.uid,i_user_shop.status,i_user_shop.shop_type,i_user_shop.category,i_user_shop.address,i_user_shop.imww,i_user_shop.time,i_user_shop.commodity_co,
	    		i_user_login.nickname,i_user_login.online,i_user_login.icon_url,i_user_login.school,i_user_info.introduction,i_user_info.dormitory_op,i_user_info.mobile,i_user_info.qq,i_user_info.weibo')
    			->limit($offset, $count)
    			->order('commodity_co DESC')
    			->select();
    			$totalRecordNums = $UserShop->where("i_user_shop.status = 2 AND i_user_info.dormitory_op = $shopcategoryString AND i_user_login.school = $recordSchoolInfo[id]")
    			->join('i_user_login ON i_user_shop.uid = i_user_login.uid')
    			->join('i_user_info ON i_user_shop.uid = i_user_info.uid')
    			->field('i_user_shop.uid,i_user_shop.status,i_user_shop.shop_type,i_user_shop.category,i_user_shop.address,i_user_shop.imww,i_user_shop.time,i_user_shop.commodity_co,
	   			i_user_login.nickname,i_user_login.online,i_user_login.icon_url,i_user_login.school,i_user_info.introduction,i_user_info.dormitory_op,i_user_info.mobile,i_user_info.qq,i_user_info.weibo')
    			->count();
    		}
    		$this->assign('shopcategoryString',$shopcategoryString);
    	} else {
    		$recordsUserShop = $UserShop->where("i_user_shop.status = 2 AND i_user_login.school = $recordSchoolInfo[id]")
    		->join('i_user_login ON i_user_shop.uid = i_user_login.uid')
    		->join('i_user_info ON i_user_shop.uid = i_user_info.uid')
    		->field('i_user_shop.uid,i_user_shop.status,i_user_shop.shop_type,i_user_shop.category,i_user_shop.address,i_user_shop.imww,i_user_shop.time,i_user_shop.commodity_co,
	    	i_user_login.nickname,i_user_login.online,i_user_login.icon_url,i_user_login.school,i_user_info.introduction,i_user_info.dormitory_op,i_user_info.mobile,i_user_info.qq,i_user_info.weibo')
    		->order('commodity_co DESC')
    		->limit($offset, $count)
    		->select();
    		$totalRecordNums = $UserShop->where("i_user_shop.status = 2 AND i_user_login.school = $recordSchoolInfo[id]")->join('i_user_login ON i_user_shop.uid = i_user_login.uid')->count();
    	}
    	$this->assign('recordsUserShop', $recordsUserShop);

    	/**
    	 * paging
    	 */
    	$totalPages = ceil($totalRecordNums / $count);
    	$this->assign('totalRecordNums', $totalRecordNums);
        $this->assign('totalPages', $totalPages);
    	$this->display();
    }
    
    public function shop()
    {
    	$shopId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$categoryId = (int)htmlspecialchars(trim($_GET["_URL_"][3]));
    	if (empty($shopId)) {
    		redirect('/mall', 3, '你访问的店铺不存在 或者关闭了 :(...');
    	}
        $IUserLogin = D("IUserLogin");
    	$shopUserLogin = $IUserLogin->userExists($shopId);
    	$this->assign('title',$shopUserLogin['nickname'].' - 店铺');
    	$this->assign('shopUserLogin',$shopUserLogin);

    	$UserShop = M("UserShop");
    	$recordUserShop = $UserShop->where("uid = $shopId")->find();
    	if (empty($recordUserShop)) {
    		redirect('/mall', 3, '你访问的店铺不存在 或者关闭了 :(...');
    	}
    	if ($recordUserShop['status'] == 1) {
    		redirect('/mall', 3, '你访问的店铺还在审核中 稍后再来看看 :)...');
    	}
        $this->assign('recordUserShop',$recordUserShop);

        $UserInfo = M("UserInfo");
        $shopUserInfo = $UserInfo->find($shopId);
        $this->assign('shopUserInfo',$shopUserInfo);

        /**
         * right part, commodity list
         */
        $page = i_page_get_num();
    	$count = 16;
        $offset = $page * $count;
        $RecordCommodity = M("RecordCommodity");
        $RecordCommoditycategory = M("RecordCommoditycategory");
        if ($categoryId >= 0 && !empty($categoryId)) {
        	$singleRecordCommoditycategory = $RecordCommoditycategory->where("cate_id = $categoryId")->find();
        	$this->assign('singleRecordCommoditycategory',$singleRecordCommoditycategory);

        	/**
        	 * parent_category
        	 */
        	if ($singleRecordCommoditycategory['parent_id'] != '0') {
        		$parentRecordCommoditycategory = $RecordCommoditycategory->where("cate_id = $singleRecordCommoditycategory[parent_id]")->find();
        		$this->assign('parentRecordCommoditycategory',$parentRecordCommoditycategory);
        	}
        	$resultsRecordCommodity = $RecordCommodity->where("shopid = $shopId AND category_id = $categoryId")
        	->field('detail',true)
        	->order("time DESC")
        	->limit($offset,$count)
        	->select();
        	$totalCommodityNums = $RecordCommodity->where("shopid = $shopId AND category_id = $categoryId")->count();
        } else {
        	$resultsRecordCommodity = $RecordCommodity->where("shopid = $shopId AND status = 1")
        	->field('detail',true)
        	->order("time DESC")
        	->limit($offset,$count)
        	->select();
        	$totalCommodityNums = $RecordCommodity->where("shopid = $shopId")->count();
        }

    	/**
    	 * paging
    	 */
        $totalPages = ceil($totalCommodityNums / $count);
        $this->assign('totalRecordNums',$totalCommodityNums);
        $this->assign('totalPages',$totalPages);

        /**
         * shop category
         */
        $categoryArray = array();
        if (!empty($resultsRecordCommodity)) {
        	$this->assign('resultsRecordCommodity',$resultsRecordCommodity);
        	$cateRecordCommodity = $RecordCommodity->where("shopid = $shopId")->field('cid,shopid,category_id')->select();
        	foreach ($cateRecordCommodity as $cateCommodity) {
        		if (!in_array($cateCommodity['category_id'], $categoryArray)) {
        			$categoryArray[] = $cateCommodity['category_id'];
        			$categoryString .= $cateCommodity['category_id'].",";
        		}
        	}
        	$categoryString = substr($categoryString, 0, -1);
        	$resultsRecordCommoditycategory = $RecordCommoditycategory->where("cate_id IN ($categoryString)")->select();
        	$this->assign('resultsRecordCommoditycategory',$resultsRecordCommoditycategory);
        }
    	$this->display();
    }

    public function item()
    {
    	$userloginid = session('userloginid');
    	$itemId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	if (empty($itemId)) {
    		redirect('/mall', 3, '你访问的商品不存在 或者被删除了 :(...');
    	}

    	$RecordCommodity = M("RecordCommodity");
    	$resultRecordCommodity = $RecordCommodity->find($itemId);
    	if (empty($resultRecordCommodity)) {
    		redirect('/mall', 3, '你访问的商品不存在 或者被删除了 :(...');
    	}
    	if (empty($resultRecordCommodity['status'])) {
    		redirect('/mallset/add/'.$resultRecordCommodity['cid'], 3, '你访问的商品违规被禁售了，请重新编辑后联系我帮圈圈发布 :(...');
    	}

    	/**
    	 *
    	 * update hit nums ...
    	 */
    	$updateHit = array(
    		'cid' => $itemId,
    		'hit' => $resultRecordCommodity['hit'] + 1
    	);
    	$RecordCommodity->save($updateHit);

    	/**
    	 * category
    	 */
    	$RecordCommoditycategory = M("RecordCommoditycategory");
    	$singleRecordCommoditycategory = $RecordCommoditycategory->where("cate_id = $resultRecordCommodity[category_id]")->find();
    	$this->assign('singleRecordCommoditycategory',$singleRecordCommoditycategory);

    	/**
    	 * parent_category
    	 */
    	if ($singleRecordCommoditycategory['parent_id'] != '0') {
    		$parentRecordCommoditycategory = $RecordCommoditycategory->where("cate_id = $singleRecordCommoditycategory[parent_id]")->find();
    		$this->assign('parentRecordCommoditycategory',$parentRecordCommoditycategory);
    	}

    	/**
    	 *
    	 * mall left
    	 */
    	$shopId = $resultRecordCommodity['shopid'];
        $IUserLogin = D("IUserLogin");
    	$shopUserLogin = $IUserLogin->userExists($shopId);
    	$this->assign('title',$resultRecordCommodity['name'].' - '.$shopUserLogin['nickname'].' ');
    	$this->assign('shopUserLogin',$shopUserLogin);
    	$UserShop = M("UserShop");
    	$recordUserShop = $UserShop->where("uid = $shopId")->find();
    	if (empty($recordUserShop)) {
    		redirect('/mall', 3, '你访问的店铺不存在 或者关闭了 :(...');
    	}
        $this->assign('recordUserShop',$recordUserShop);
        $UserInfo = M("UserInfo");
        $shopUserInfo = $UserInfo->find($shopId);
        $this->assign('shopUserInfo',$shopUserInfo);
        
    	if ($recordUserShop['status'] == 1) {
    		redirect('/mall', 3, '你访问的店铺还在审核中 稍后再来看看 :)...');
    	}

        /**
         *
         */
        $this->assign('resultRecordCommodity',$resultRecordCommodity);
    	$this->display();
    }

    /**
     *
     * ajax for item
     */
    public function itemajax()
    {
    	$userloginid = session('userloginid');
    	if ($this->isPost()) {
    		/**
    		 *
    		 * ajax show commodity detail
    		 */
    		if (!empty($_POST['salesdetail'])) {
    			$commodityid = (int)$_POST['salesdetail'];
    			$RecordCommodity = M("RecordCommodity");
    			$resultRecordCommodityDetail = $RecordCommodity->where("cid = $commodityid")->field('detail')->find();
    			$this->ajaxReturn(stripslashes($resultRecordCommodityDetail['detail']),'返回商品详情','ok');
    		}

    		/**
    		 *
    		 * ajax show assess and sales record
    		 */
    		$RecordCommodityassess = M("RecordCommodityassess");
    		if (!empty($_POST['page'])) {
    			$page = (int)$_POST['page'];
    		} else {
    			$page = 0;
    		}
    		$count = 15;
    		$offset = $page * $count;
    		if (!empty($_POST['salesrecord'])) {
    			$commodityid = (int)$_POST['salesrecord'];
    			$resultsRecordCommodityassess = $RecordCommodityassess->where("cid = $commodityid")
    			->join('i_user_login ON i_record_commodityassess.uid = i_user_login.uid')
    			->field('i_record_commodityassess.uid,cid,anonymous,buynums,buyprice,buyway,refusereason,start_ti,end_ti,i_user_login.nickname,i_user_login.icon_url')
    			->order("start_ti DESC")
    			->limit($offset,$count)
    			->select();
    			$resultsRecordCommodityassessArray = NULL;
    			foreach ($resultsRecordCommodityassess as $commodityassess) {
    				if ($commodityassess['buyway'] == 1) {
						$commodityassessbuyway = "免费送货上门";
    				} else if ($commodityassess['buyway'] == 2) {
						$commodityassessbuyway = "+1元送货上门";
    				} else if ($commodityassess['buyway'] == 3) {
						$commodityassessbuyway = "自己来取";
    				}
    				if (!empty($commodityassess['end_ti'])) {
    					$commodityassessendtime = i_time($commodityassess['end_ti']);
    				}
    				if($commodityassess['anonymous'] == 1){
    					$resultsRecordCommodityassessArray[] = array(
	    					'cid' => $commodityassess['cid'],
		    				'nickname' => "匿名***",
		    				'uid' => 0,
		    				'buynums' => $commodityassess['buynums'],
		    				'buyprice' => $commodityassess['buyprice'],
	    					'buyway' => $commodityassessbuyway,
    						'refusereason' => $commodityassess['refusereason'],
		    				'start_ti' => i_time($commodityassess['start_ti']),
		    				'end_ti' => $commodityassessendtime,
	    					'icon_url' => i_icon_check($commodityassess['uid'],NULL)
    					);
    				} else {
    					$resultsRecordCommodityassessArray[] = array(
	    					'cid' => $commodityassess['cid'],
		    				'nickname' => $commodityassess['nickname'],
		    				'uid' => $commodityassess['uid'],
		    				'buynums' => $commodityassess['buynums'],
		    				'buyprice' => $commodityassess['buyprice'],
	    					'buyway' => $commodityassessbuyway,
    						'refusereason' => $commodityassess['refusereason'],
		    				'start_ti' => i_time($commodityassess['start_ti']),
		    				'end_ti' => $commodityassessendtime,
	    					'icon_url' => i_icon_check($commodityassess['uid'],$commodityassess['icon_url'],'s')
    					);
    				}
    			}
    			$this->ajaxReturn($resultsRecordCommodityassessArray,'返回记录','ok');
    		}

    		if (!empty($_POST['assess'])) {
    			$commodityid = (int)$_POST['assess'];
    			$resultsRecordCommodityassess = $RecordCommodityassess->where("cid = $commodityid AND content != ''")
    			->join('i_user_login ON i_record_commodityassess.uid = i_user_login.uid')
    			->field('i_record_commodityassess.uid,cid,anonymous,content,score,start_ti,end_ti,assess_ti,i_user_login.nickname,i_user_login.icon_url')
    			->order("assess_ti DESC")
    			->limit($offset,$count)
    			->select();
    			$resultsRecordCommodityassessArray = NULL;
    			foreach ($resultsRecordCommodityassess as $commodityassess) {
    				$tiemgap = $commodityassess['end_ti'] - $commodityassess['start_ti'];
    				if ($tiemgap < 60) {
    					$tiemgap = $tiemgap.'秒,这也太快了吧';
    				} else if ($tiemgap < 600) {
    					$tiemgap = '10分钟之内';
			        } else if ($tiemgap < 3600) {
			        	$tiemgap =  floor($tiemgap/60)."分钟";
			        } else if ($tiemgap < 86400 ) {
			        	$tiemgap = floor($tiemgap/3600)."小时";
			        } else {
			        	$tiemgap = floor($tiemgap/86400)."天".floor((($tiemgap % 86400) / 3600))."小时";
			        }
    				if($commodityassess['anonymous'] == 1){
    					$resultsRecordCommodityassessArray[] = array(
	    					'cid' => $commodityassess['cid'],
		    				'nickname' => "匿名***",
		    				'uid' => NULL,
		    				'content' => $commodityassess['content'],
    						'score' => $commodityassess['score'],
		    				'assess_ti' => i_time($commodityassess['assess_ti']),
    						'timegap' => $tiemgap,
	    					'icon_url' => i_icon_check($commodityassess['uid'],NULL)
    					);
    				} else {
    					$resultsRecordCommodityassessArray[] = array(
	    					'cid' => $commodityassess['cid'],
		    				'nickname' => $commodityassess['nickname'],
		    				'uid' => $commodityassess['uid'],
		    				'content' => $commodityassess['content'],
    						'score' => $commodityassess['score'],
		    				'assess_ti' => i_time($commodityassess['assess_ti']),
    						'timegap' => $tiemgap,
	    					'icon_url' => i_icon_check($commodityassess['uid'],$commodityassess['icon_url'],'s')
    					);
    				}
    			}
    			$this->ajaxReturn($resultsRecordCommodityassessArray,'返回评论','ok');
    		}

    		if (empty($userloginid)) {
    			$this->ajaxReturn(0,'你还没有登录呢','error');
    		}

    		/**
    		 * buy now
    		 */
    		if (!empty($_POST['buynowcid'])) {
    			$buynowcid = (int)$_POST['buynowcid'];
    			$buynownums = (int)$_POST['buynownums'];
    			$url = 'mallset/buynow/'.$buynowcid.'/'.$buynownums;
    			$this->ajaxReturn($url,'成功','ok');
    		}

    		/**
    		 * add shopping cart data
    		 */
    		if (!empty($_POST['addshopcartcid'])) {
    			$addshopcartcid = (int)$_POST['addshopcartcid'];
    			$UserShopcart = M("UserShopcart");
    			$resultUserShopcart = $UserShopcart->where("cid = $addshopcartcid")->find();
    			if ($resultUserShopcart['id']) {
    				$this->ajaxReturn(0,'已添加了哦','error');
    			} else {
	    			$newUserShopcart = array(
	    				'id' => '',
	    				'uid' => $userloginid,
	    				'cid' => $addshopcartcid,
	    				'time' => time()
	    			);
	    			$isAddUserShopcart = $UserShopcart->add($newUserShopcart);
	    			if ($isAddUserShopcart) {
	    				$this->ajaxReturn(0,'已添加到购物车','ok');
	    			} else {
	    				$this->ajaxReturn(0,'添加失败','error');
	    			}
    			}
    		}
    	}
    }


    /**
     * open an new shop
     */
    public function newshop()
    {
    	$userloginid = session('userloginid');
    	$this->assign('title','我要开店');
    	if (empty($userloginid)) {
    		redirect('/mall', 3, '你还没有登录呢...');
    	}
    	
	    /**
	     * 
	     * quanquan code ...
	     */
    	if (!empty($_GET['qcode'])) {
    		$qcode = (int)$_GET['qcode'];
    		if ($qcode > 0) {
    			$DaQcode = M("DaQcode");
    			$userRecordDaQcode = $DaQcode->where("uid = $userloginid")->find();
    			if (!empty($userRecordDaQcode['id'])) {
    				$this->assign('qcode', $userRecordDaQcode['qcode']);
    			} else {
	    			$recordDaQcode = $DaQcode->where("qcode = $qcode")->find();
	    			if (!empty($recordDaQcode['uid']) && $recordDaQcode['uid'] != $userloginid) {
	    				redirect('/mall/newshop?agreement=read', 3, '圈圈码已经被别人使用了...');
	    			} else if (!empty($recordDaQcode['id'])) {
	    				$useDaQcode = array(
	    					'id' => $recordDaQcode['id'],
	    					'uid' => $userloginid,
	    					'use' => 1,
	    					'time' => time()
	    				);
	    				$DaQcode->save($useDaQcode);
	    				$this->assign('qcode', $qcode);
	    			} else {
	    				redirect('/mall/newshop?agreement=read', 3, '圈圈码不存在...');
	    			}
    			}
    		}
    	}

    	$UserShop = M("UserShop");
    	$recordUserShop = $UserShop->find($userloginid);
    	if ($this->isPost()) {
    		$shoptype = (int)$_POST['shoptype'];
    		if (!empty($_FILES)) {
    			if ($_FILES["uploadidcard"]["error"] > 0) {
    				redirect('/mall/newshop', 3, 'file error...'.$_FILES["uploadidcard"]["error"]);
    			} else {
    				$imageOldName = $_FILES["uploadidcard"]["name"];
    				$imageType = $_FILES["uploadidcard"]["type"];
    				$imageSize = $_FILES["uploadidcard"]["size"];
    				$imageTmpName = $_FILES["uploadidcard"]["tmp_name"];
    			}

    			/**
    			 * $tempRealSize = getimagesize($_FILES["uploadedimg"]["tmp_name"]);
    			 * $logoRealWidth = $tempRealSize['0'];
    			 * $logoRealHeight = $tempRealSize['1'];
    			 */
    			if ($imageSize > 3670016) {
    				redirect('/mall/newshop', 3, 'error...上传图片太大, 最大能上传单张 3.5MB');
    			} else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {
    				import("@.ORG.UploadFile");
    				$config=array(
		                'allowExts'=>array('jpeg','jpg','gif','png','bmp'),
		                'savePath'=>'./Public/mall/idcard/',
		                'saveRule'=>$userloginid.time(),
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
    					$newfilepath = $storage->getUrl("public", "mall/idcard/".$info[0]['savename']);

    					/**
    					 * update into i_user_album
    					 */
    					if (!empty($recordUserShop['uid'])) {
	    					$updateUserShop = array(
	    						'uid' => $userloginid,
	    						'status' => 1,
	    						'idcard' => $newfilepath,
	    					);
	    					$isUpdateFlag = $UserShop->save($updateUserShop);
	    					if ($isUpdateFlag) {
	    						redirect('/mall/newshop', 3, '更新证件成功,等待审核...');
	    					} else {
	    						redirect('/mall/newshop', 3, 'failed...请重试');
	    					}
    					} else {

    						/**
    						 * status
    						 * 1 for upload idcord; need check
    						 * 2 for ok
    						 */
    						$updateUserShop = array(
	    						'uid' => $userloginid,
    							'status' => 1,
    							'shop_type' => $shoptype,
	    						'idcard' => $newfilepath,
	    						'time' => time(),
	    					);
    						$isUpdateFlag = $UserShop->add($updateUserShop);
    						if ($isUpdateFlag) {
	    						redirect('/mallset', 3, '上传证件成功,等待审核后开通店铺...');
	    					} else {
	    						redirect('/mall/newshop', 3, 'failed...请重试');
	    					}
    					}

    				}
    			} else {
    				redirect('/mall/newshop', 3, 'error...上传图片格式错误, 目前仅支持.jpg .png .gif');
    			}
    		}
    	}

    	if (empty($recordUserShop['uid']) && empty($_GET['agreement'])) {
    		redirect('/mall/newshop?agreement=read', 3, '请选择店铺类型...');
    	}

        $this->assign('recordUserShop',$recordUserShop);
    	$this->display();
    }

}

?>