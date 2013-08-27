<?php
/**
 * update for ihelpoo move bate 3 to bate 4
 * @author chenkehao
 */
class UpdateAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
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
    
    /**
     * truncate table
     */
    public function truncatetable()
    {
    	/**
    	echo "truncate table i_msg_active <br/>";
    	$MsgActive = M("MsgActive");
        $MsgActive->query("TRUNCATE TABLE i_msg_active");
        
        echo "truncate table i_msg_comment <br/>";
    	$MsgComment = M("MsgComment");
        $MsgComment->query("TRUNCATE TABLE i_msg_comment");
        
        echo "truncate table i_record_comment <br/>";
    	$RecordComment = M("RecordComment");
        $RecordComment->query("TRUNCATE TABLE i_record_comment");
        
        echo "truncate table i_record_diffusion <br/>";
    	$RecordDiffusion = M("RecordDiffusion");
        $RecordDiffusion->query("TRUNCATE TABLE i_record_diffusion");
        
        echo "truncate table i_record_dynamic <br/>";
    	$RecordDynamic = M("RecordDynamic");
        $RecordDynamic->query("TRUNCATE TABLE i_record_dynamic");
        
        echo "truncate table i_record_help <br/>";
        $RecordHelp = M("RecordHelp");
        $RecordHelp->query("TRUNCATE TABLE i_record_help");
        
        echo "truncate table i_record_helpreply <br/>";
        $RecordHelpreply = M("RecordHelpreply");
        $RecordHelpreply->query("TRUNCATE TABLE i_record_helpreply");
        
        echo "truncate table i_record_outimg <br/>";
        $RecordOutimg = M("RecordOutimg");
        $RecordOutimg->query("TRUNCATE TABLE i_record_outimg");
        
        echo "truncate table i_record_say <br/>";
        $RecordSay= M("RecordSay");
        $RecordSay->query("TRUNCATE TABLE i_record_say");
        
        echo "truncate table i_user_album <br/>";
        $UserAlbum = M("UserAlbum");
        $UserAlbum->query("TRUNCATE TABLE i_user_album");
        
        echo "truncate table i_user_invite <br/>";
        $UserInvite = M("UserInvite");
        $UserInvite->query("TRUNCATE TABLE i_user_invite");
        
        echo "truncate table i_user_login_wb <br/>";
        $UserLoginWb = M("UserLoginWb");
        $UserLoginWb->query("TRUNCATE TABLE i_user_login_wb");
        
        echo "truncate table i_user_priority <br/>";
        $UserPriority = M("UserPriority");
        $UserPriority->query("TRUNCATE TABLE i_user_priority");
        */
    	
        echo "delete i_user_login <br/>";
        $UserLogin = M("UserLogin");
        $UserLogin->where("uid < 15000")->delete();
        
        echo "delete i_user_info <br/>";
    	$UserInfo = M("UserInfo");
    	$UserInfo->where("uid < 15000")->delete();
    	
    	echo "delete i_user_status <br/>";
    	$UserStatus = M("UserStatus");
    	$UserStatus->where("uid < 15000")->delete();
    	redirect('/update/msgactive', 1, 'next');
    }

    /**
     * i_msg_active
     */
    public function msgactive()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/msgactive?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
            
    		echo '迁移i_msg_active<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		$MsgActive = M("MsgActive");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$MsgActive->add($data);
    			}
    		}
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/msgactive?p='.$page, 1, 'while');
    		}
    	}
    	redirect('/update/msgcomment', 1, 'next');
    }
    
    /**
     * i_msg_comment
     */
    public function msgcomment()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/msgcomment?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
            
    		echo '迁移i_msg_comment<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		$MsgComment = M("MsgComment");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$MsgComment->add($data);
    			}
    		}
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/msgcomment?p='.$page, 1, 'while');
    		}
    	}
    	redirect('/update/recordcomment', 1, 'next');
    }
    
    /**
     * i_record_comment
     */
    public function recordcomment()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/recordcomment?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_record_comment<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordComment = M("RecordComment");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$RecordComment->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/recordcomment?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/recorddiffusion', 1, 'next');
    }
    
    /**
     * i_record_diffusion
     */
    public function recorddiffusion()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/recorddiffusion?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_record_diffusion + view<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordDiffusion = M("RecordDiffusion");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$data['view'] = '';
    				$RecordDiffusion->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/recorddiffusion?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/recorddynamic', 1, 'next');
    }
    
    /**
     * i_record_dynamic
     */
    public function recorddynamic()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/recorddynamic?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_record_dynamic<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordDynamic = M("RecordDynamic");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$RecordDynamic->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/recorddynamic?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/recordhelp', 1, 'next');
    }
    
    /**
     * i_record_help
     */
    public function recordhelp()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/recordhelp?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_record_help<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordHelp = M("RecordHelp");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$RecordHelp->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/recordhelp?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/recordhelpreply', 1, 'next');
    }
    
    /**
     * i_record_helpreply
     */
    public function recordhelpreply()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/recordhelpreply?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_record_helpreply<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordHelpreply = M("RecordHelpreply");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$RecordHelpreply->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/recordhelpreply?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/recordoutimg', 1, 'next');
    }
    
    /**
     * i_record_outimg
     */
    public function recordoutimg()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/recordoutimg?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_record_outimg<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordOutimg = M("RecordOutimg");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$RecordOutimg->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/recordoutimg?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/recordsay', 1, 'next');
    }
    
    /**
     * i_record_say
     */
    public function recordsay()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/recordsay?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_record_say + school_id<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordSay = M("RecordSay");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$data['school_id'] = 1;
    				$RecordSay->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/recordsay?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/useralbum', 1, 'next');
    }
    
    /**
     * i_user_album
     */
    public function useralbum()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/useralbum?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_user_album<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserAlbum = M("UserAlbum");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$UserAlbum->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/useralbum?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/userinfo', 1, 'next');
    }
    
    /**
     * i_user_info < 15000
     */
    public function userinfo()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/userinfo?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_user_info<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserInfo = M("UserInfo");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				if ($data['academy_op'] == '12') {
    					$data['academy_op'] = '13';
    				}
    				if ($data['academy_op'] == '11') {
    					$data['academy_op'] = '14';
    				}
    				if ($data['academy_op'] == '15') {
    					$data['academy_op'] = '11';
    				}
    				if ($data['academy_op'] == '99') {
    					$data['academy_op'] = '0';
    				}
    				$UserInfo->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/userinfo?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/userinvite', 1, 'next');
    }
    
    /**
     * i_user_invite
     */
    public function userinvite()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/userinvite?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_user_invite<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserInvite = M("UserInvite");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$UserInvite->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/userinvite?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/userlogin', 1, 'next');
    }
    
    /**
     * i_user_login < 15000
     */
    public function userlogin()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/userlogin?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_user_login +c school<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserLogin = M("UserLogin");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$data['nickname'];//handle preg_replace("/[^\x{4e00}-\x{9fa5}]/iu",'',$str);
    				$data['school'] = 1;
    				$UserLogin->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/userlogin?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/userloginwb', 1, 'next');
    }
    
    /**
     * i_user_login_wb
     */
    public function userloginwb()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/userloginwb?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_user_login_wb<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserLoginWb = M("UserLoginWb");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$UserLoginWb->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/userloginwb?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/userpriority', 1, 'next');
    }
    
    /**
     * i_user_priority
     */
    public function userpriority()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/userpriority?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_user_priority + group_id<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserPriority = M("UserPriority");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$data['group_id'] = 0;
    				$UserPriority->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/userpriority?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/userstatus', 1, 'next');
    }
    
    /**
     * i_user_status < 15000
     */
    public function userstatus()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/userstatus?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移i_user_status<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserStatus = M("UserStatus");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$UserStatus->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/userstatus?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/movezzulicleandb', 1, 'next');
    }
    
    /**	
     * zzuli
     * move data from zzuli
     */
    public function movezzulicleandb()
    {
    	echo '清理表 i_user_status i_user_info ...';
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
    	redirect('/update/zzuliuserlogin', 1, 'next');
    }
    
    /**
     * zzuli i_user_login
     */
    public function zzuliuserlogin()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://zzuli.ihelpoo.com/updateversion4/zzuliuserlogin?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移zzuli i_user_login +c school<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserLogin = M("UserLogin");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$recordUserLogin = $UserLogin->find($data['uid']);
    				if (empty($recordUserLogin['uid'])) {
    					$data['school'] = 2;
    					$data['nickname'] = preg_replace('/[^a-zA-Z\x{4e00}-\x{9fa5}{0-9}_]/u','',$data['nickname']);
    					$UserLogin->add($data);
    				} else if ($recordUserLogin['active'] > $data['active']) {
    					$data = $recordUserLogin;
    					$data['school'] = 2;
    					$data['nickname'] = preg_replace('/[^a-zA-Z\x{4e00}-\x{9fa5}{0-9}_]/u','',$data['nickname']);
    					$UserLogin->save($data);
    				}
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/zzuliuserlogin?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/zzuliuserinfo', 1, 'next');
    }
    
    /**
     * zzuli i_user_info
     */
    public function zzuliuserinfo()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://zzuli.ihelpoo.com/updateversion4/zzuliuserinfo?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移zzuli i_user_info<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserInfo = M("UserInfo");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$recordUserInfo = $UserInfo->find($data['uid']);
    				if (empty($recordUserInfo['uid'])) {
    					$data['academy_op'] = $data['academy_op'] + 22;
    					$data['specialty_op'] = $data['specialty_op'] + 62;
    					if ($data['dormitory_op'] <= '6') {
    						$data['dormitory_op'] = $data['dormitory_op'] + 48;
    					} else if ($data['dormitory_op'] == '8') {
    						$data['dormitory_op'] = '55';
    					} else if ($data['dormitory_op'] == '10') {
    						$data['dormitory_op'] = '56';
    					} else if ($data['dormitory_op'] == '11') {
    						$data['dormitory_op'] = '57';
    					} else if ($data['dormitory_op'] == '12') {
    						$data['dormitory_op'] = '58';
    					} else if ($data['dormitory_op'] == '31') {
    						$data['dormitory_op'] = '59';
    					} else if ($data['dormitory_op'] == '33') {
    						$data['dormitory_op'] = '60';
    					} else if ($data['dormitory_op'] == '34') {
    						$data['dormitory_op'] = '61';
    					} else if ($data['dormitory_op'] == '40') {
    						$data['dormitory_op'] = '62';
    					} else if ($data['dormitory_op'] == '41') {
    						$data['dormitory_op'] = '63';
    					} else if ($data['dormitory_op'] == '42') {
    						$data['dormitory_op'] = '64';
    					} else if ($data['dormitory_op'] == '43') {
    						$data['dormitory_op'] = '65';
    					}
    					$UserInfo->add($data);
    				}
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/zzuliuserinfo?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/zzuliuserstatus', 1, 'next');
    }
    
    /**
     * zzuli i_user_status < 15000
     */
    public function zzuliuserstatus()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://zzuli.ihelpoo.com/updateversion4/zzuliuserstatus?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移zzuli i_user_status<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserStatus = M("UserStatus");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$recordUserStatus = $UserStatus->find($data['uid']);
    				if (empty($recordUserStatus['uid'])) {
    					$UserStatus->add($data);
    				}
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/zzuliuserstatus?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/zzulirecordsay', 1, 'next');
    }
    
    /**
     * zzuli i_record_say
     */
    public function zzulirecordsay()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://zzuli.ihelpoo.com/updateversion4/zzulirecordsay?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移 zzuli i_record_say + school_id<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordSay = M("RecordSay");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$data['sid'] = $data['sid'] + 40000;
    				$data['school_id'] = 2;
    				$RecordSay->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/zzulirecordsay?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/zzulirecordcomment', 1, 'next');
    }
    
    /**
     * zzuli i_record_comment
     */
    public function zzulirecordcomment()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://zzuli.ihelpoo.com/updateversion4/zzulirecordcomment?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移zzuli i_record_comment<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordComment = M("RecordComment");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$data['cid'] = '';
    				$data['sid'] = $data['sid'] + 40000;
    				$RecordComment->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/zzulirecordcomment?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/zzulirecordhelp', 1, 'next');
    }
    
    /**
     * zzuli i_record_help
     */
    public function zzulirecordhelp()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://zzuli.ihelpoo.com/updateversion4/zzulirecordhelp?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移zzuli i_record_help<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordHelp = M("RecordHelp");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$data['hid'] = '';
    				$data['sid'] = $data['sid'] + 40000;
    				$RecordHelp->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/zzulirecordhelp?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/zzulirecordhelpreply', 1, 'next');
    }
    
    /**
     * zzuli i_record_helpreply
     */
    public function zzulirecordhelpreply()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://zzuli.ihelpoo.com/updateversion4/zzulirecordhelpreply?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移zzuli i_record_helpreply<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$RecordHelpreply = M("RecordHelpreply");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$data['id'] = '';
    				$data['sid'] = $data['sid'] + 40000;
    				$RecordHelpreply->add($data);
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/zzulirecordhelpreply?p='.$page, 1, 'while');
    		} 	
    	}
    	redirect('/update/zzuliuserpriority', 1, 'next');
    }
    
    /**
     * zzuli i_user_priority
     */
    public function zzuliuserpriority()
    {
    	$page = i_page_get_num();
    	++$page;
    	$url = "http://www.ihelpoo.com/updateversion4/zzuliuserpriority?p=".$page;
    	$datacontents = file_get_contents($url);
    	$datacontentArray = json_decode($datacontents,TRUE);
    	if (is_array($datacontentArray)) {
    		$total = $datacontentArray['total'];
    		$count = $datacontentArray['count'];
    		$page = $datacontentArray['page'];
    		$handlednums = $page * $count;
    		echo '迁移zzuli i_user_priority + group_id<br/>';
    		echo $info = "总记录：".$total."，已处理：".$handlednums.", 当前页：".$page."...";
    		
    		$UserPriority = M("UserPriority");
    		foreach ($datacontentArray as $data) {
    			if (is_array($data)) {
    				$recordUserPriority = $UserPriority->where("(uid = $data[uid] AND pid = $data[pid]) OR (uid = $data[uid] AND sid = $data[sid])")->find();
    				if (empty($recordUserPriority['id'])) {
    					$data['id'] = '';
    					$UserPriority->add($data);
    				}
    			}
    		}
    		
    		while ($handlednums < $total) {
    			++$page;
    			redirect('/update/zzuliuserpriority?p='.$page, 1, 'while');
    		} 	
    	}
    	echo '升级成功啦，哈哈哈 :D';
    }
    
}  
?>