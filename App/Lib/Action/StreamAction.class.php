<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class StreamAction extends Action
{

    protected function _initialize()
    {
        $userloginid = session('userloginid');
        if (!empty($userloginid)) {
            i_db_update_activetime($userloginid);
    		$UserLogin = M("UserLogin");
    		$userloginedrecord = $UserLogin->find($userloginid);
    		$this->assign('userloginedrecord',$userloginedrecord);
        } else {
            redirect('/user/notlogin', 0, '你还没有登录呢...');
        }
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
        $userloginid = session('userloginid');
        $recordSchoolInfo = i_school_domain();
        $title = "信息流 个人中心 - " . $recordSchoolInfo['school'];

        $this->assign('title', $title);
        $this->assign('thisschoolid', $recordSchoolInfo['id']);
        $this->assign('schoolname', $recordSchoolInfo['school']);

        $RecordSay = M("RecordSay");
        $UserLogin = M("UserLogin");
        $UserPriority = M("UserPriority");
        $UserStatus = M("UserStatus");
        $recordUserStatus = $UserStatus->find($userloginid);
        $this->assign('recordUserStatus', $recordUserStatus);

        $ISysParameter = D("ISysParameter");

        /**
         * post publish
         */
        if ($this->isPost()) {
            $validate = array(
                array('textareacontent', 'require', '内容不能为空'),
                array('videourl', 'url', '视频地址格式错误', 2),
                array('help_is', 'number', '信息类型错误'),
                array('authority', 'number', 'authority错误'),
                array('reward_coins', 'number', 'reward_coins格式错误'),
                array('verificationcode', 'number', '验证码格式错误'),
                array('schoolpublishid', 'number', '验证码格式错误'),
            );
            $RecordSay->setProperty("_validate", $validate);
            $result = $RecordSay->create();
            if (!$result) {
                $publishError = $RecordSay->getError();
                $this->ajaxReturn(0, $publishError, 'error');
            } else {
                $content = i_makechickableLinks(trim(addslashes(strip_tags($_POST["textareacontent"], "<a>"))));
                $imageurls = htmlspecialchars(strtolower(trim($_POST["imageurls"])));
                $videourl = trim($_POST["videourl"]);
                $atusers = htmlspecialchars(addslashes(strtolower(trim($_POST["atusers"]))));
                $help_is = (int)htmlspecialchars(strtolower(trim($_POST["help_is"])));
                $schoolpublishid = (int)htmlspecialchars(strtolower(trim($_POST["schoolpublishid"])));
                $authority = (int)htmlspecialchars(strtolower(trim($_POST["authority"])));
                $reward_coins = (int)htmlspecialchars(strtolower(trim($_POST["reward_coins"])));
                $verificationcode = (int)htmlspecialchars(strtolower(trim($_POST["verificationcode"])));
                $quietlyreleased = htmlspecialchars(strtolower(trim($_POST["quietlyreleased"])));
                $groupmsgpush_system = htmlspecialchars(strtolower(trim($_POST["groupmsgpush_system"])));
                $groupmsgpush_mail = htmlspecialchars(strtolower(trim($_POST["groupmsgpush_mail"])));

                $recordUserLogin = $UserLogin->where("uid = $userloginid")->field('uid,logintime,nickname,coins,active')->find();
                if ($verificationcode == 999) {

                    /**
                     * show verificaation code
                     * 999 is a mark number , when show this num, I defined that the system shut verification off;
                     */
                    $verificationTimeRule = $ISysParameter->getParam("record_verifi_time");

                    /**
                     * show verification code ; time/second low;
                     */
                    $lastTwoRecord = $RecordSay->where("uid = $userloginid")->field("uid,sid,time,content")->order("time DESC")->limit(2)->select();
                    $timediffer = $lastTwoRecord[0]['time'] - $lastTwoRecord[1]['time'];
                    if ($timediffer < $verificationTimeRule['value']) {
                        /**
                         * create virification code
                         */
                        Vendor('Ihelpoo.Verifi');
                        $verifi = new Verifi();
                        $verifiString = $verifi->value_rand();
                        $_SESSION['verificationcode'] = $verifiString['formula'];
                        $_SESSION['verificationresult'] = $verifiString['result'];
                        $verificationTimeRuleAjaxReturn = array(
                            'last1Record' => $lastTwoRecord[0]['time'] . $lastTwoRecord[0]['content'] . $lastTwoRecord[0]['sid'],
                            'last2Record' => $lastTwoRecord[1]['time'] . $lastTwoRecord[1]['content'] . $lastTwoRecord[1]['sid'],
                            'timediffer' => $timediffer,
                            'verification Time' => $verificationTimeRule['value'],
                            'message' => '最后发布的两条信息时间间隔小于' . $verificationTimeRule['value'],
                        );
                        $this->ajaxReturn($verificationTimeRuleAjaxReturn, "请输入验证码", 'verifi');
                    }

                    /**
                     * show verification code ; nums/times low;
                     */
                    $verificationNumsRule = $ISysParameter->getParam("record_verifi_nums");
                    $userInsertAll = $RecordSay->where("uid = $userloginid AND time > $recordUserLogin[logintime]")->order("time DESC")->count();
                    if ($userInsertAll >= $verificationNumsRule['value']) {

                        /**
                         * create virification code
                         */
                        Vendor('Ihelpoo.Verifi');
                        $verifi = new Verifi();
                        $verifiString = $verifi->value_rand();
                        $_SESSION['verificationcode'] = $verifiString['formula'];
                        $_SESSION['verificationresult'] = $verifiString['result'];
                        $verificationNumsRuleAjaxReturn = array(
                            '今天发布总数' => $userInsertAll,
                            '上线' => $verificationNumsRule['value'],
                            'message' => '一天发布总数超过上线' . $verificationNumsRule['value'],
                        );
                        $this->ajaxReturn($verificationNumsRuleAjaxReturn, "请输入验证码", 'verifi');
                    }
                } else {
                    if ($verificationcode != $_SESSION['verificationresult']) {
                        $this->ajaxReturn(0, "验证码错误", 'error');
                    }
                }

                /**
                 * record publish limit nums 6 per 12 hours
                 */
                $time12hour = time() - 43200;
                $userInsertTime12hourAll = $RecordSay->where("uid = $userloginid AND time > $time12hour AND (say_type = 0 OR say_type = 1)")->order("time DESC")->count();
                if ($userInsertTime12hourAll >= $recordUserStatus['record_limit']) {
                    $this->ajaxReturn(0, "发布信息太多，休息休息再来吧:)", 'error');
                }

                /**
                 * handle coin nums
                 */
                if (($recordUserLogin['active'] - $reward_coins) < 0 && $help_is == 1) {
                    $this->ajaxReturn(0, "活跃不够了", 'error');
                }

                /**
                 * insert data into database
                 * image string handle
                 */
                $RecordOutimg = M("RecordOutimg");
                $tempImageUrls = $imageurls;
                $tempImageUrlsArray = explode("---", $tempImageUrls);
                $tempImageUrlsArray = array_unique($tempImageUrlsArray);
                $imageUrlString = '';
                foreach ($tempImageUrlsArray as $tempImageUrlString) {
                    if (preg_match("/undefined/", $tempImageUrlString)) {
                        $imageUrlString;
                    } else if (preg_match("/ihelpooupload/", $tempImageUrlString)) {
                        $imageUrlString .= $tempImageUrlString . ";";
                    } else {
                        $imageOutNewData = array(
                            'id' => '',
                            'uid' => $userloginid,
                            'rpath' => $tempImageUrlString,
                            'time' => time(),
                        );
                        $imageOutLastInsertId = $RecordOutimg->add($imageOutNewData);
                        $imageUrlString .= $imageOutLastInsertId . ";";
                    }
                }
                if (!empty($imageUrlString)) {
                    $imageHandled = substr($imageUrlString, 0, -1);
                } else {
                    $imageHandled = '';
                }

                /**
                 * get Browser Info
                 */
                Vendor('Ihelpoo.Browser');
                $browserObj = new Browser();
                $fromBrowser = $browserObj->getPlatform() . " " . $browserObj->getBrowser() . " " . $browserObj->getVersion();
                $sayType = $help_is;
                if ($quietlyreleased == 'on') {
                    $fromBrowser = '悄悄发布';
                    $sayType = '9';
                }

                if ($groupmsgpush_system == 'on' || $groupmsgpush_mail == 'on') {
                    $fromBrowser = '组织推送';
                }

                $dataRecordSay = array(
                    'sid' => NULL,
                    'uid' => $userloginid,
                    'say_type' => $sayType,
                    'content' => $content,
                    'image' => $imageHandled,
                    'url' => $videourl,
                    'authority' => $authority,
                    'time' => time(),
                    'last_comment_ti' => time(),
                    'from' => $fromBrowser,
                    'school_id' => $schoolpublishid
                );
                $sayLastInsertId = $RecordSay->add($dataRecordSay);

                /**
                 * send mail
                 */
                Vendor('Ihelpoo.Email');
                $emailObj = new Email();

                /**
                 * push group msg
                 */
                if ($groupmsgpush_system == 'on' || $groupmsgpush_mail == 'on') {
                    $userGroupPrioritied = $UserPriority->where("pid = $userloginid")->select();
                    if (!empty($userGroupPrioritied)) {
                        $MsgSystem = M("MsgSystem");
                        foreach ($userGroupPrioritied as $userPrio) {
                            if ($groupmsgpush_system == 'on') {
                                $msgSystemType = 'stream/i-para:groupmsgpush';
                                $contentMsgSystem = "组织有新的消息通知你!";
                                $pushMsgData = array(
                                    'id' => '',
                                    'uid' => $userPrio['uid'],
                                    'type' => $msgSystemType,
                                    'url_id' => $sayLastInsertId,
                                    'from_uid' => $userloginid,
                                    'content' => $contentMsgSystem,
                                    'time' => time(),
                                    'deliver' => 0,
                                );
                                $MsgSystem->add($pushMsgData);
                            }
                            if ($groupmsgpush_mail == 'on') {
                                $groupPushEmailUser = $UserLogin->find($userPrio['uid']);

                                /**
                                 * send emil
                                 */
                                if (!empty($groupPushEmailUser['email'])) {
                                    $emailObj->groupMessagePush($groupPushEmailUser['email'], $groupPushEmailUser['nickname'], $recordUserLogin['nickname'], $content);
                                }
                            }
                        }
                    }
                }

                /**
                 * calculate active
                 */
                if ($recordUserStatus['active_s_limit'] < 3) {
                    $recordUserLoginActive = $recordUserLogin['active'] + 3;
                    $recordUserLogin['active'] = $recordUserLogin['active'] == NULL ? 0 : $recordUserLogin['active'];

                    /**
                     * msg active
                     */
                    $MsgActive = M("MsgActive");
                    $msgActiveArray = array(
                        'id' => '',
                        'uid' => $userloginid,
                        'total' => $recordUserLogin['active'],
                        'change' => 3,
                        'way' => 'add',
                        'reason' => '写新记录或求助 (每天最多加3次)',
                        'time' => time(),
                        'deliver' => 0,
                    );
                    $MsgActive->add($msgActiveArray);
                } else {
                    $recordUserLoginActive = $recordUserLogin['active'];
                }

                /**
                 * update add active limit
                 */
                $updateUserStatusInfo = array(
                    'uid' => $userloginid,
                    'active_s_limit' => $recordUserStatus['active_s_limit'] + 1,
                );
                $UserStatus->save($updateUserStatusInfo);

                /**
                 * insert into i_record_help
                 */
                if ($help_is) {
                    $RecordHelp = M("RecordHelp");

                    /**
                     * if is help insert system_msg to fans
                     */
                    $userPrioritied = $UserPriority->where("pid = $userloginid")->select();
                    if (!empty($userPrioritied)) {
                        $MsgSystem = M("MsgSystem");
                        foreach ($userPrioritied as $userPrio) {
                            $msgSystemType = 'stream/ih-para:needhelp';
                            $contentMsgSystem = "有困难了，需要你的帮助";
                            $needHelpData = array(
                                'id' => '',
                                'uid' => $userPrio['uid'],
                                'type' => $msgSystemType,
                                'url_id' => $sayLastInsertId,
                                'from_uid' => $userloginid,
                                'content' => $contentMsgSystem,
                                'time' => time(),
                                'deliver' => 0,
                            );
                            $MsgSystem->add($needHelpData);

                            /**
                             * send emil
                             */
                            //              	$userPrioritiedMail = $UserLogin->find($userPrio['uid']);
                            //              	if (!empty($userPrioritiedMail['email'])) {
                            // $emailObj->helpstatusNeed($userPrioritiedMail['email'], $userPrioritiedMail['nickname'], $recordUserLogin['nickname'], $content);
                            //              	}
                        }
                    }

                    $helpData = array(
                        'hid' => '',
                        'sid' => $sayLastInsertId,
                        'reward_coins' => $reward_coins,
                        'status' => 1,
                    );
                    $helpLastInsertId = $RecordHelp->add($helpData);

                    /**
                     * 1.Add coin records into i_user_coins
                     * 2.Calculate coins use left
                     * 3.Encryption records
                     */
                    if (!empty($reward_coins)) {
                        $MsgActive = M("MsgActive");
                        $msgActiveArray = array(
                            'id' => '',
                            'uid' => $userloginid,
                            'total' => $recordUserLoginActive,
                            'change' => $reward_coins,
                            'way' => 'min',
                            'reason' => '求帮助使用',
                            'time' => time(),
                            'deliver' => 0,
                        );
                        $MsgActive->add($msgActiveArray);
                    }

                    /**
                     *
                     */
                    $updateUserLoginInfo = array(
                        'uid' => $userloginid,
                        'active' => $recordUserLoginActive - $reward_coins,
                    );
                } else {
                    $updateUserLoginInfo = array(
                        'uid' => $userloginid,
                        'active' => $recordUserLoginActive,
                    );
                }
                $UserLogin->save($updateUserLoginInfo);

                /**
                 * at user info
                 * insert into i_msg_at
                 */
                if (!empty($atusers)) {
                    $MsgAt = M("MsgAt");
                    $atUserTempArray = explode(",", $atusers);
                    foreach ($atUserTempArray as $atUser) {
                        $atUser = substr($atUser, 1);
                        $atUserRecord = $UserLogin->where("nickname = '$atUser'")->field('uid,nickname')->find();
                        $newAtMsgData = array(
                            'id' => '',
                            'touid' => $atUserRecord['uid'],
                            'fromuid' => $userloginid,
                            'sid' => $sayLastInsertId,
                            'hid' => $helpLastInsertId,
                            'time' => time(),
                            'deliver' => 0
                        );
                        $MsgAt->add($newAtMsgData);
                    }
                }
                $this->ajaxReturn(0, "发布成功", 'ok');
            }
        }

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
        } else if (preg_match("/index\/specialty/iUs", $_SERVER["REQUEST_URI"])) {
            $requestWay = "specialty";
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
        $sidString = NULL;
        $allIdString = NULL;
        $pidGroupArray = array();
        $isSetPriority = $UserPriority->where("uid = $userloginid")->select();
        if (!empty($isSetPriority)) {
            foreach ($isSetPriority as $priorityRecord) {
                if (!empty($priorityRecord['pid'])) {
                    $pidString .= $priorityRecord['pid'] . ",";
                    $allIdString .= $priorityRecord['pid'] . ",";

                    /**
                     * is set org priority
                     */
                    if ($priorityRecord['pid_type'] == 2) {
                        $pidGroupArray[] = $UserLogin->where("uid = $priorityRecord[pid]")->field('uid,nickname')->find();
                    }
                } else if (!empty($priorityRecord['sid'])) {
                    $sidString .= $priorityRecord['sid'] . ",";
                    $allIdString .= $priorityRecord['sid'] . ",";
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
        } else if ($requestWay == "time") {
            if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
            } else {
                $select->where("say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
            }
            $select->order('i_record_say.time DESC');
            $streamway = "time";
        } else if ($requestWay == "help") {
            if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type = '1' AND i_record_say.school_id = $recordSchoolInfo[id]");
            } else {
                $select->where("say_type = '1' AND i_record_say.school_id = $recordSchoolInfo[id]");
            }
            $select->order('i_record_say.last_comment_ti DESC');
            $streamway = "help";
        } else if ($requestWay == "group") {
            $groupUid = (int)trim($_GET["_URL_"][3]);
            $isSetGroupListPriority = $UserPriority->where("pid = $groupUid")->select();
            $pidGroupString = NULL;
            $pidGroupNums = 0;
            if (!empty($isSetGroupListPriority)) {
                foreach ($isSetGroupListPriority as $priorityRecord) {
                    if (!empty($priorityRecord['uid'])) {
                        $pidGroupString .= $priorityRecord['uid'] . ",";
                        $pidGroupNums++;
                    }
                }
            } else {
                redirect('/stream', 3, '组织成员为空 3秒后页面跳转...');
            }
            $pidGroupString = substr($pidGroupString, 0, -1);
            $select->where("i_record_say.uid IN ($pidGroupString) AND say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
            $select->order('i_record_say.last_comment_ti DESC');
            $streamway = "group";
            $this->assign('groupUserNums', $pidGroupNums);
            $groupUserRecord = $UserLogin->find($groupUid);
            $this->assign('groupUserRecord', $groupUserRecord);
        } else if ($requestWay == "specialty") {
            $specialtyId = (int)trim($_GET["_URL_"][3]);
            //$isSetGroupListPriority = $UserPriority->where("pid = $groupUid")->select();
            $UserInfo = M("UserInfo");
            $allSameSpecialtyUsers = $UserInfo->where("specialty_op = $specialtyId")->select();
            $allUserString = NULL;
            $allUserNums = 0;
            if (!empty($allSameSpecialtyUsers)) {
                foreach ($allSameSpecialtyUsers as $specialtyUsers) {
                    if (!empty($specialtyUsers['uid'])) {
                        $allUserString .= $specialtyUsers['uid'] . ",";
                        $allUserNums++;
                    }
                }
            } else {
                redirect('/stream', 3, '还没有就读该专业的同学 3秒后页面跳转...');
            }
            $allUserString = substr($allUserString, 0, -1);
            $select->where("i_record_say.uid IN ($allUserString) AND say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
            $select->order('i_record_say.last_comment_ti DESC');
            $streamway = "specialty";
            $this->assign('groupUserNums', $allUserNums);
            $OpSpecialty = M("OpSpecialty");
            $recordOpSpecialty = $OpSpecialty->where("id = $specialtyId")->find();
            $this->assign('specialtyName', $recordOpSpecialty['name']);
            $this->assign('specialtyId', $specialtyId);
            $this->assign('academyId', $recordOpSpecialty['academy']);
        } else {
            if (!empty($sidString)) {
                $sidString = substr($sidString, 0, -1);
                $select->where("i_record_say.uid NOT IN ($sidString) AND say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
            } else {
                $select->where("say_type != '9' AND i_record_say.school_id = $recordSchoolInfo[id]");
            }
            $select->order('i_record_say.last_comment_ti DESC');
            $streamway = "default";
        }
        $recordSay = $select->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->join('i_user_info ON i_record_say.uid = i_user_info.uid')
            ->join('i_op_specialty ON i_user_info.specialty_op = i_op_specialty.id')
            ->join('i_school_info ON i_user_login.school = i_school_info.id')
            ->join("i_user_remark ON i_record_say.uid = i_user_remark.ruid AND $userloginid = i_user_remark.uid")
            ->field('sid,i_user_login.uid,say_type,content,image,url,i_user_login.school,comment_co,diffusion_co,hit_co,plus_co,i_record_say.time,from,last_comment_ti,school_id,nickname,sex,birthday,enteryear,type,online,active,icon_url,i_user_info.specialty_op,i_op_specialty.name,i_op_specialty.academy,i_school_info.id,i_school_info.school as schoolname,i_school_info.domain,i_school_info.domain_main,i_user_remark.remark')
            ->limit($offset, $count)->select();
        $userRecordSayUidBefore = NULL;
        foreach ($recordSay as $record) {
            $schooldomain = $record['domain_main'] == NULL ? $record['domain'] : $record['domain_main'];
            if ($userRecordSayUidBefore == $record['uid']) {
                $recordSayArray[] = array(
                    'sid' => $record['sid'],
                    'uid' => $record['uid'],
                    'say_type' => $record['say_type'],
                    'content' => stripslashes($record['content']),
                    'image' => $record['image'],
                    'url' => $record['url'],
                    'school' => $record['school'],
                    'school_record' => $record['school_id'],
                    'comment_co' => $record['comment_co'],
                    'diffusion_co' => $record['diffusion_co'],
                    'hit_co' => $record['hit_co'],
                    'plus_co'=>$record['plus_co'],
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
                    'schoolname' => $record['schoolname'],
                    'domain' => $schooldomain,
                    'remark' => $record['remark'],
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
                    'school' => $record['school'],
                    'school_record' => $record['school_id'],
                    'comment_co' => $record['comment_co'],
                    'diffusion_co' => $record['diffusion_co'],
                    'hit_co' => $record['hit_co'],
                    'plus_co'=>$record['plus_co'],
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
                    'schoolname' => $record['schoolname'],
                    'domain' => $schooldomain,
                    'remark' => $record['remark'],
                    'repatenums' => 0
                );
            }
            $userRecordSayUidBefore = $record['uid'];
        }

        $this->assign('streamway', $streamway);
        $this->assign('recordSay', $recordSayArray);

        /**
         * show new active nums
         */
        $MsgActive = M("MsgActive");
        $msgActiveNewNums = $MsgActive->where("uid = $userloginid AND deliver = 0")->count();
        $this->assign('msgActiveNewNums', $msgActiveNewNums);

        /**
         * show user info
         */
        $UserInfo = M("UserInfo");
        $recordUserInfo = $UserInfo->find($userloginid);
        $this->assign('recordUserInfo', $recordUserInfo);

        /**
         * show online user nums
         */
        $IWebStatus = D("IWebStatus");
        $recordOnlineUserNums = $IWebStatus->paraExists('online_user_nums');
        $this->assign('onlineUserNums', $recordOnlineUserNums['valuechar']);

        /**
         * show user honor nums
         */
        $UserHonor = M("UserHonor");
        $totalUserHonorNums = $UserHonor->where("uid = $userloginid")->count();
        $this->assign('totalUserHonorNums', $totalUserHonorNums);

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
            $this->assign('isAlreadyBind', $recordUserLoginWb);
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

        /**
         * index_spread_info
         */
        $SchoolSystem = M("SchoolSystem");
        $recordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->find();
        $indexSpreadInfoVaule = $recordSchoolSystem['index_spread_info'];
        $this->assign('indexSpreadInfoVaule', $indexSpreadInfoVaule);
        
        /**
         * school advertisement
         */
        $SchoolAd = M("SchoolAd");
        $streamRightSchoolAd = $SchoolAd->where("type = '2' AND sid = $recordSchoolInfo[id]")->order("time DESC")->limit(3)->select();
        $this->assign('streamRightSchoolAd', $streamRightSchoolAd);
    	$streamHeaderTopSchoolAd = $SchoolAd->where("type = '1' AND sid = $recordSchoolInfo[id]")->order("time DESC")->limit(6)->select();
    	$this->assign('streamHeaderTopSchoolAd', $streamHeaderTopSchoolAd);
        
        $this->display();
    }

    public function fav()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '我的收藏');

        $page = i_page_get_num();
        $RecordFavourites = M("RecordFavourites");
        $count = 20;
        $this->assign('pageCount', $count);
        $offset = $page * $count;
        $recordFavouriteArray = $RecordFavourites->where("i_record_favourites.uid = $userloginid")
            ->join('i_record_say ON i_record_favourites.sid = i_record_say.sid')
            ->join('i_user_login ON i_record_say.uid = i_user_login.uid')
            ->field('id,i_user_login.uid,i_record_favourites.sid,i_record_favourites.time,nickname,sex,birthday,enteryear,type,online,active,icon_url,i_record_say.content')
            ->order("time DESC")->limit($offset, $count)->select();
        $this->assign('recordFavourites', $recordFavouriteArray);

        /**
         * page link
         */
        $totalFavouritesNums = $RecordFavourites->where("uid = $userloginid")->count();
        $this->assign('totalFavouritesNums', $totalFavouritesNums);
        $totalPages = ceil($totalFavouritesNums / $count);
        $this->assign('totalPages', $totalPages);
        if ($this->isPost()) {
            $recordSayId = (int)htmlspecialchars(trim($_POST["sid"]));
            if ($recordSayId) {
                $favData = array(
                    'id' => NULL,
                    'uid' => $userloginid,
                    'sid' => $recordSayId,
                    'time' => time(),
                );
                $isFavourite = $RecordFavourites->where("sid = $recordSayId AND uid = $userloginid")->find();
                if (!empty($isFavourite['id'])) {
                    $this->ajaxReturn(0, "您已经收藏过这条信息了", 'info');
                } else {
                    $affected = $RecordFavourites->add($favData);
                    if ($affected) {
                        $this->ajaxReturn(1, "收藏成功", 'yes');
                    } else {
                        $this->ajaxReturn(0, "收藏失败", 'wrong');
                    }
                }
            }
        }

        /**
         * need change to ajax
         */
        if (!empty($_GET['del'])) {
            $sid = (int)$_GET['del'];
            $deleteaffected = $RecordFavourites->where("sid = $sid AND uid = $userloginid")->delete();
            if (!$deleteaffected) {
                redirect('/stream/fav', 2, '删除出错啦...');
            }
            redirect('/stream/fav', 1, '删除成功...');
        }
        $this->display();
    }
    
    public function ajaxcomment()
    {
    	$userloginid = session('userloginid');
    	Vendor('Ihelpoo.Emotion');
    	$emotion = new Emotion();
    	$commentSidString = $_POST['commentSid'];
    	$commentSidArray = explode("-", $commentSidString);
    	$commentSid = $commentSidArray['1'];
    	$UserLogin = M("UserLogin");
    	if (!empty($commentSid)) {
    		$RecordComment = M("RecordComment");
    		$sayCommentNums = $RecordComment->where("sid = $commentSid")->count();
    		$sayComment = $RecordComment->where("i_record_comment.sid = $commentSid")->join('i_user_login ON i_record_comment.uid = i_user_login.uid')->join('i_record_say ON i_record_comment.sid = i_record_say.sid')
	        ->field('cid,i_record_comment.uid,i_record_say.uid as record_owneruid,i_record_comment.sid,toid,i_record_comment.content,i_record_comment.image,i_record_comment.time,nickname,sex,birthday,enteryear,type,online,active,icon_url')
	        ->limit(10)->order('cid DESC')->select();
	        
	        /**
	         * return html
	         */
	        echo '<div class="comment_view_div_box_reply" sid='.$commentSid.'>';
	        echo '<textarea class="comment_view_div_box_reply_textarea textarea_style"></textarea>';
	        echo '<span class="post_icon comment_textareaicon_reply" title="表情"></span>';
	        echo '<span class="comment_reply_verification_stream">';
		    echo '<img class="comment_reply_verification_stream_code_img" src="" />';
		    echo '<input class="comment_reply_verification_streamcode" type="text" value="999" />';
		    echo '</span>';
	        echo '<a class="comment_reply_submit btn">评论</a>';
	        echo '</div>';
	        echo '<ul class="comment_view_div_box_ul">';
	        foreach ($sayComment as $comment) {
	        	echo '<li>';
		    	echo '<a href="/wo/'.$comment['uid'].'" class="getuserinfo c_v_d_b_ul_li_icon" userid="'.$comment['uid'].'"><img src="'.i_icon_check($comment['uid'], $comment['icon_url'], 's').'" height="30" class="radius3" /></a>';
		    	echo '<p class="c_v_d_b_ul_li_content">';
		    	echo '<a href="/wo/'.$comment['uid'].'" class="getuserinfo" userid="'.$comment['uid'].'">'.$comment['nickname'].':</a> ';
		    	echo '<span class="gray fb">';
		      	if (!empty($comment['toid'])) {
		  			$commentReplyUser = $UserLogin->where("$comment[toid] = uid")->field('uid,nickname')->find();
         		 	echo "[回复:".$commentReplyUser['nickname']."] ";
				} 
		    	echo '</span>';
		    	echo $emotion->transEmotion(stripslashes($comment['content']));
		    	echo ' <span class="gray">('.i_time($comment['time']).')</span>';
		    	echo '</p>';
				if (!empty($comment['image'])) {
					echo '<p class="c_v_d_b_ul_li_content_image">';
					echo '<a href="'.$comment['image'].'" title="点击查看原图" target="_blank">';
					echo '<img src="'.i_image_thumbnail($comment['image']).'" class="" />';
					echo '</a></p>';
				}
		    	echo '<span class="c_v_d_b_ul_li_content_reply">';
		    	if ($comment['uid'] == $userloginid || $comment['record_owneruid'] == $userloginid) {
				    echo '<a class="c_v_d_b_ul_li_content_del gray" value="'.$comment['cid'].'">删除</a>';
			    }
		    	if (!empty($comment['uid']) && $comment['uid'] != $userloginid) {
			    	echo ' <i class="icon_plus"></i> <a class="c_v_d_b_ul_li_content_reply_btn">回复</a>';
			    } 
		    	echo '</span>';
	        	if (!empty($comment['uid']) && $comment['uid'] != $userloginid) {
	        		echo '<div class="comment_view_div_box_replyinner" sid='.$comment['sid'].' cid='.$comment['cid'].' toid='.$comment['uid'].'>';
	        		echo '<textarea class="comment_view_div_box_replyinner_textarea textarea_style"></textarea>';
	        		echo '<span class="post_icon comment_textareaicon_replyinner" title="表情"></span>';
	        		echo '<span class="comment_reply_verification_stream">';
	        		echo '<img class="comment_reply_verification_stream_code_img" src="" />';
	        		echo '<input class="comment_reply_verification_streamcode" type="text" value="999" />';
	        		echo '</span>';
	        		echo '<a class="comment_reply_submit btn">回复</a>';
	        		echo '</div>';
			    }
		    	echo '</li>';
	        }
	        echo '</ul>';
	        if ($sayCommentNums > 10) {
	        	echo "<div>后面还有".($sayCommentNums - 10)."条评论，<a href='/item/say/".$commentSid."'>点击查看&gt;&gt;</a></div>";
	        } else {
	        	echo "<div><a href='/item/say/".$commentSid."'>详细内容页&gt;&gt;</a></div>";
	        }
    	}
    }

    public function plusToggle(){
    	if(empty($_POST['plusSid'])){
    		exit();
    	}
        $plusSidArr = explode("-", $_POST['plusSid']);
        $RecordPlus = M('RecordPlus');
        $userloginid = session('userloginid');
        $sid = $plusSidArr[1];
        $plus = $RecordPlus->where("uid = $userloginid AND sid = $sid")->find();
        $plusId = $plus['id'];
        $MsgNotice = M('MsgNotice');
        $noticeType = 'stream/' . $plusSidArr['0'] . '-para:plus';
        $msgNotice = $MsgNotice->where("notice_type = '".$noticeType."' AND source_id = $userloginid AND detail_id = $sid AND format_id = 'plus'")->find();

        $redis = new Redis();
        $redis->pconnect(C('REDIS_HOST'), C('REDIS_PORT'));

        if (!empty($plus)) {
            $RecordPlus->where("id=$plusId")->delete();
            $recordSay = $this->bouncePlusCountOfRecord($sid ,-1);
            $this->deleteNoticeMessage($msgNotice['notice_id']);
            $this->bounceNoticeMessageCount($redis, $recordSay['uid'], -1);
            $this->deliverBack($recordSay['uid'], $msgNotice['notice_id']);
            $this->ajaxReturn($recordSay['plus_co'], "return data", 'yes');
        }else{
            $this->addPlusRecord($sid);
            $recordSay = $this->bouncePlusCountOfRecord($sid ,1);
            $noticeIdForOwner = $this->saveNoticeMessageForOwner($plusSidArr, $userloginid, $sid, 'plus');
            $this->bounceNoticeMessageCount($redis, $recordSay['uid'], 1);
            $this->deliverTo($recordSay['uid'], $noticeIdForOwner);
            $this->ajaxReturn($recordSay['plus_co'], "return data", 'yes');
        }
        exit();
    }
    
    public function plusView(){
    	if(empty($_POST['sidString'])){
    		exit();
    	}
    	$sidStringArr = explode("-", $_POST['sidString']);
        $userloginid = session('userloginid');
        $sid = $sidStringArr['1'];
        $RecordPlus = M('RecordPlus');
        $resultsRecordPlus = $RecordPlus->where("sid = $sid")
        ->join("i_user_login ON i_record_plus.uid = i_user_login.uid")
        ->field("id,i_user_login.uid,i_record_plus.sid,i_record_plus.create_time,nickname,icon_url")
        ->limit(5)
        ->order("create_time DESC")
        ->select();
        if (!empty($resultsRecordPlus)) {
        	echo '<p class="stream_plus_users_p gray">他们赞过了这条信息</p>';
	        echo '<ul class="stream_plus_users_ul">';
	        foreach ($resultsRecordPlus as $recordPlus) {
	        	echo '<li>';
			    echo '<a href="/wo/'.$recordPlus['uid'].'" title="'.$recordPlus['nickname'].'"><img src="'.i_icon_check($recordPlus['uid'], $recordPlus['icon_url'], 's').'" height="25" class="radius3" /></a>';
			    echo '</li>';
	        }
	        if ($sidStringArr['0'] == 'i') {
	        	echo '<li><a href="/item/say/'.$sid.'" class="f12">更多</a></li>';
	        } else {
	        	echo '<li><a href="/item/help/'.$sid.'" class="f12">更多</a></li>';
	        }
	        echo '</ul>';
        } else {
        	echo '<p class="stream_plus_users_p">还没有人赞过这条信息，快来赞赞吧！</p>';
        }
    }

    public function addPlusRecord($sid){
        $RecordPlus = M('RecordPlus');
        $userloginid = session('userloginid');

        $plus = array(
            'id' => '',
            'sid' => $sid,
            'uid' => $userloginid,
            'view'=> '',
            'deliver'=>0,
            'create_time' => time(),
        );
        return $RecordPlus->add($plus);
    }

    public function bouncePlusCountOfRecord($sid, $offset)
    {
        $RecordSay = M("RecordSay");
        $resultRecordSay = $RecordSay->find($sid);
        $recordSay = array(
            'sid' => $sid,
            'uid' => $resultRecordSay['uid'],
            'plus_co' => $resultRecordSay['plus_co'] + $offset,
        );
        $RecordSay->save($recordSay);
        return $recordSay;
    }


    public function ajax()
    {
        if (empty($_POST['diffusionSid'])) {
            exit();
        }
        $userloginid = session('userloginid');
        $RecordDiffusion = M("RecordDiffusion");
        $diffusionSidArray = explode("-", $_POST['diffusionSid']);
        $this->exitWhenDuplicated($RecordDiffusion, $userloginid, $diffusionSidArray);
        //TODO for test temporarily
//        $this->exitWhenSpamming($RecordDiffusion, $userloginid);
        $diffusionId = $this->saveRecordDiffusion($userloginid, $diffusionSidArray, $RecordDiffusion);
        $noticeIdForFollowers = $this->saveNoticeMessageForFollowers($diffusionSidArray, $userloginid, $diffusionId);
        $resultRecordSay = $this->increaseDiffusionsCountOfRecord($diffusionSidArray, $userloginid);
        $noticeIdForOwner = $this->saveNoticeMessageForOwner($diffusionSidArray, $userloginid, $diffusionId, 'diffusiontoowner');
        $this->diffuse($userloginid, $noticeIdForOwner, $noticeIdForFollowers, $resultRecordSay['uid']);
        exit();
    }

    public function exitWhenDuplicated($RecordDiffusion, $userloginid, $diffusionSidArray)
    {
        $isDiffusion = $RecordDiffusion->where("uid = $userloginid AND $diffusionSidArray[1] = sid")->find();
        if (!empty($isDiffusion['id'])) {
            echo "你已经扩散了这条信息";
            exit();
        }
    }

    public function exitIfDiffuseMoreThanThresholdWithinHalfDay($RecordDiffusion, $userloginid, $threshold)
    {
        $time12hour = time() - 43200;
        $userDiffusion12hourAll = $RecordDiffusion->where("uid = $userloginid AND time > $time12hour")->order("time DESC")->count();
        if ($userDiffusion12hourAll >= $threshold) {
            echo "你扩散了太多消息，休息休息再来吧 :)";
            echo "<br />每12小时最多扩散3条";
            exit();
        }
    }


    public function exitWhenSpamming($RecordDiffusion, $userloginid)
    {
        $this->exitIfDiffuseMoreThanThresholdWithinHalfDay($RecordDiffusion, $userloginid, 3);
    }


    public function saveRecordDiffusion($userloginid, $diffusionSidArray, $RecordDiffusion)
    {
        $dataDiffusion = array(
            'id' => '',
            'uid' => $userloginid,
            'sid' => $diffusionSidArray[1],
            'view'=> trim($_POST['diffusionView']),
            'time' => time(),
        );
        $diffusionId = $RecordDiffusion->add($dataDiffusion);
        return $diffusionId;
    }



    public function saveNoticeMessageForOwner($typeIdArr, $userloginid, $detailId, $noticeType)
    {
        return $this->saveNoticeMessage($typeIdArr, $userloginid, $noticeType,$detailId);
    }

    public function saveNoticeMessageForFollowers($diffusionSidArray, $userloginid,$detailId)
    {
        return $this->saveNoticeMessage($diffusionSidArray, $userloginid, 'diffusion', $detailId);
    }

    public function saveNoticeMessage($typeIdArr, $userloginid, $noticeType, $detailId)
    {
        Vendor('Ihelpoo.Idworker');
        $idworker = new Idworker();
        $noticeId = $idworker->next();
        $hs = new HandlerSocket(C('MYSQL_MASTER'), C('HS_PORT_WR'));
        if (!($hs->openIndex(3, C('OO_DBNAME'), C('H_I_MSG_NOTICE'), '', 'notice_id,notice_type,source_id,detail_id,format_id,create_time'))) {
            echo 'ERROR1:' . $hs->getError(), PHP_EOL;
            die();
        }

        if ($hs->executeInsert(3, array($noticeId, 'stream/' . $typeIdArr['0'] . '-para:' . $noticeType, $userloginid, $detailId, $noticeType, time())) === false) {
            echo 'ERR2:' . $hs->getError(), PHP_EOL;
        }
        unset($hs);
        return $noticeId;
    }

    public function deleteNoticeMessage($noticeId){
        //DELETE
        $hs = new HandlerSocket(C('MYSQL_MASTER'), C('HS_PORT_WR'));
        if (!($hs->openIndex(4, C('OO_DBNAME'), C('H_I_MSG_NOTICE'), '', '')))
        {
            echo 'ERR_WHEN_DEL_OPEN: '.$hs->getError(), PHP_EOL;
            die();
        }

        if ($hs->executeDelete(4, '=', array($noticeId)) === false)
        {
            echo 'ERR_WHEN_DEL: '.$hs->getError(), PHP_EOL;
            die();
        }
        unset($hs);
    }

    public function increaseDiffusionsCountOfRecord($diffusionSidArray, $userloginid)
    {
        $RecordSay = M("RecordSay");
        $resultRecordSay = $RecordSay->find($diffusionSidArray[1]);

        $UserLogin = M("UserLogin");
        $recordUserLogin = $UserLogin->find($userloginid);
        $recordSaySet = array(
            'sid' => $diffusionSidArray['1'],
            'diffusion_co' => $resultRecordSay['diffusion_co'] + 1,
        );
        $RecordSay->save($this->affectRecordTimeWhenLevelMoreThan(i_degree($recordUserLogin['active']), $recordSaySet, 2));
        return $resultRecordSay;
    }


    public function affectRecordTimeWhenLevelMoreThan($userLevel, $recordSaySet, $thresholdLevel)
    {
        if ($userLevel >= $thresholdLevel)
            $recordSaySet['last_comment_ti'] = time();
        return $recordSaySet;
    }

    public function bounceNoticeMessageCount($redis, $uid, $offset)
    {
        $redis->hIncrBy(C('R_NOTICE') . C('R_SYSTEM') . substr($uid, 0, strlen($uid) - 3), substr($uid, -3), $offset);
    }

    public function diffuse($userloginid, $noticeIdForOwner, $noticeIdForFollowers, $recordOwnerId)
    {
        $UserPriority = M("UserPriority");
        $userPriorityObj = $UserPriority->where("pid = $userloginid")->join("i_user_login ON i_user_priority.uid = i_user_login.uid")->select();
        $userPriorityNums = sizeof($userPriorityObj);
        $this->outputMessage($userPriorityNums, $userPriorityObj);
        $this->saveDiffusionRelations($noticeIdForOwner, $noticeIdForFollowers, $userPriorityObj, $recordOwnerId);
    }

    public function outputMessage($userPriorityNums, $userPriorityObj)
    {
        $data = array(
            10000,11111
        );
        $this->ajaxReturn($data, '扩散了', 'yes');
        return;
    	$userloginid = session('userloginid');
        echo "已扩散给 <a href='" . __ROOT__ . "/wo/quaned/".$userloginid."'>您圈子</a> 中的<span class='f14 fb orange'>" . $userPriorityNums . "</span> 人...<br /><br />";
        if (empty($userPriorityNums)) {
            exit();
        }
        foreach ($userPriorityObj as $idx => $userPriority) {
            if ($idx >= 10) break;
            echo "<a href='/wo/".$userPriority['uid']."' class='f12'>".$userPriority['nickname']."</a> <br />";
        }
        echo "...";
        return $userPriority;
    }

    public function saveDiffusionRelations($noticeIdForOwner, $noticeIdForFollowers, $userPriorityObj, $recordOwnerId)
    {
        $redis = new Redis();
        $redis->pconnect(C('REDIS_HOST'), C('REDIS_PORT'));
        $this->bounceNoticeMessageCount($redis, $recordOwnerId, 1);
        $this->deliverTo($recordOwnerId, $noticeIdForOwner);
        foreach ($userPriorityObj as $userPriority) {
            $this->bounceNoticeMessageCount($redis, $userPriority['uid'], 1);
            $this->deliverTo($userPriority['uid'], $noticeIdForFollowers);
        }
    }


    public function deliverTo($who, $noticeId)
    {
        $redis = new Redis();
        $redis->pconnect(C('REDIS_HOST'), C('REDIS_PORT'));
        $redis->hSet(C('R_ACCOUNT')  . C('R_MESSAGE'). $who, $noticeId, 0);
    }


    /*
     * remove one's single notice
     */
    public function deliverBack($who, $noticeId)
    {
        $redis = new Redis();
        $redis->pconnect(C('REDIS_HOST'), C('REDIS_PORT'));
        $redis->hDel(C('R_ACCOUNT')  . C('R_MESSAGE'). $who, $noticeId);
    }


}

?>