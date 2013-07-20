<?php
/**
 * update for ihelpoo move bate 2 to bate 3
 * @author chenkehao
 */
class UpdateAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function moveusericon() {
    	$IUserLogin = D("IUserLogin");
    	//offset , limit 
    	$users = $IUserLogin->where()->limit(700,700)->select();
    	$s = new SaeStorage();
    	
    	foreach ($users as $user) {
    		if(empty($user['icon_url'])) {
    			$flag = $s->fileExists('public', 'tempusericon/'.$user['uid'].'.jpg');
    			if ($flag) {
    				$iconnamestring = $user['uid'].time();
    				$stringurl = $s->getUrl('public', 'tempusericon/'.$user['uid'].'.jpg');
    				$srcFile = file_get_contents($stringurl);
    				$destFile = "/useralbum"."/".$user['uid']."/".$iconnamestring.".jpg";
    				$f = $s->write('public', $destFile, $srcFile);
    				var_dump($f);
    				echo "<br/>";
    				$userLoginData = array(
    					'uid' => $user['uid'],
    					'icon_fl' => 1,
    					'icon_url' => $iconnamestring
    				);
    				$IUserLogin->save($userLoginData);
    			}
    		    $flag = $s->fileExists('public', 'tempusericon/'.$user['uid'].'_m.jpg');
    			if ($flag) {
    				$stringurl = $s->getUrl('public', 'tempusericon/'.$user['uid'].'_m.jpg');
    				$srcFile = file_get_contents($stringurl);
    				$destFile = "/useralbum"."/".$user['uid']."/".$iconnamestring."_m.jpg";
    				$f = $s->write('public', $destFile, $srcFile);
    				var_dump($f);
    				echo "<br/>";
    			}
    		    $flag = $s->fileExists('public', 'tempusericon/'.$user['uid'].'_s.jpg');
    			if ($flag) {
    				$stringurl = $s->getUrl('public', 'tempusericon/'.$user['uid'].'_s.jpg');
    				$srcFile = file_get_contents($stringurl);
    				$destFile = "/useralbum"."/".$user['uid']."/".$iconnamestring."_s.jpg";
    				$f = $s->write('public', $destFile, $srcFile);
    				var_dump($f);
    				echo "<br/>";
    			}
    		}
    	}
    }
    
    public function calculatefansnums()
    {
    	$UserInfo = M("UserInfo");
    	$UserPriority = M("UserPriority");
    	$allUsers = $UserInfo->select();
    	foreach($allUsers as $user) {
    		$userId = $user['uid'];
    		$userFollowNums = $UserPriority->where("uid = $userId")->count();
    		$userFansNums = $UserPriority->where("pid = $userId")->count();
    		$newUserInfoNums = array(
    			'uid' => $userId,
    			'follow' => $userFollowNums,
    			'fans' => $userFansNums,
    		);
    		$isSaved = $UserInfo->save($newUserInfoNums);
    		if ($isSaved) {
    			echo $userId." update ok <br />";
    		}
    	}
    }
    
    public function prioritytype()
    {
    	$UserLogin = M("UserLogin");
    	$UserPriority = M("UserPriority");
    	$allUsers = $UserPriority->select();
    	foreach($allUsers as $user) {
    		$userId = $user['pid'];
    		$recordUserLogin = $UserLogin->find($userId);
    		$newprioritytype = array(
	    		'id' => $user['id'],
	    		'pid_type' => $recordUserLogin['type']
    		);
    		$isSaved = $UserPriority->save($newprioritytype);
    		if ($isSaved) {
    			echo $userId." update user prioritied type ok <br />";
    		} else {
    			var_dump($isSaved);
    			echo  "<br />";
    		}
    	}
    }
    
    public function movedatabase()
    {
    	echo "跳过 i_admin_realnamemf";
    	echo "跳过 i_admin_user";
    	echo "跳过 i_admin_userlogin";
    	echo "跳过 i_admin_userrecord";
    	echo "已删除表 i_au_spider_list";
    	echo "已删除表 i_au_user_frequent";
    	echo "导入数据[结构匹配] i_au_mail_send";
    	echo "导入数据[结构匹配] i_au_temp_uploadimg";
    	echo "跳过 i_da_students";
    	echo "跳过[已建新表] i_cms_artical";
    	echo "跳过[已建新表] i_mall_cooperation";
    	echo "跳过[已建新表] i_mall_indeximg";
    	echo "跳过[已建新表] i_msg_active";
    	echo "跳过[已建新表] i_msg_at";
    	echo "导入数据[结构匹配] i_msg_comment";
    	echo "导入数据[结构匹配] i_msg_system";
    	echo "跳过 i_op_academy";
    	echo "跳过 i_op_city";
    	echo "跳过 i_op_dormitory";
    	echo "跳过 i_op_province";
    	echo "跳过 i_op_specialty";
    	echo "已删除表 i_op_reward";
    	echo "导入数据[add field image,diffusion_co] i_record_comment";
    	echo "跳过[已建新表] i_record_commodity";
    	echo "跳过[已建新表] i_record_commodityassess";
    	echo "跳过[已建新表] i_record_commoditycategory";
    	echo "导入数据[+field comment_id, helpreply_id, assess_id] i_record_diffusion";
    	echo "跳过[已建新表] i_record_dynamic";
    	echo "导入数据[结构匹配] i_record_favourites";
    	echo "导入数据[!important -field reward_op, action_uid, category_id] i_record_help";
    	echo "已删除表 i_record_helpcategory";
    	echo "导入数据[+field image, diffusion_co] i_record_helpreply";
    	echo "导入数据[结构匹配] i_record_outimg";
    	echo "已删除表 i_record_push";
    	echo "已删除表 i_record_pushresult";
    	echo "导入数据[!important -field at] i_record_say";
    	echo "跳过 i_sys_parameter";
    	echo "导入数据[+field image] i_talk_content";
    	echo "跳过 i_talk_inputstatus";
    	echo "导入数据[结构匹配] i_talk_list";
    	echo "跳过[已建新表] i_user_album";
    	echo "已删除表 i_ti_user_infobase";
    	echo "已删除表 i_ti_user_infoconn";
    	echo "已删除表 i_ti_user_login";
    	echo "导入数据[结构匹配] i_user_changeinfo";
    	echo "跳过[已建新表] i_user_coins";
    	echo "跳过[已建新表] i_user_deliveryaddress";
    	echo "导入数据[结构匹配] i_user_honor";
    	echo "合并表 [!important i_user_infobase + i_user_infoconn = i_user_info] i_user_info";
    	echo "跳过[已建新表] i_user_invite";
    	echo "已删除表 i_user_label";
    	echo "导入数据[!important +field creat_ti(i_ti_user_login), login_days_co, online, coins, active, icon_fl, icon_url, skin] i_user_login";
    	echo "跳过 i_user_login_mi";
    	echo "导入数据[结构匹配] i_user_login_wb";
    	echo "导入数据[+field pid_type] i_user_priority";
    	echo "跳过[已建新表] i_user_shop";
    	echo "跳过[已建新表] i_user_shopcart";
    	echo "导入数据[!important changefield active->i_user_login, coins->i_user_login, login_days_co->i_user_login, -online_is, +acquire_seconds, +acquire_times, +dynamic_flag, +record_limit] i_user_status";
    	echo "跳过 i_web_status";
    	echo "已删除表 temp_i_da_idcard";
    	
    	$empty_i_au_mail_send = 'TRUNCATE TABLE `i_au_mail_send`';
    	$empty_i_au_temp_uploadimg = 'TRUNCATE TABLE `i_au_temp_uploadimg`';
    	$empty_i_msg_comment = 'TRUNCATE TABLE `i_msg_comment`';
    	$empty_i_msg_system = 'TRUNCATE TABLE `i_msg_system`';
    	$empty_i_record_comment = 'TRUNCATE TABLE `i_record_comment`';
    	$empty_i_record_diffusion = 'TRUNCATE TABLE `i_record_diffusion`';
    	$empty_i_record_favourites = 'TRUNCATE TABLE `i_record_favourites`';
    	$empty_i_record_helpreply = 'TRUNCATE TABLE `i_record_helpreply`';
    	$empty_i_record_outimg = 'TRUNCATE TABLE `i_record_outimg`';
    	$empty_i_talk_content = 'TRUNCATE TABLE `i_talk_content`';
    	$empty_i_talk_list = 'TRUNCATE TABLE `i_talk_list`';
    	$empty_i_user_changeinfo = 'TRUNCATE TABLE `i_user_changeinfo`';
    	$empty_i_user_honor = 'TRUNCATE TABLE `i_user_honor`';
    	$empty_i_user_login_wb = 'TRUNCATE TABLE `i_user_login_wb`';
    	$empty_i_user_priority = 'TRUNCATE TABLE `i_user_priority`';
    	$empty_important_i_record_help = 'TRUNCATE TABLE `i_record_help`';
    	$empty_important_i_record_say = 'TRUNCATE TABLE `i_record_say`';
    	$empty_important_i_user_info = 'TRUNCATE TABLE `i_user_info`';
    	$empty_important_i_user_login = 'TRUNCATE TABLE `i_user_login`';
    	$empty_important_i_user_status= 'TRUNCATE TABLE `i_user_status`';
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
    	
    	
    	$listIn = '/useralbum/10000/100001352533354.jpg';
    	$fileUrl = 'http://ihelpoo-public.stor.sinaapp.com/useralbum/10000/100001352533354.jpg';
    	try {
    		$fh = fopen($fileUrl, 'rb');
    		var_dump($fh);
    		$rsp = $upyun->writeFile($listIn, $fh, True);   //上传图片，自动创建目录
    		fclose($fh);
    		var_dump($rsp);
    	}
    	catch(Exception $e) {
    		echo $e->getCode();
    		echo $e->getMessage();
    	}
    	exit();
    }
    
    
    
}  
?>