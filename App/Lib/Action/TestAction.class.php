<?php

/**
 * 本页仅供测试
 */
class TestAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        exit();
    }
    
    public function index() {
    	
    	exit();
    	$RecordComment = M("RecordComment");
    	$recordId = 22088;
    	$page = 0;
        $count = 20;
        $offset = $page * $count;
        $sayComment = $RecordComment->where("sid = $recordId")
        ->join('i_user_login ON i_record_comment.uid = i_user_login.uid')
        ->join('i_user_login ON i_record_comment.touid = i_user_login.uid')
        ->field('cid,i_user_login.uid,sid,toid,content,image,diffusion_co,time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
        ->limit($offset,$count)->order('cid ASC')->select();
        
        var_dump($sayComment);
        
        
    	
    	//--------------
    	$str = 'http://sae.sina.com.cn/?m=mysqlmng&app_id=ihelpoo&ver=1###';
    	
    	function i_makechickableLinks($text) {
    		$text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_+.~#?&//=]+)', '<a href="\1"><span class="post_link"></span></a>', $text);
    		$text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_+.~#?&//=]+)','\1<a href="http://\2"><span class="post_link"></span></a>', $text);
    		//$text = eregi_replace('([_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3})', '<a href="mailto:\1">\1</a>', $text);
    		return $text;
    	}
    	echo i_makechickableLinks($str);
    	//------------
    	$IUserLogin = D("IUserLogin");
    	$users = $IUserLogin->where()->limit(690,400)->select();
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
    
    
    public function stream()
    {
    	$userloginid = session('userloginid');
        $this->assign('title','个人中心主页 信息流');
        
		$RecordSay = M("RecordSay");
		$UserLogin = M("UserLogin");
		$UserPriority = M("UserPriority");
		$UserStatus = M("UserStatus");
        $recordUserStatus = $UserStatus->find($userloginid);
        $this->assign('recordUserStatus',$recordUserStatus);
        
        /**
         * post publish
         */
        
        if (preg_match("/priority/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "priority";
        } else if (preg_match("/shield/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "shield";
        } else if (preg_match("/time/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "time";
        } else if (preg_match("/index\/help/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "help";
        } else if (preg_match("/index\/group/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "group";
        } else {
            $requestWay = "default";
        }
        
        /**
         * 
         * show $count records every page
         * $count int
         * $offect int Equal current page * count
         */
        $page = i_page_get_num();
        $count = 25;
        $offset = $page * $count;
        
        /**
         * 
         * priority set; rules by i_user_priority
         */
        $pidString = NULL;
        $allIdString = NULL;
        $pidGroupArray = array();
        $isSetPriority = $UserPriority->where("uid = $userloginid")->select();
        if (!empty($isSetPriority)) {
            foreach ($isSetPriority as $priorityRecord) {
                if (!empty($priorityRecord['pid'])) {
                    $pidString .= $priorityRecord['pid'].",";
                    $allIdString .= $priorityRecord['pid'].",";
                    
                    /**
                     * is set group priority
                     */
                    if ($priorityRecord['pid_type'] == 2) {
                    	$pidGroupArray[] = $UserLogin->where("uid = $priorityRecord[pid]")->field('uid,nickname')->find();
                    }
                } else if (!empty($priorityRecord['sid'])) {
                    $sidString .= $priorityRecord['sid'].",";
                    $allIdString .= $priorityRecord['sid'].",";
                }
            }
        }
        
        $select = $RecordSay;
        if ($requestWay == "priority") {
            if (!empty($pidString)) {
                $pidString = substr($pidString, 0, -1);
                $select->where("i_record_say.uid IN ($pidString) AND say_type != '9'");
        	} else {
        		$select->where("say_type != '9'");
        	}
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "priority";
        } else if ($requestWay == "shield") {
            if (!empty($sidString)) {
        	    $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid IN ($sidString) AND say_type != '9'");
        	} else {
        		$select->where("say_type != '9'");
        	}
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "shield";
        } else if($requestWay == "time") {
            if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type != '9'");
        	} else {
        		$select->where("say_type != '9'");
        	}
        	$select->order('i_record_say.time DESC');
        	$streamway = "time";
        } else if($requestWay == "help") {
            if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type = '1'");
        	} else {
        	    $select->where("say_type = '1'");
        	}
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "help";
        } else if($requestWay == "group") {
        	$groupUid = (int)trim($_GET["_URL_"][3]);
        	$isSetGroupListPriority = $UserPriority->where("pid = $groupUid")->select();
        	$pidGroupString = NULL;
        	$pidGroupNums = 0;
	        if (!empty($isSetGroupListPriority)) {
	            foreach ($isSetGroupListPriority as $priorityRecord) {
	                if (!empty($priorityRecord['uid'])) {
	                    $pidGroupString .= $priorityRecord['uid'].",";
	                    $pidGroupNums++;
	                }
	            }
	        } else {
	        	redirect('/stream', 3, '组织成员为空 3秒后页面跳转...');
	        }
	        $pidGroupString = substr($pidGroupString, 0, -1);
        	$select->where("i_record_say.uid IN ($pidGroupString) AND say_type != '9'");
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "group";
        	$this->assign('groupUserNums',$pidGroupNums);
        	$groupUserRecord = $UserLogin->find($groupUid);
        	$this->assign('groupUserRecord',$groupUserRecord);
        } else {
        	if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type != '9'");
        	} else {
        		$select->where("say_type != '9'");
        	}
        	$select->order('i_record_say.last_comment_ti DESC');
        	$streamway = "default";
        }
        $recordSay = $select->join('i_user_login ON i_record_say.uid = i_user_login.uid')
        ->join('i_user_info ON i_record_say.uid = i_user_info.uid')
        ->join('i_op_specialty ON i_user_info.specialty_op = i_op_specialty.id')
		->field('sid,i_user_login.uid,say_type,content,image,url,comment_co,diffusion_co,hit_co,time,from,last_comment_ti,nickname,sex,birthday,enteryear,type,online,active,icon_url,i_user_info.specialty_op,i_op_specialty.name,i_op_specialty.number,i_op_specialty.academy')
		->limit($offset,$count)->select();
		var_dump($recordSay);
		$userRecordSayUidBefore = NULL;
		foreach ($recordSay as $record) {
			if ($userRecordSayUidBefore == $record['uid']) {
				$recordSayArray[] = array(
					'sid' => $record['sid'],
					'uid' => $record['uid'],
					'say_type' => $record['say_type'],
					'content' => stripslashes($record['content']),
					'image' => $record['image'],
					'url' => $record['url'],
					'comment_co' => $record['comment_co'],
					'diffusion_co' => $record['diffusion_co'],
					'hit_co' => $record['hit_co'],
					'time' => $record['time'],
					'from' => $record['from'],
					'last_comment_ti' => $record['last_comment_ti'],
					'nickname' => $record['nickname'],
					'sex' => $record['sex'],
					'birthday' => $record['birthday'],
					'enteryear' => $record['enteryear'],
					'type' => $record['type'],
					'online' => $record['online'],
					'active' => $record['active'],
					'icon_url' => $record['icon_url'],
					'specialty_op' => $record['specialty_op'],
					'name' => $record['name'],
					'number' => $record['number'],
					'academy' => $record['academy'],
					'repatenums' => 1,
				);
			} else {
				$recordSayArray[] = array(
					'sid' => $record['sid'],
					'uid' => $record['uid'],
					'say_type' => $record['say_type'],
					'content' => stripslashes($record['content']),
					'image' => $record['image'],
					'url' => $record['url'],
					'comment_co' => $record['comment_co'],
					'diffusion_co' => $record['diffusion_co'],
					'hit_co' => $record['hit_co'],
					'time' => $record['time'],
					'from' => $record['from'],
					'last_comment_ti' => $record['last_comment_ti'],
					'nickname' => $record['nickname'],
					'sex' => $record['sex'],
					'birthday' => $record['birthday'],
					'enteryear' => $record['enteryear'],
					'type' => $record['type'],
					'online' => $record['online'],
					'active' => $record['active'],
					'icon_url' => $record['icon_url'],
					'specialty_op' => $record['specialty_op'],
					'name' => $record['name'],
					'number' => $record['number'],
					'academy' => $record['academy'],
					'repatenums' => 0,
				);
			}
			$userRecordSayUidBefore = $record['uid'];
		}
		
        $this->assign('streamway',$streamway);
		$this->assign('recordSay',$recordSayArray);
		//var_dump($recordSayArray);
		
		/**
		 * show new active nums
		 */
		$MsgActive = M("MsgActive");
		$msgActiveNewNums = $MsgActive->where("uid = $userloginid AND deliver = 0")->count();
		$this->assign('msgActiveNewNums',$msgActiveNewNums);
        
        /**
         * show user info
         */
		$UserInfo = M("UserInfo");
        $recordUserInfo = $UserInfo->find($userloginid);
        $this->assign('recordUserInfo',$recordUserInfo);
        
        /**
         * show online user nums
         */
        $IWebStatus = D("IWebStatus");
        $recordOnlineUserNums = $IWebStatus->paraExists('online_user_nums');
        $this->assign('onlineUserNums',$recordOnlineUserNums['valuechar']);
        
        /**
         * show user honor nums
         */
        $UserHonor = M("UserHonor");
        $totalUserHonorNums = $UserHonor->where("uid = $userloginid")->count();
        $this->assign('totalUserHonorNums',$totalUserHonorNums);
        
        /**
         * user shop
         */
        $UserShop = M("UserShop");
        $recordUserShop = $UserShop->find($userloginid);
        if (!empty($recordUserShop['uid'])) {
        	$this->assign('recordUserShop',$recordUserShop);
        }
        
        /**
         * user shopping
         */
        $RecordCommodityassess = M("RecordCommodityassess");
        $goodOnCommodity = $RecordCommodityassess->where("uid = $userloginid AND status = 1")->count();
        $goodOnNeedsure = $RecordCommodityassess->where("uid = $userloginid AND status = 2")->count();
        $goodOnAssess = $RecordCommodityassess->where("uid = $userloginid AND status = 4")->count();
        $this->assign('goodOnCommodity', $goodOnCommodity);
        $this->assign('goodOnNeedsure', $goodOnNeedsure);
        $this->assign('goodOnAssess', $goodOnAssess);
        
        /**
         * user group view
         */
        if (!empty($pidGroupArray)) {
        	$this->assign('pidGroupArray', $pidGroupArray);
        }
        
        /**
         * weibo
         */
        $UserLoginWb = M("UserLoginWb");
        $recordUserLoginWb = $UserLoginWb->where("uid = $userloginid")->find();
    	if (!empty($recordUserLoginWb['uid'])) {
            $this->assign('isAlreadyBind',$recordUserLoginWb);
        }
        
        /**
         * calculate online user numbers. calculate per 30 second
         */
        $IWebStatus = D("IWebStatus");
        $recordOnlineUserNums = $IWebStatus->paraExists('online_user_nums');
        $userOnlineObject = $UserLogin->where("online != 0")->join('i_user_status ON i_user_status.uid = i_user_login.uid')->join('i_user_info ON i_user_info.uid = i_user_login.uid')->select();
        if (15 < (time() - $recordOnlineUserNums['valueint'])) {
            foreach ($userOnlineObject as $userOnlineOne) {
                if (900 < (time() - $userOnlineOne['last_active_ti'])) {
                    $updateUserOnlineStatusData = array(
                    	'uid' => $userOnlineOne['uid'],
            	        'online' => 0,
            	    );
                    $UserLogin->save($updateUserOnlineStatusData);
                }
            }
        }
        $userOnlineNums = $UserLogin->where("online != 0")->count();
        $updateUserOnlineNums = array(
            'parameter' => $recordOnlineUserNums['parameter'],
            'valuechar' => $userOnlineNums,
            'valueint' => time(),
        );
        $IWebStatus->save($updateUserOnlineNums);
        
    }

    //模拟器首页
    public function imit() {
        echo "<h2>Sae服务模拟器功能测试(以下服务在本地也可以运行)：</h2>";
        echo "<div>请结合源码观看效果</div>";
        echo "<h3><a href='" . __URL__ . "/counter' target='_blank'>Counter</a>   <a href='" . __URL__ . "/kv' target='_blank'>KVDB</a>  <a href='" . __URL__ . "/rank' target='_blank'>Rank</a>  <a href='" . __URL__ . "/mc' target='_blank'>Memcache</a>   <a href='" . __URL__ . "/tq' target='_blank'>TaskQueue</a>   <a href='" . __URL__ . "/storage' target='_blank'>Storage</a>    <a href='" . __URL__ . "/mail' target='_blank'>Mail</a>   <a href='" . __URL__ . "/fetchurl' target='_blank'>fetchURL</a>  <a href='" . __URL__ . "/wrappers' target='_blank'> Wrappers</a> <a href='" . __URL__ . "/saeimage' target='_blank'>SaeImage</a>  <a href='" . __URL__ . "/saemysql' target='_blank'>SaeMysql</a></h3>";
    }

    //平滑性测试
    public function pinghua() {
        echo "<h2>平滑性测试(不用特别学习SAE服务，使用ThinkPHP内置功能也使用了SAE服务)：</h2>";
        echo "<div>请结合源码观看效果</div>";
        echo "<h3><a href='" . __URL__ . "/mysql' target='_blank'>数据库</a>  <a href='" . __URL__ . "/scache' target='_blank'>S缓存</a>   <a href='" . __URL__ . "/fcache' target='_blank'>F缓存</a> <a href='" . __URL__ . "/upload' target='_blank'>上传文件</a>  <a href='" . __URL__ . "/image' target='_blank'>图片处理</a>  <a href='" . __URL__ . "/log' target='_blank'>查看日志</a></h3>";
    }
    
    public function mysql(){
        echo '数据操作使用了SaeMysql服务，做到了分布式和读写分离,可以通过查看配置得知,在本地和SAE环境下查看会是不一样的结果：<br />';
        echo '是否分布式连接：';
        dump(C('DB_DEPLOY_TYPE'));
        echo '数据库地址为：';
        dump(C('DB_HOST'));
        echo '是否读写分离:';
        dump(C('DB_RW_SEPARATE'));
    }

    public function new_features(){
        echo "<h2>新功能测试：</h2>";
        echo "<div>请结合源码观看效果</div>";
        echo "<h3><a href='" . __URL__ . "/sms' target='_blank'>短信预警</a>  <a href='" . __URL__ . "/sae_runtime' target='_blank'>SAE Runtime模式</a>   <a href='" . __URL__ . "/spare_db' target='_blank'>备用数据库</a>  <a href='" . __URL__ . "/upgrade_notice' target='_blank'>升级短信通知</a></h3>";
    }

    public function sms(){
        if(!IS_SAE){
            exit('  请在SAE环境下测试短信预警功能~');
        }
            echo '
                请先配置'.CONF_PATH.'config_sae.php 文件。<br />
                设置： SMS_ON 为true 开启短信预警功能。<br />
                设置 :   SMS_MOBILE  为你的接收短信的手机号。<br />
                另外还要在SAE平台对当前应用开启短信服务。<br /><br />
                ';

    if(C('SMS_ON')){
       // M('unkowntable')->select();//执行一段有问题的代码。 数据库表不存在
       //unkownfunction();//fatalError ， 请先注释上一行， 在去掉本行注释再测试下。
      sae_send_sms('有订单交易失败，请在SAE日志中心查看详情','订单号：12345,用户：luofei614,交易时间：2012-6-15');//你还可以自定义发送一条错误短信,请先注释上面两行代码，比如你可以在你代码中判断是否订单交易失败，如果交易失败发送短信。
        

        echo '看看你有没有收到短信， 每次发短信间隔最小时间为15秒，请15秒后再测试。<br />
        短信中只会显示部分提示信息，你需要到SAE的日志中心，查看debug日志看详细报警信息<br />
        你还可以增加发送短信的间隔时间， 配置项 SMS_INTERVAL。 在正式项目中，增加短信发送的间隔时间将会为你节约短信费用
        ';
    }

    }

    public function upgrade_notice(){
        echo '
                请先配置'.CONF_PATH.'config_sae.php 文件或者'.CONF_PATH.'config.php文件。<br />
                 \'UPGRADE_NOTICE_ON\'=>true,//开启短信升级提醒功能 <br />
        \'UPGRADE_NOTICE_DEBUG\'=>true, <br />//调试默认，设置为true后UPGRADE_NOTICE_CHECK_INTERVAL配置项不会起作用，每次都会检测，调试完毕后，请设置此配置项为false<br />
        \'UPGRADE_NOTICE_MOBILE\'=>\'136456789\',//接受短信的手机号<br />
        \'UPGRADE_NOTICE_CHECK_INTERVAL\' => 604800,//检测频率,单位秒,默认是一周<br />
        \'UPGRADE_CURRENT_VERSION\'=>\'0\',//升级后的版本号，会在短信中告诉你填写什么<br /><br />
        配置完后，开通短信服务，然后再访问网站，看看能否收到升级短信
                ';
    }

    public function sae_runtime(){
        echo '请在入口文件定义常量，SAE_RUNTIME为true<br />';
        echo '请在本地打开命令行， cd 到项目所在文件夹，执行命令： php index.php <br />';
        echo '此时会在'.APP_PATH.'Sae_Runtime目录下批量删除缓存文件， 请将生成的缓存文件上传到SAE<br />';
        echo '开启Sae Runtime模式后 ， 在SAE上运行框架将不会占用Memcache，能节约云豆并能避免Memcache的瓶颈';
    }

    public function spare_db(){
          if(!IS_SAE){
            exit('  请在SAE环境下测试备用数据库功能~');
        }

               echo  '请先配置'.CONF_PATH.'config_sae.php 文件 配置你的备用数据库信息。<br />
                并设置 SPARE_DB_DEBUG 为true 进行调试，此时将模拟mysql超额被禁用的状态。 调试完后在设置SPARE_DB_DEBUG为false。<br />
                开启备用数据库后，myql因超额被禁用，自动访问备用数据库，保证网站正常浏览。<br />
                注意：备用数据库要进行跨应用授权，详情见：<a href="http://sae.sina.com.cn/?m=devcenter&catId=192" target="_blank">http://sae.sina.com.cn/?m=devcenter&catId=192</a><br /><br />

                在备用数据库和当前项目数据库中都建立一个think_spare表, 输入不同的数据。 测试一下看看 数据是显示的哪个数据库的
                ';

                $data=M('Spare')->select();// 在备用数据库和当前项目数据库中都建立一个think_spare表, 输入不同的数据。 测试一下看看 数据是显示的哪个数据库的
                dump($data);
    }

    public function log() {
        log::write('写入日志测试');
        echo '日志已写入，在SAE平台请在日志中心查看（选择debug类型）；在本地环境请在' . LOG_PATH . '查看';
    }

    public function image() {
        echo 'ThinkPHP的验证码功能使用SaeVcode服务；水印、缩略图等功能，使用了SaeImage服务，本示例测试验证码<br />';
        echo "<img src='" . __URL__ . "/verify'/>";
    }

    public function verify() {
        import("@.ORG.Image");
        Image::buildImageVerify();
    }

    //S缓存的平滑性检测
    public function scache() {
        S('test', 'testvalue', 60);
        if (IS_SAE) {
            echo '您正在SAE环境下测试，您的缓存数据将保存在Memcache中<br />';
            $m = memcache_init();
            echo '用Mecache获得的值为：' . $m->get($_SERVER['HTTP_APPVERSION'].'/test') . '<br />';
            echo '用S函数获得的值为：' . S('test') . '<br />';
        } else {
            echo '您正在本地环境进行测试， 你的缓存数据保存在了' . DATA_PATH . '目录下<br />';
            echo '用S函数获得的值为：' . S('test');
        }
    }

    //F缓存的平滑性，使用前需要在SAE平台对KVDB进行初始化
    public function fcache() {
        F('test2', 'testvalue2');
        if (IS_SAE) {
            echo '您正在SAE环境下测试，您的数据将保存在KVDB中<br />';
            $kv = new SaeKvClient();
            $kv->init();
            echo '使用KVDB获得的值：' . $kv->get($_SERVER['HTTP_APPVERSION'].'/test2') . '<br />';
            echo '使用F函数获得值为：' . F('test2');
        } else {
            echo '您正在本地环境下测试，您的数据将保存在' . DATA_PATH . '目录下<br />';
            echo '使用F函数获得值为:' . F('test2');
        }
    }

    //上传文件平滑性测试

    public function upload() {
        if (!empty($_FILES)) {
            import("@.ORG.UploadFile");
            $config=array(
                'allowExts'=>array('jpg','gif','png'),
                'savePath'=>'./Public/upload/',
                'saveRule'=>'time',
            );
            $upload = new UploadFile($config);
            $upload->imageClassPath="@.ORG.Image";
            $upload->thumb=true;
            $upload->thumbMaxHeight=100;
            $upload->thumbMaxWidth=100;
            if (!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            } else {
                $info = $upload->getUploadFileInfo();
                $this->assign('filename', $info[0]['savename']);
            }
        }
        $this->display();
    }

    //删除图片
    public function unlink() {
        sae_unlink('./Public/upload/' . $_GET['filename']);
        sae_unlink('./Public/upload/thumb_' . $_GET['filename']);
        $this->success('删除成功');
    }

    //Counter测试
    public function counter() {
        $c = new SaeCounter(); //实例化
        $c->create("test"); //创建计算器
        $c->set("test", 30); //设置值
        $ret = $c->get("test"); //获得值
        dump($ret);
        $ret = $c->incr("test"); //增加值
        dump($ret);
        $ret = $c->decr("test"); //减少值
        dump($ret);
    }

    //KVDB测试
    public function kv() {
        $k = new SaeKVClient();
        $k->init();
        $k->set('a', 'aaa'); //建立一条字符串数据
        $ret = $k->get('a'); //获得a的值
        dump($ret);
        $k->set('b', array('a', 'b', 'c')); //可存储数组或对象
        $ret = $k->get("b"); //获得b的值
        dump($ret);
        $k->delete("a"); //删除a
    }

    //rank排行榜测试
    public function rank() {
        $r = new SaeRank();
        $r->create("list", 100); //创建一个榜单。
        $r->set("list", "a", 3); //设置值
        $r->set("list", "b", 4);
        $r->set("list", "c", 1);
        $r->increase("list", "c"); //增加值
        $ret = $r->getList("list", true); //获得排行榜
        dump($ret);
        $ret = $r->getRank("list", "a"); //获得某个键的排名,注意是从0开始
        dump($ret);
        $r->clear("list"); //清空排行榜
    }

    //memcache测试
    //内置了memcache模拟器，即使本地环境不支持memcache也能运行。
    public function mc() {
        $m = memcache_init();
        $m->set("a", "aaa"); //设置值
        $ret = $m->get("a"); //获得值
        dump($ret);
    }

    //taskqueue 任务列队测试，本地环境需要配置curl
    public function tq() {
        $t = new SaeTaskQueue("test");
        $t->addTask("http://" . $_SERVER['HTTP_HOST'] . __URL__ . "/tq_test1"); //添加列队任务1
        $t->addTask("http://" . $_SERVER['HTTP_HOST'] . __URL__ . "/tq_test2", "k1=v1&k2=v2", true); //添加列队任务2
        if (!$t->push()) {
            echo '出错:' . $t->errmsg();
        } else {
            echo '执行成功！请查看[' . LOG_PATH . 'sae_debug.log' . ']文件中的日志';
        }
    }

    //列队任务1
    public function tq_test1() {
        sae_debug("列队任务1被执行"); //在本地请查看日志：App\Runtime\Logs\sae_debug.log
    }

    //列队任务2
    public function tq_test2() {
        sae_debug("列队任务2被执行,k1的值：{$_POST['k1']},k2的值:{$_POST['k2']}"); //在本地请查看日志：App\Runtime\Logs\sae_debug.log
    }

    //storage测试
    public function storage() {
        $s = new SaeStorage();
        $s->write('Public', 'example/thebook', 'bookcontent'); //写入文件
        $ret = $s->read('Public', 'example/thebook'); //读取文件
        dump($ret);
        $ret = $s->getUrl('Public', 'example/thebook'); //获得地址
        dump($ret);
    }

    //Mail测试
    public function mail() {
        //现在暂不支持gmail邮箱和附件上传，建议使用新浪邮箱测试。注意需要开启你邮箱的smtp功能。
        $mail = new SaeMail();
        $ret = $mail->quickSend('121670155@qq.com', '邮件标题', '邮件内容', 'saemailtest@sina.com', '123456');
        if ($ret === false) {
            var_dump($mail->errno(), $mail->errmsg());
        } else {
            echo "邮件发送成功，请更改源码，将邮箱改为自己的测试";
        }
    }

    //fetchURL测试
    public function fetchurl() {
        $f = new SaeFetchurl();
        echo $f->fetch('http://sina.cn');
    }

    //wrappers 测试
    public function wrappers() {
        file_put_contents('saemc://name', 'Memcache');
        echo file_get_contents('saemc://name');
        echo '<br />';
        file_put_contents('saestor://Public/upload/test.txt', 'SaeStorage');
        echo file_get_contents('saestor://Public/upload/test.txt');
    }

    //SaeImage 测试
    public function saeimage() {
        //从网络上抓取要合成的多张图片
        $img1 = file_get_contents('http://ss2.sinaimg.cn/bmiddle/53b05ae9t73817f6bf751&690');
        $img2 = file_get_contents('http://timg.sjs.sinajs.cn/miniblog2style/images/common/logo.png');
        $img3 = file_get_contents('http://i1.sinaimg.cn/home/deco/2009/0330/logo_home.gif');

//实例化SaeImage并取得最大一张图片的大小，稍后用于设定合成后图片的画布大小
        $img = new SaeImage($img1);
        $size = $img->getImageAttr();

//清空$img数据
        $img->clean();

//设定要用于合成的三张图片（如果重叠，排在后面的图片会盖住排在前面的图片）
        $img->setData(array(
            array($img1, 0, 0, 1, SAE_TOP_LEFT),
            array($img2, 0, 0, 0.5, SAE_BOTTOM_RIGHT),
            array($img3, 0, 0, 1, SAE_BOTTOM_LEFT),
        ));

//执行合成
        $img->composite($size[0], $size[1]);

//输出图片
        $img->exec('jpg', true);
    }

    //saemysql,  本地支持SaeMysql，不过建议用ThinkPHP的Model进行对数据库的操作
    public function saemysql() {
        $mysql = new SaeMysql();
        $mysql->runSql('create table saetest(`id` int(11) NOT NULL);');
        echo '在本地时请先配置好数据库，本程序执行完毕后会向数据库中建立名为saetest数据表';
    }

}

?>