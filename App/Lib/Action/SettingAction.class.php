<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class SettingAction extends Action
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
            $this->assign('schoolname', $recordSchoolInfo['school']);
            $configIsLoginWeibo = C('IS_LOGIN_WEIBO');
	        $this->assign('configIsLoginWeibo', $configIsLoginWeibo);
        } else {
            redirect('/user/notlogin', 0, '你还没有登录呢...');
        }
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index()
    {
        $this->assign('title', '账号资料设置');
        $userloginid = session('userloginid');
        $UserLogin = M("UserLogin");
        $recordUserLogin = $UserLogin->find($userloginid);
        $UserInfo = M("UserInfo");
        $recordUserInfo = $UserInfo->find($userloginid);

        /**
         * post data
         */
        if ($this->isPost()) {
            $validate = array(
                array('nickname', 'require', '昵称不能为空'),
                array('usertype', 'require', 'usertype不能为空'),
                array('sex', 'number', 'sex格式错误'),
                array('enteryear', 'number', 'enteryear格式错误'),
                array('school', 'number', 'school格式错误'),
                array('academy', 'number', 'academy格式错误'),
                array('specialty', 'number', 'specialty格式错误'),
                array('year', 'number', 'year格式错误'),
                array('month', 'number', 'month格式错误'),
                array('day', 'number', 'day格式错误'),
                array('dormitory', 'number', 'dormitory格式错误'),
                array('province', 'number', 'province格式错误'),
                array('city', 'number', 'city格式错误'),
                array('qq', 'number', 'qq格式错误', 2),
                array('weibo', 'url', 'weibo格式错误 必须是url', 2),
                array('mobile', 'number', 'mobile格式错误', 2),
            );
            $UserLogin->setProperty("_validate", $validate);
            $result = $UserLogin->create();
            if (!$result) {
                $errorinfo = $UserLogin->getError();
                $this->ajaxReturn(0, $errorinfo, 'wrong');
            } else {
                $nickname = trim(addslashes(htmlspecialchars(strip_tags($_POST["nickname"]))));
                $nickname = str_ireplace(' ', '', $nickname);
                $nickname = preg_replace('/[^a-zA-Z\x{4e00}-\x{9fa5}{0-9}_]/u','',$nickname);
                $usertype = trim(addslashes(htmlspecialchars(strip_tags($_POST["usertype"]))));
                $sex = trim(htmlspecialchars(strip_tags($_POST["sex"])));
                $enteryear = trim(htmlspecialchars(strip_tags($_POST["enteryear"])));
                $year = trim(htmlspecialchars(strip_tags($_POST["year"])));
                $month = trim(htmlspecialchars(strip_tags($_POST["month"])));
                $day = trim(htmlspecialchars(strip_tags($_POST["day"])));
                //db seperater
                $school = trim(htmlspecialchars(strip_tags($_POST["school"])));
                $academy = trim(htmlspecialchars(strip_tags($_POST["academy"])));
                $specialty = trim(htmlspecialchars(strip_tags($_POST["specialty"])));
                $dormitory = trim(htmlspecialchars(strip_tags($_POST["dormitory"])));
                $introduction = trim(addslashes(htmlspecialchars(strip_tags($_POST["introduction"]))));
                //seperater
                $province = trim(htmlspecialchars(strip_tags($_POST["province"])));
                $city = trim(htmlspecialchars(strip_tags($_POST["city"])));
                $qq = trim(htmlspecialchars(strip_tags($_POST["qq"])));
                $weibo = trim(addslashes(strip_tags($_POST["weibo"])));
                //realinfo
                $mobile = trim(htmlspecialchars(strip_tags($_POST["mobile"])));
                $birthday = $year . "-" . $month . "-" . $day;
                $isNicknameUseUserLogin = $UserLogin->where("nickname = '$nickname'")->find();
                if (!empty($isNicknameUseUserLogin['uid']) && ($isNicknameUseUserLogin['uid'] != $recordUserLogin['uid'])) {
                    $this->ajaxReturn(0, '这个昵称已经被别人占用', 'wrong');
                }
                
                /**
                 * 1 for default student
                 * 2 for group
                 * 3 for business
                 * 4 for teacher
                 * 5 for postgraduate
                 * 6 for senior
                 */
	            $type = 1;
	            if ($usertype == 'default') {
	            	$type = 1;
	            } else if ($usertype == 'teacher') {
	            	$type = 4;
	            } else if ($usertype == 'postgraduate') {
	            	$type = 5;
	            } else if ($usertype == 'senior') {
	            	$type = 6;
	            }

                /**
                 * update i_user_login
                 */
                $updateUserloginData = array(
                    'uid' => $userloginid,
                    'nickname' => $nickname,
                    'sex' => $sex,
                    'birthday' => $birthday,
                    'enteryear' => $enteryear,
                	'type' => $type,
                    'school' => $school
                );
                $UserLogin->save($updateUserloginData);

                /**
                 * update i_user_info
                 */
                $updateUserInfoData = array(
                    'uid' => $userloginid,
                    'introduction' => $introduction,
                    'academy_op' => $academy,
                    'specialty_op' => $specialty,
                    'dormitory_op' => $dormitory,
                    'province_op' => $province,
                    'city_op' => $city,
                    'mobile' => $mobile,
                    'qq' => $qq,
                    'weibo' => $weibo,
                );
                $UserInfo->save($updateUserInfoData);
                $this->ajaxReturn(0, "修改成功", 'yes');
            }
        }

        $schoolId = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
        if (empty($schoolId)) {
            redirect('/setting/index/' . $recordUserLogin['school'], 0, '缺少学校参数...');
            $schoolId = $recordUserLogin['school'];
        }
        
        /**
         * is school webmaster
         */
        $SchoolWebmaster = M("SchoolWebmaster");
        $isSchoolWebmaster = $SchoolWebmaster->where("uid = $userloginid")->find();
        if (!empty($isSchoolWebmaster['id']) && ($schoolId != $isSchoolWebmaster['sid'])) {
        	redirect('/setting/index/' . $recordUserLogin['school'], 3, '你是站长，不能切换学校，阵地需要坚守:)...');
        }

        /**
         * school info
         */
        $SchoolInfo = M("SchoolInfo");
        $recordSchoolInfo = $SchoolInfo->find($schoolId);
        $listSchoolInfo = $SchoolInfo->select();
        $this->assign('recordSchoolInfo', $recordSchoolInfo);

        /**
         * show user info
         */
        $OpAcademy = M("OpAcademy");
        $listOpAcademy = $OpAcademy->where("school = $schoolId")->select();
        $OpSpecialty = M("OpSpecialty");

        if (!empty($recordUserInfo['academy_op'])) {
            $listOpSpecialty = $OpSpecialty->where("academy = $recordUserInfo[academy_op]")->select();
        } else {
            $listOpSpecialty = $OpSpecialty->where("school = $schoolId")->select();
        }

        $OpDormitory = M("OpDormitory");
        $listOpDormitory = $OpDormitory->where("school =$schoolId")->select();
        $OpProvince = M("OpProvince");
        $listOpProvince = $OpProvince->select();
        $OpCity = M("OpCity");

        if (!empty($recordUserInfo['province_op'])) {
            $listOpCity = $OpCity->where("prov_id = $recordUserInfo[province_op]")->select();
        } else {
            $listOpCity = $OpCity->where("prov_id = 27")->select();
        }

        if (!empty($recordUserLogin['birthday'])) {
            $birthstring = $recordUserLogin['birthday'];
            $birtharray = explode("-", $birthstring);
            $birthyear = $birtharray[0];
            $birthmonth = $birtharray[1];
            $birthdate = $birtharray[2];
        }

        if (!empty($recordUserInfo['dormitory_op'])) {
            $recordDormitory = $OpDormitory->where("id = $recordUserInfo[dormitory_op]")->find();
        }
        
        /**
         * view
         */
        $this->assign('title', '账号设置');
        $this->assign('recorduserlogin', $recordUserLogin);
        $this->assign('recorduserinfo', $recordUserInfo);
        $this->assign('listschoolinfo', $listSchoolInfo);
        $this->assign('listopacademy', $listOpAcademy);
        $this->assign('listopspecialty', $listOpSpecialty);
        $this->assign('listopdormitory', $listOpDormitory);
        $this->assign('listopprovince', $listOpProvince);
        $this->assign('listopcity', $listOpCity);
        $this->assign('birthyear', $birthyear);
        $this->assign('birthmonth', $birthmonth);
        $this->assign('birthdate', $birthdate);
        $this->assign('dormitorytype', $recordDormitory['type']);
        $this->display();
    }

    public function g()
    {

        $uid = session('userloginid');
        $gid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));

        $UserGroup = M("UserGroup");
        $userGroup = $UserGroup->where("id = $gid")->find();

        $this->assign('userGroup', $userGroup);


        $UserPriority = M("UserPriority");
        $userPrioritys = $UserPriority->where("i_user_priority.uid = $uid AND group_id=$gid AND pid != ''")
            ->join('i_user_login ON i_user_priority.pid = i_user_login.uid')
            ->order('i_user_priority.time DESC')
            ->select();
        $this->assign('userPrioritys', $userPrioritys);
        $this->display();

    }

    public function groupme()
    {
        if ($this->isPost()) {

            $id = $_POST['id'];
            $gids = $_POST['gids'];

            $gidsArr = explode(",", $gids);
            $firstGid = $gidsArr[0];

            //TODO, support one person - multiple groups


            $UserPriority = M("UserPriority");
            $groupData = array(
                "id" => $id,
                "group_id" => $firstGid,
            );
            $UserPriority->save($groupData);
            $this->ajaxReturn(0, "创建成功", 'yes');
        }
    }


    public function groupDelete()
    {

        $gid = $_POST["group_id"];
        if ($this->isPost()) {
            $IUserGroup = M("UserGroup");
            $IUserGroup->where("id = $gid")->delete();
            $this->ajaxReturn(0, "删除分组成功", 'yes');
        }
    }

    public function groupUpdate()
    {

        if ($this->isPost()) {
            $IUserGroup = M("UserGroup");
            $gid = $_POST["group_id"];
            $groupName = trim(addslashes(htmlspecialchars(strip_tags($_POST["group_name"]))));
            $groupDesc = trim(addslashes(htmlspecialchars(strip_tags($_POST["group_desc"]))));
            $groupData = array(
                'id' => $gid,
                'group_name' => $groupName,
                'group_desc' => $groupDesc,
                'update_time' => time(),
            );
            $IUserGroup->save($groupData);
            $this->ajaxReturn(0, "更新分组成功", 'yes');
        }
    }


    public function group()
    {

        $this->assign('title', '分组');
        $uid = session('userloginid');

        $UserGroup = M('UserGroup');
        $userGroup = $UserGroup->where("uid = $uid")->select();
        $this->assign('userGroups', $userGroup);


        $UserPriority = M("UserPriority");
        $userPrioritys = $UserPriority->where("i_user_priority.uid = $uid AND pid != ''")
            ->join('i_user_login ON i_user_priority.pid = i_user_login.uid')
            ->order('i_user_priority.time DESC')
            ->select();
        $this->assign('userPrioritys', $userPrioritys);

        if ($this->isPost()) {
            $IUserGroup = D("UserGroup");
            $validate = array(
                array('group_name', 'require', '请输入分组名'),
            );

            $IUserGroup->setProperty("_validate", $validate);
            $result = $IUserGroup->create();

            if (!$result) {
                exit($IUserGroup->getError());
            } else {
                $groupName = trim(addslashes(htmlspecialchars(strip_tags($_POST["group_name"]))));
                $groupDesc = trim(addslashes(htmlspecialchars(strip_tags($_POST["group_desc"]))));
                $groupData = array(
                    'group_name' => $groupName,
                    'group_desc' => $groupDesc,
                    'uid' => $uid,
                    'create_time' => time(),
                    'update_time' => time(),
                );
                $IUserGroup->add($groupData);
                $this->ajaxReturn(0, "创建成功", 'yes');
            }
        }
        $this->display();
    }

    public function ps()
    {
        $this->assign('title', '修改密码');
        $userloginid = session('userloginid');
        if ($this->isPost()) {
            $IUserLogin = D("IUserLogin");
            $validate = array(
                array('passwordoriginal', 'require', '原始密码不能为空'),
                array('password', 'require', '密码不能为空'),
                array('passwordrepeat', 'password', '两次密码不一致', 0, 'confirm'),
            );
            $IUserLogin->setProperty("_validate", $validate);
            $result = $IUserLogin->create();
            if (!$result) {
                exit($IUserLogin->getError());
            } else {
                $passwordoriginal = trim(addslashes(htmlspecialchars(strip_tags($_POST["passwordoriginal"]))));
                $password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));
                $recordUserLogin = $IUserLogin->userExists($userloginid);
                if ($recordUserLogin['password'] == md5($passwordoriginal)) {
                    $password = md5($password);

                    /**
                     * iuc user data
                     */
                    $updateUserlogignData = array(
                        'uid' => $userloginid,
                        'password' => $password,
                    );
                    $IUserLogin->save($updateUserlogignData);
                    $this->ajaxReturn(0, "修改成功", 'yes');
                } else {
                    $this->ajaxReturn(0, "原始密码错误", 'wrong');
                }
            }
        }
        $this->display();
    }


    public function icon()
    {
        $this->assign('title', '修改头像');
        $userloginid = session('userloginid');
        $UserLogin = M("UserLogin");
        if (!empty($_POST['icontemppath'])) {
            $cutIconFullPath = $_POST['icontemppath'];
            $iconTempRealSize = getimagesize($cutIconFullPath);
            $iconRealWidth = $iconTempRealSize['0'];
            $iconRealHeight = $iconTempRealSize['1'];
            $imageType = $iconTempRealSize['mime'];

            /**
             * Calculate the ratio first
             */
            $iconRatio = $iconRealWidth / 500;
            $dst_x = round($_POST['iconx'] * $iconRatio);
            $dst_y = round($_POST['icony'] * $iconRatio);
            $dst_w = round($_POST['iconw'] * $iconRatio);
            $dst_h = round($_POST['iconh'] * $iconRatio);

            if ($imageType == 'image/jpeg') {
                $imgOld = imagecreatefromjpeg($cutIconFullPath);
            } else if ($imageType == 'image/gif') {
                $imgOld = imagecreatefromgif($cutIconFullPath);
            } else if ($imageType == 'image/png') {
                $imgOld = imagecreatefrompng($cutIconFullPath);
            }

            /**
             * 500 * 375 size
             */
            $imgObj = imagecreatetruecolor(500, 375);

            /**
             * php function book page 398
             */
            imagecopyresampled($imgObj, $imgOld, 0, 0, $dst_x, $dst_y, 500, 375, $dst_w, $dst_h);

            /**
             * new image file
             */
            $srcTempLargeIconFilename = 'temp' . $userloginid . '.jpg';
            $srcTempMiddleIconFilename = 'temp' . $userloginid . '_m.jpg';
            $srcTempSmallIconFilename = 'temp' . $userloginid . '_s.jpg';
            imagejpeg($imgObj, $srcTempLargeIconFilename);

            /**
             * destroy
             */
            imagedestroy($imgObj);
            imagedestroy($imgOld);

            /**
             * create size middle 180 * 135 & small 68 * 51
             */
            $imgLarge = imagecreatefromjpeg($srcTempLargeIconFilename);
            $imgMiddleObj = imagecreatetruecolor(180, 135);
            imagecopyresampled($imgMiddleObj, $imgLarge, 0, 0, 0, 0, 180, 135, 500, 375);
            imagejpeg($imgMiddleObj, $srcTempMiddleIconFilename, 100);
            $imgSmallObj = imagecreatetruecolor(68, 51);
            imagecopyresampled($imgSmallObj, $imgLarge, 0, 0, 0, 0, 68, 51, 500, 375);
            imagejpeg($imgSmallObj, $srcTempSmallIconFilename, 100);
            imagedestroy($imgMiddleObj);
            imagedestroy($imgSmallObj);
            imagedestroy($imgLarge);

            /**
             * image handle ok print json
             */
            $fullpath = "/useralbum/" . $userloginid . "/";
            $newImageName = "icon" . $userloginid . time();
            $storageLargeIconFilename = $fullpath . $newImageName . ".jpg";
            $storageMiddleIconFilename = $fullpath . $newImageName . "_m.jpg";
            $storageSmallIconFilename = $fullpath . $newImageName . "_s.jpg";

            /**
             * storage in upyun
             */
            Vendor('Ihelpoo.Upyun');
            $upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
            try {
                $fh = fopen($srcTempLargeIconFilename, 'rb');
                $rsp = $upyun->writeFile($storageLargeIconFilename, $fh, True);
                fclose($fh);

                $fh = fopen($srcTempMiddleIconFilename, 'rb');
                $rsp = $upyun->writeFile($storageMiddleIconFilename, $fh, True);
                fclose($fh);

                $fh = fopen($srcTempSmallIconFilename, 'rb');
                $rsp = $upyun->writeFile($storageSmallIconFilename, $fh, True);
                fclose($fh);
                $imageStorageUrl = image_storage_url();
                $newfilepath = $imageStorageUrl . $storageLargeIconFilename;
            } catch (Exception $e) {
                $errorUpyunCode = $e->getCode();
                $errorUpyunMessage = $e->getMessage();
                $errorUpyun = 'upyun-code:' . $errorUpyunCode . 'upyun-message:' . $errorUpyunMessage;
                $this->ajaxReturn(0, $errorUpyun, 'wrong');
            }

            unset($srcTempLargeIconFilename);
            unset($srcTempMiddleIconFilename);
            unset($srcTempSmallIconFilename);

            /**
             * update i_user_login
             */
            $newLoginIconData = array(
                'uid' => $userloginid,
                'icon_fl' => 1,
                'icon_url' => $newImageName,
            );
            $UserLogin->save($newLoginIconData);

            /**
             * add default dynamic record.
             */
            $recordDynamicContent = "我刚刚换了新头像噢 :)";
            $RecordSay = M("RecordSay");
            $RecordDynamic = M("RecordDynamic");
            $newRecordSayData = array(
                'uid' => $userloginid,
                'say_type' => 2,
                'content' => $recordDynamicContent,
                'time' => time(),
                'from' => '动态'
            );
            $newRecordSayId = $RecordSay->add($newRecordSayData);

            $UserAlbum = M("UserAlbum");
            $lastRecordUserAlbum = $UserAlbum->where("uid = $userloginid")->order('time DESC')->find();
            $newRecordDynamicData = array(
                'sid' => $newRecordSayId,
                'type' => 'changeicon',
                'url_id' => $lastRecordUserAlbum['id']
            );
            $RecordDynamic->add($newRecordDynamicData);
            $this->ajaxReturn($newfilepath, '保存成功', 'ok');
        }

        if ($this->isPost()) {
            if (!empty($_FILES)) {
                if ($_FILES["uploadedimg"]["error"] > 0) {
                    $this->ajaxReturn(0, '上传图片失败, info' . $_FILES["uploadedimg"]["error"], 'error');
                } else {
                    $imageOldName = $_FILES["uploadedimg"]["name"];
                    $imageType = $_FILES["uploadedimg"]["type"];
                    $imageType = trim($imageType);
                    $imageSize = $_FILES["uploadedimg"]["size"];
                    $imageTmpName = $_FILES["uploadedimg"]["tmp_name"];
                }
                if ($imageSize > 3670016) {
                    $this->ajaxReturn(0, '上传图片太大, 最大能上传单张 3.5MB', 'error');
                } else if ($imageType == 'image/jpeg' || $imageType == 'image/pjpeg' || $imageType == 'image/gif' || $imageType == 'image/x-png' || $imageType == 'image/png') {

                    /**
                     * storage in upyun
                     */
                    Vendor('Ihelpoo.Upyun');
                    $upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
                    $fh = fopen($imageTmpName, 'rb');
                    $fileName = 'iconorignal' . $userloginid . time() . '.jpg';
                    $storageTempFilename = '/useralbum/' . $userloginid . '/' . $fileName;
                    $rsp = $upyun->writeFile($storageTempFilename, $fh, True);
                    fclose($fh);
                    $imageStorageUrl = image_storage_url();
                    $newfilepath = $imageStorageUrl . $storageTempFilename;

                    $opts = array(
                        UpYun::X_GMKERL_TYPE => 'fix_max',
                        UpYun::X_GMKERL_VALUE => 150,
                        UpYun::X_GMKERL_QUALITY => 95,
                        UpYun::X_GMKERL_UNSHARP => True
                    );
                    $fh = fopen($imageTmpName, 'rb');
                    $storageThumbTempFilename = '/useralbum/' . $userloginid . '/thumb_' . $fileName;
                    $rsp = $upyun->writeFile($storageThumbTempFilename, $fh, True, $opts);
                    fclose($fh);

                    /**
                     * insert into i_user_album
                     */
                    $UserAlbum = M("UserAlbum");
                    $newAlbumIconData = array(
                        'uid' => $userloginid,
                        'type' => 1,
                        'url' => $newfilepath,
                        'size' => $imageSize,
                        'time' => time()
                    );
                    $UserAlbum->add($newAlbumIconData);

                    /**
                     * ajax return
                     */
                    $this->ajaxReturn($newfilepath, '上传成功', 'uploaded');
                } else {
                    $this->ajaxReturn(0, '上传图片格式错误, 目前仅支持.jpg .png .gif', 'error');
                }
            }
            exit();
        }
        $this->display();
    }

    public function bind()
    {
        $this->assign('title', '绑定微博');
        $userloginid = session('userloginid');
        $UserLoginWb = M("UserLoginWb");
        $recordUserLoginWb = $UserLoginWb->where("uid = $userloginid")->find();
        if (!empty($recordUserLoginWb['uid'])) {
            $this->assign('isAlreadyBind', $recordUserLoginWb['weibo_uid']);
        }
        if (!empty($_POST['weibo_user_id'])) {
            $isbindUserLoginWb = $UserLoginWb->where("weibo_uid = $_POST[weibo_user_id]")->find();
            if (!empty($isbindUserLoginWb['uid'])) {
                $this->ajaxReturn(0, '这个微博已经绑定了账号，请选择另一个微博', 'wrong');
            }

            $bindData = array(
                'uid' => $userloginid,
                'weibo_uid' => $_POST['weibo_user_id'],
            );
            $isBind = $UserLoginWb->add($bindData);
            if ($isBind) {
                $this->ajaxReturn(0, '绑定成功', 'ok');
            }
        }
        $this->display();
    }

    public function realfirst()
    {
        $this->assign('title', '快速匹配个人信息');
        $userloginid = session('userloginid');
        $UserInfo = M("UserInfo");
        $UserLogin = M("UserLogin");
        $recordUserInfo = $UserInfo->find($userloginid);
        $this->assign('recordUserInfo', $recordUserInfo);
        if ($this->isPost()) {

            /**
             * Filte rules; For the realinfo reset post data
             */
            $postrealname = trim(addslashes(htmlspecialchars(strip_tags($_POST['realname']))));
            $this->assign('realnametemp', $postrealname);
            $updateUserInfoReal = array(
                'uid' => $userloginid,
                'realname' => $postrealname,
                'realname_re' => 1,
            );
            $UserInfo->save($updateUserInfoReal);
            redirect('/setting/realfirst?step=2', 0, '继续填充...');
        }
        $this->display();
    }

    public function fillaccount()
    {
        $this->assign('title', '完善账号');
        $userloginid = session('userloginid');
        $UserLogin = M("UserLogin");

        /**
         * post
         */
        if ($this->isPost()) {
            $validate = array(
                array('email', 'email', '邮箱格式不对'),
                array('email', '', '邮箱已经存在！', 0, 'unique', 1),
                array('password', 'require', '密码不能为空'),
            );
            $UserLogin->setProperty("_validate", $validate);
            $result = $UserLogin->create();
            if (!$result) {
                $errorRegister = $UserLogin->getError();
                $this->ajaxReturn(0, $errorRegister, 'wrong');
            } else {
                $email = htmlspecialchars(strtolower(trim($_POST["email"])));
                $password = trim(addslashes(htmlspecialchars(strip_tags($_POST["password"]))));
                $password = md5($password);
                $recordUserLogin = $UserLogin->find($userloginid);
                if (empty($recordUserLogin['email']) || ($recordUserLogin['status'] == '1')) {
                    $newUserlogignData = array(
                        'uid' => $userloginid,
                        'status' => '1',
                        'email' => $email,
                        'password' => $password,
                    );
                    $newUserId = $UserLogin->save($newUserlogignData);
                    $this->ajaxReturn(0, '', 'yes');
                } else {
                    $this->ajaxReturn(0, '账号登录资料已经完善，无需修改', 'wrong');
                }
            }
        }
        $this->display();
    }

    public function ajax()
    {
        if (!empty($_POST['getschoollist'])) {
            $SchoolInfo = M("SchoolInfo");
            $resultsSchoolInfo = $SchoolInfo->where("status = 1")->order("initial ASC")->select();
            echo '<div class="setting_school_list_div"><a class="gray f12" id="setting_school_close_span"><span class="close_x" title="关闭">×</span></a><ul>';
            foreach ($resultsSchoolInfo as $schoolInfo) {
                echo "<li>[".strtoupper($schoolInfo['initial'])."]<a href='" . __ROOT__ . "/setting/index/$schoolInfo[id]'>$schoolInfo[school]</a></li>";
            }
            echo '</ul></div>';
            exit();
        }
        if (!empty($_POST['selectAcademy'])) {
            $selectAcademyNum = (int)$_POST['selectAcademy'];
            $OpSpecialty = M("OpSpecialty");
            $selectSpecialtyObj = $OpSpecialty->where("academy = $selectAcademyNum")->select();
            echo '<select id="specialty" name="specialty">';
            foreach ($selectSpecialtyObj as $selectSpecialty) {
                echo "<option value='$selectSpecialty[id]'>$selectSpecialty[name]</option>";
            }
            echo '</select>';
            exit();
        }
        if (!empty($_POST['selectDormitory']) && !empty($_POST['selectSchool'])) {
            $selectDormitoryType = (int)$_POST['selectDormitory'];
            $selectSchool = (int)$_POST['selectSchool'];
            $OpDormitory = M("OpDormitory");
            $selectDormitoryObj = $OpDormitory->where("type = $selectDormitoryType AND school = $selectSchool")->select();
            echo '<select id="dormitory" name="dormitory">';
            foreach ($selectDormitoryObj as $selectDormitory) {
                echo "<option value='$selectDormitory[id]'>$selectDormitory[name]</option>";
            }
            echo '</select>';
            exit();
        }
        if (!empty($_POST['monthAjax']) || !empty($_POST['yearAjax'])) {
            $month = (int)$_POST['monthAjax'];
            $year = (int)$_POST['yearAjax'];
            if (4 == $month || 6 == $month || 9 == $month || 11 == $month) {
                $mouthDay = 30;
            } elseif (1 == $month || 3 == $month || 5 == $month || 7 == $month || 8 == $month || 10 == $month || 12 == $month) {
                $mouthDay = 31;
            } else {
                if (($year % 4 == 0) && ($year % 100 != 0) || $year % 400 == 0) {
                    $mouthDay = 29;
                } else {
                    $mouthDay = 28;
                }
            }
            $i = 1;
            echo '<select id="day" name="day">';
            while ($i <= $mouthDay) {
                echo "<option value=\"" . $i . "\">" . $i . "</option>";
                $i++;
            }
            echo "</select>";
            exit();
        }
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
        exit();
    }
}

?>