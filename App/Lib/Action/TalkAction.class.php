<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class TalkAction extends Action
{

    protected function _initialize()
    {
        $userloginid = session('userloginid');
        if (!empty($userloginid)) {
            i_db_update_activetime($userloginid);
            $UserLogin = M("UserLogin");
            $userloginedrecord = $UserLogin->find($userloginid);
            $this->assign('userloginedrecord', $userloginedrecord);
            $recordSchoolInfo = i_school_domain();
            $this->assign('schoolname',$recordSchoolInfo['school']);


            /**
             *
             * update user input status
             * @param int $uid
             * @param int $touid
             * @param int $status 1 for input now; 0 for default
             */
            function updateInputStatus($uid, $touid, $status)
            {
                $TalkInputstatus = M("TalkInputstatus");
                $userInputStatus = $TalkInputstatus->where("uid = $uid AND touid = $touid")->find();
                if ($userInputStatus) {
                    $data = array(
                        'id' => $userInputStatus['id'],
                        'status' => $status,
                        'time' => time(),
                    );
                    $isOk = $TalkInputstatus->save($data);
                } else {
                    $data = array(
                        'id' => '',
                        'uid' => $uid,
                        'touid' => $touid,
                        'status' => $status,
                    );
                    $isOk = $TalkInputstatus->add($data);
                }
                return $isOk;
            }

        } else {
            redirect('/user/notlogin', 0, '你还没有登录呢...');
        }
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '聊天 悄悄话');

        /**
         * talk list
         */
        $TalkList = M("TalkList");
        $allTalkList = $TalkList->where("uid = $userloginid")->find();
        if (!empty($allTalkList)) {
            $talkList = $TalkList->where("i_talk_list.uid = $userloginid")
                ->join('i_user_login ON i_talk_list.listuid = i_user_login.uid')
                ->join('i_user_info ON i_talk_list.listuid = i_user_info.uid')
                ->order('i_user_login.online DESC')
                ->limit(100)
                ->select();
            $this->assign('talkLists', $talkList);
        }
        //$this->view->userid = $user->uid;

        /**
         * random talk
         */
        if (!empty($_POST['random_talk'])) {
            $UserLogin = M("UserLogin");
            $OpAcademy = M("OpAcademy");
            $UserInfo = M("UserInfo");
            $SchoolInfo = M("SchoolInfo");
            $searchRandUserSql = "SELECT * FROM i_user_login ORDER BY RAND() LIMIT 1";
            $searchRandUser = $UserLogin->query($searchRandUserSql);
            $randTalkUid = $searchRandUser[0]['uid'];
            $randTalkSchoolid = $searchRandUser[0]['school'];
            $randTalkUserlogin = $searchRandUser[0];
            $randTalkUserInfo = $UserInfo->where("uid = $randTalkUid")->find();
            $randTalkUserOpAcademy = $OpAcademy->where("id = $randTalkUserInfo[academy_op]")->find();
            $randTalkUserSchoolInfo = $SchoolInfo->where("id = $randTalkSchoolid")->find();
            if (!empty($randTalkUserlogin['enteryear'])) {
                $randTalkUserGrade = i_grade($randTalkUserlogin['enteryear']);
            }
            if (!empty($randTalkUserlogin['birthday'])) {
                $randTalkUserConstellation = i_constellation($randTalkUserlogin['birthday']);
            }
            $imageUrl = i_icon_check($randTalkUid, $randTalkUserlogin['icon_url'], 's');
            $jsonEncode = array(
                'uid' => $randTalkUid,
                'image' => $imageUrl,
                'nickname' => $randTalkUserlogin['nickname'],
                'school' => $randTalkUserSchoolInfo['school'],
                'domain' => $randTalkUserSchoolInfo['domain'],
                'academy' => $randTalkUserOpAcademy['name'],
                'sex' => $randTalkUserlogin['sex'],
                'constellation' => $randTalkUserConstellation,
                'grade' => $randTalkUserGrade,
            );
            $this->ajaxReturn($jsonEncode, '随机用户', 'ok');
        }

        /**
         *
         */
        $TalkContent = M("TalkContent");
        $selectUserSexArray = $TalkContent->where("i_talk_content.uid = $userloginid OR i_talk_content.touid = $userloginid")
        ->join('i_user_login ON i_talk_content.touid = i_user_login.uid')->select();
        $totalTalkNums = 0;
        $totalNewTalkNums = 0;
        foreach ($selectUserSexArray as $selectUserSex) {
            if ($selectUserSex['uid'] != $userloginid) {
                if ($selectUserSex['sex'] == 1) {
                    $talkBoyArray[] = $selectUserSex['uid'];
                } else {
                    $talkGirlArray[] = $selectUserSex['uid'];
                }
            }
            if ($selectUserSex['del'] != $userloginid) {
                $totalTalkNums++;
            }
            if ($selectUserSex['deliver'] == 0 && $selectUserSex['uid'] == $userloginid) {
                $totalNewTalkNums++;
            }
        }
        $talkBoyArray = array_unique($talkBoyArray);
        $talkBoyNums = count($talkBoyArray);
        $talkGirlArray = array_unique($talkGirlArray);
        $talkGirlNums = count($talkGirlArray);
        $this->assign('talkBoyNums', $talkBoyNums);
        $this->assign('talkGirlNums', $talkGirlNums);
        $this->assign('totalTalkNums', $totalTalkNums);
        $this->assign('totalNewTalkNums', $totalNewTalkNums);

        /**
         * talk history
         */
        $talkHistoryObject = $TalkContent->where("uid = $userloginid OR touid = $userloginid")->order("time DESC")->select();
        if (!empty($talkHistoryObject)) {
            $i = 1;
            $talkHistoryObjectTempArray = array();
            foreach ($talkHistoryObject as $talkHistory) {
                if (!in_array($talkHistory['uid'] . $talkHistory['touid'], $talkHistoryObjectTempArray) && $i < 26) {
                    $talkHistoryObjectTempArray[] = $talkHistory['uid'] . $talkHistory['touid'];
                    $talkHistoryObjectArray[] = array(
                        'uid' => $talkHistory['uid'],
                        'touid' => $talkHistory['touid'],
                        'time' => $talkHistory['time'],
                    );
                    $i++;
                }
            }
            $this->assign('talkHistoryObjectArray', $talkHistoryObjectArray);
        }
        $this->display();
    }

    public function to()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '聊天 悄悄话');
        $toUserId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));


//        $redis = new Redis();
//        $redis->pconnect(C('REDIS_HOST'), C('REDIS_PORT'));
//        $redis->set($userloginid, $toUserId);


        //FIXME table might be write-locked
        $TalkContent = M("TalkContent");
        $leaveWords = $TalkContent->where("i_talk_content.uid = $toUserId AND touid = $userloginid AND deliver = '0' ")
            ->join('i_user_login ON i_talk_content.uid = i_user_login.uid')
            ->order("time ASC")->select();
        $this->assign('leaveWords', $leaveWords);
        foreach ($leaveWords as $leaveWord) {
            $updateData = array(
                'id' => $leaveWord['id'],
                'deliver' => '1',
            );
            $TalkContent->save($updateData);
        }

        /**
         * talk list
         */
        $TalkList = M("TalkList");
        $allTalkList = $TalkList->where("uid = $userloginid")->find();
        if (!empty($allTalkList)) {
            $talkList = $TalkList->where("i_talk_list.uid = $userloginid")
                ->join('i_user_login ON i_talk_list.listuid = i_user_login.uid')
                ->join('i_user_info ON i_talk_list.listuid = i_user_info.uid')
                ->order('i_user_login.online DESC')
                ->limit(100)
                ->select();
            $this->assign('talkLists', $talkList);
        }


        /**
         * view
         */
        $IUserLogin = D("IUserLogin");
        $toUserRecord = $IUserLogin->userExists($toUserId);
        if (!$toUserRecord['uid']) {
            redirect('/talk', 3, '用户不存在或者设置为不和陌生人聊天 :(...');
        }
        if ($toUserRecord['uid'] == $userloginid) {
            redirect('/talk', 3, '不能和自己聊天噢 :(...');
        }
        $this->assign('toUserRecord', $toUserRecord);

        $UserInfo = M("UserInfo");
        $toUserInfo = $UserInfo->where("uid = $toUserId")->find();
        if (!empty($toUserInfo['introduction'])) {
            $this->assign('toUserIntroduction', $toUserInfo['introduction']);
        }

        $OpAcademy = M("OpAcademy");
        $OpSpecialty = M("OpSpecialty");
        if (!empty($toUserInfo['academy_op'])) {
            $toUserAcademy = $OpAcademy->where("id = $toUserInfo[academy_op]")->find();
            $this->assign('toUserAcademy', $toUserAcademy);
        }
        if (!empty($toUserInfo['specialty_op'])) {
            $toUserSpecialty = $OpSpecialty->where("id = $toUserInfo[specialty_op]")->find();
            $this->assign('toUserSpecialty', $toUserSpecialty);
        }
        $recordSchoolInfo = i_school_domain();
        $this->assign('recordSchoolInfo', $recordSchoolInfo);
        if ($toUserRecord['school'] != $recordSchoolInfo['id']) {
            $SchoolInfo = M("SchoolInfo");
            $userLoginSchoolInfo = $SchoolInfo->find($toUserRecord['school']);
            $this->assign('userLoginSchoolInfo', $userLoginSchoolInfo);
        }
        $this->display();
    }

    public function toajax()
    {
        $postUid = $_POST['uid'];
        $postTouid = $_POST['touid'];
        $postContent = $_POST['content'];
        $postImage = $_POST['image'];
        if ((!empty($postUid) && !empty($postTouid) && !empty($postContent)) || (!empty($postUid) && !empty($postTouid) && !empty($postImage))) {
            $TalkContent = M("TalkContent");

            /**
             * update input status
             */
            if ($postContent == "status_input") {
                updateInputStatus($postUid, $postTouid, '1');
                $returnData = array();
                $returnData['status_input'] == 'status1';
                $this->ajaxReturn($returnData, 'message ajax, update input status 1', 'ok');
            }

            $dataTalkContent = array(
                'id' => '',
                'uid' => $postUid,
                'touid' => $postTouid,
                'content' => $postContent,
                'image' => $postImage,
                'deliver' => '0',
                'del' => '0',
                'time' => time(),
            );
            $isDataTalkContentInsert = $TalkContent->add($dataTalkContent);

            /**
             * trans emotion
             */
            Vendor('Ihelpoo.Emotion');
            $emotionObj = new Emotion();
            if ($isDataTalkContentInsert) {
                $UserLogin = M("UserLogin");
                $recordUserLogin = $UserLogin->where("uid = $postUid")->find();
                $returnData['nickname'] = $recordUserLogin['nickname'];
                $returnData['content'] = $emotionObj->transEmotion($postContent);
                $returnData['image'] = $postImage;
                $returnData['imagethumb'] = i_image_thumbnail($postImage);
                $returnData['time'] = date("H:i:s", time());
                $this->ajaxReturn($returnData, 'message ajax, return talk data', 'ok');
            }

            /**
             * add to talk list
             */
            $TalkList = M("TalkList");
            $talkListRecord = $TalkList->where("uid = $postUid AND listuid = $postTouid")->find();
            if (empty($talkListRecord['id'])) {
                $talkListData = array(
                    'id' => '',
                    'uid' => $postUid,
                    'listuid' => $postTouid,
                    'time' => time(),
                );
                $TalkList->add($talkListData);
            }
            $this->ajaxReturn($returnData, 'message ajax, add to talk list', 'ok');
        }
    }

    public function history()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '历史记录 悄悄话');
        $toUserId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));

        /**
         *
         */
        $IUserLogin = D("IUserLogin");
        $toUserLogin = $IUserLogin->userExists($toUserId);
        $this->assign('toUserLogin', $toUserLogin);
        $TalkContent = M("TalkContent");

        /**
         * pageing
         */
        $page = i_page_get_num();
        $count = 30;
        $offset = $page * $count;
        $talkHistory = $TalkContent->where("(uid = $userloginid AND touid = $toUserId AND del != $userloginid) OR (uid = $toUserId AND touid = $userloginid AND del != $userloginid)")->order("time DESC")->limit($offset, $count)->select();
        $this->assign('talkHistories', $talkHistory);

        /**
         * page link
         */
        $totalTalkHistoryNums = $TalkContent->where("(uid = $userloginid AND touid = $toUserId AND del != $userloginid) OR (uid = $toUserId AND touid = $userloginid AND del != $userloginid)")->count();
        $totalPages = ceil($totalTalkHistoryNums / $count);
        $this->assign('totalTalkHistoryNums', $totalTalkHistoryNums);
        $this->assign('totalPages', $totalPages);

        /**
         * delete talk history
         */
        if ($_GET['delete'] == 'sure') {
            foreach ($talkHistory as $talkHistoryIn) {
                if ($talkHistoryIn['del'] == $toUserId) {
                    $TalkContent->where("id = $talkHistoryIn[id]")->delete();
                } else if (empty($talkHistoryIn['del'])) {
                    $deleteData = array(
                        'id' => $talkHistoryIn['id'],
                        'del' => $userloginid
                    );
                    $TalkContent->save($deleteData);
                }
            }
            $this->assign('deleteFlag', 'yes');
        }
        $this->display();
    }

    public function add()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '加好友 悄悄话');
        $addUserId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));

        $IUserLogin = D("IUserLogin");
        $addUserRecord = $IUserLogin->userExists($addUserId);
        $this->assign('addUserLoginRecord', $addUserRecord);

        /**
         * add to talk list
         */
        $TalkList = M("TalkList");
        $talkListRecord = $TalkList->where("uid = $userloginid AND listuid = $addUserId")->find();
        if (empty($talkListRecord['id'])) {
            $talkListData = array(
                'id' => '',
                'uid' => $userloginid,
                'listuid' => $addUserId,
                'time' => time(),
            );
            $TalkList->add($talkListData);
            $this->assign('addStatus', 'true');
        } else {
            $this->assign('addStatus', 'false');
        }
        $this->display();
    }

    public function lists()
    {
        $userloginid = session('userloginid');
        $this->assign('title', '加好友 悄悄话');
        if (!empty($_GET["_URL_"][2])) {
            $toUserId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        }
        if (!empty($toUserId)) {
            $IUserLogin = D("IUserLogin");
            $toUserLogin = $IUserLogin->userExists($toUserId);
            $this->assign('toUserLogin', $toUserLogin);
        }

        $TalkList = M("TalkList");

        /**
         * remove user from userlist
         */
        if (!empty($_GET['delete']) == 'sure' && !empty($toUserId)) {
            $isUserRemove = $TalkList->where("uid = $userloginid AND listuid = $toUserId")->delete();
            if ($isUserRemove) {
                $this->assign('userRemoveFlag', 'true');
            }
        }

        $allTalkList = $TalkList->where("uid = $userloginid")->find();
        if (!empty($allTalkList['id'])) {
            $talkLists = $TalkList->where("i_talk_list.uid = $userloginid")
                ->join('i_user_login ON i_talk_list.listuid = i_user_login.uid')
                ->join('i_user_info ON i_talk_list.listuid = i_user_info.uid')
                ->order('i_user_login.online DESC')
                ->limit(100)
                ->select();
            $this->assign('talkLists', $talkLists);
        }
        $this->display();
    }
}

?>