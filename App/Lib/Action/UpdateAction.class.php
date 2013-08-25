<?php
/**
 * update for ihelpoo move bate 3 to bate 4
 * @author chenkehao
 */
class UpdateAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/index?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		var_dump($datacontentArray);
    		/*while ($handlednums < $total) {
    			++$page;
    			redirect('/update/index?p='.$page, 1, "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...");
    			*
    			 * redirect
    			
    		} */	
    	}
    }
    
    /**	
     *
     * update to version 4
     */
    public function moveimgupyun()
    {
    	$url = "http://ihelpoo-public.stor.sinaapp.com/";
    	
    	/**
    	 * move useralbum
    	 * add Upyun.php in ...Vendor
    	 */
    	Vendor('Ihelpoo.Upyun');
    	$upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
    	$rsp = $upyun->readFile($listIn);
    	var_dump($rsp);
    	exit();
    	
    	$listIn = '/useralbum/10000/100001352533354.jpg';
    	$fileUrl = 'http://ihelpoo-public.stor.sinaapp.com/useralbum/10000/100001352533354.jpg';
    	$srcTempLargeIconFilename = 'temp.jpg';
    	$imgOld = imagecreatefromjpeg($fileUrl);
    	imagejpeg($imgOld,$srcTempLargeIconFilename);
    	imagedestroy($imgOld);
    	try {
    		$fh = fopen($srcTempLargeIconFilename, 'rb');
    		$rsp = $upyun->writeFile($listIn, $fh, True);   //上传图片，自动创建目录
    		fclose($fh);
    		var_dump($rsp);
    		echo "<a href='http://ihelpoo.b0.upaiyun.com/".$listIn."' target='_blank'>".$listIn."</a><br />";
    	}
    	catch(Exception $e) {
    		echo $e->getCode();
    		echo $e->getMessage();
    	}
    	exit();
    }
    
    public function moveuserfromzzulicleandb()
    {
    	$UserLogin = M("UserLogin");
    	$UserInfo = M("UserInfo");
    	$UserStatus = M("UserStatus");
    	$recordUserInfo = $UserInfo->select();
    	foreach ($recordUserInfo as $userInfo) {
    		$recordUserLogin = $UserLogin->find($userInfo['uid']);
    		if (empty($recordUserLogin['uid'])) {
    			$isdeleteUserInfo = $UserInfo->where("uid = $userInfo[uid]")->delete();
    			echo $isdeleteUserInfo;
    			echo '<br />';
    		}
    	}
    	
    	$recordUserStatus = $UserStatus->select();
    	foreach ($recordUserStatus as $userStatus) {
    		$recordUserLogin = $UserLogin->find($userStatus['uid']);
    		if (empty($recordUserLogin['uid'])) {
    			$isdeleteUserInfo= $UserStatus->where("uid = $userStatus[uid]")->delete();
    			echo $isdeleteUserInfo;
    			echo '<br />';
    		}
    	}
    }
    
    public function moveuserfromzzuli()
    {
    	$UserLogin = M("UserLogin");
    	$UserInfo = M("UserInfo");
    	$UserStatus = M("UserStatus");
    	$UserLoginzzuli = M("UserLoginzzuli");
    	$UserInfozzuli = M("UserInfozzuli");
    	$UserStatuszzuli = M("UserStatuszzuli");
    	$recordUserLoginzzuli = $UserLoginzzuli->select();
    	foreach ($recordUserLoginzzuli as $userLoginzzuli) {
    		$existUserLogin = $UserLogin->find($userLoginzzuli['uid']);
    		if ($existUserLogin['uid']) {
    			echo 'uid:'.$existUserLogin['uid'].' 昵称:'.$existUserLogin['nickname'].' 已经存在<br />';
    		} else {
    			$userLoginzzuli['school'] = 2;
    			$UserLogin->add($userLoginzzuli);
    			
    			//echo '<br />i_user_login<br />';
    			//var_dump($userLoginzzuli);
    			
    			$recordUserInfozzuli = $UserInfozzuli->find($userLoginzzuli['uid']);
    			$UserInfo->add($recordUserInfozzuli);
    			
    			//echo '<br />i_user_info<br />';
    			//var_dump($recordUserInfozzuli);
    			
    			$recordUserStatuszzuli = $UserStatuszzuli->find($userLoginzzuli['uid']);
    			$UserStatus->add($recordUserStatuszzuli);
    			
    			//echo '<br />i_user_status<br />';
    			//var_dump($recordUserStatuszzuli);
    			echo 'uid:'.$userLoginzzuli['uid'].' 插入数据库<br />';
    		}
    	}
    }
    
    /**
     * user priority
     */
    
    public function movesayfromzzuli()
    {
    	$RecordSayzzuli = M("RecordSayzzuli");
    	$RecordSay = M("RecordSay");
    	$resultsRecordSayzzuli = $RecordSayzzuli->select();
    	foreach($resultsRecordSayzzuli as $recordSayzzuli) {
    		$recordSayzzuli['school_id'] = 2;
    		$recordSayzzuli['sid'] = $recordSayzzuli['sid'] + 35000;
    		$isInsertRecordSay = $RecordSay->add($recordSayzzuli);
    		echo $isInsertRecordSay."<br />";
    	}
    }
    
    /**
     * move saycomment
     * move say help
     * move say helpreply
     * move i_record_commodity
     * move i_talk_list
     * move i_talk_content
     */
    
    
}  
?>