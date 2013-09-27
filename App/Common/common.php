<?php
/**
 * config function
 */
function image_storage_url()
{
    return $url = C('IMAGE_STORAGE_URL');
}

function i_school_domain()
{
    $SchoolInfo = M("SchoolInfo");
    $domain = $_SERVER['HTTP_HOST'];
    if (preg_match("/\.ihelpoo/iUs", $domain)) {
        $recordSchoolInfo = $SchoolInfo->where("domain = '$domain'")->find();
    } else {
        $recordSchoolInfo = $SchoolInfo->where("domain_main = '$domain'")->find();
    }
    if (!is_array($recordSchoolInfo)) {
    	redirect('/index/changeschool', 0, '跳转页面 :)...');
    }
    return $recordSchoolInfo;
}

/**
 *
 * uodate last active time in db i_user_status...
 * @param int $uid
 */
function i_db_update_activetime($uid)
{
    $User = M("UserStatus");
    $recordUserStatus = $User->where("uid = $uid")->find();
    $timeAdd = time() - $recordUserStatus['last_active_ti'];
    if ($timeAdd > 999) {
        $timeAdd = 0;
    }
    $data['total_active_ti'] = $recordUserStatus['total_active_ti'] + $timeAdd;
    $data['last_active_ti'] = time();
    $data['acquire_seconds'] = 1500;
    $data['acquire_times'] = 5;
    return $User->where("uid = $uid")->save($data);
}

function i_ajax_msg($uid)
{
    $UserStatus = M("UserStatus");
    $recordUserStatus = $UserStatus->where("uid = $uid")->find();
    $acquireTimes = $recordUserStatus['acquire_times'];
    if ($acquireTimes < 10) {
        $acquireMilliseconds = 3000; //30s
    } else if ($acquireTimes < 20) {
        $acquireMilliseconds = 6000; //60s
    } else if ($acquireTimes < 30) {
        $acquireMilliseconds = 9000; //90s
    } else if ($acquireTimes < 40) {
        $acquireMilliseconds = 15000; //150s
    } else {
        $acquireMilliseconds = 900000;
        $data['last_active_ti'] = time();
        $data['total_active_ti'] = $recordUserStatus['total_active_ti'] + 900;
    }
    $data['acquire_times'] = $acquireTimes + 1;
    $data['acquire_seconds'] = $acquireMilliseconds;
    $UserStatus->where("uid = $uid")->save($data);
    return $UserStatus->where("uid = $uid")->find();
}

function i_time($unixtime)
{
    $now = time();
    $secondWidth = $now - $unixtime;
    if ($secondWidth < 60) {
        return $secondWidth . "秒前";
    } else if ($secondWidth < 3600) {
        return floor($secondWidth / 60) . "分钟前" . date("H:i", $unixtime);
    } else if ($secondWidth < 43200) {
        return floor($secondWidth / 3600) . "小时前" . date("H:i", $unixtime);
    } else if ($secondWidth < 86400) {
        return floor($secondWidth / 3600) . "小时前" . date("d日H:i", $unixtime);
    } else if ($secondWidth > 31536000) {
        return date("Y年m月d H:i", $unixtime);
    } else {
        return date("m月d H:i", $unixtime);
    }
}

function i_grade($enteryear)
{
    if (empty($enteryear)) {
        return NULL;
    }
    $thisyear = getdate();
    if ($thisyear['mon'] > 8) {
        $num = $thisyear['year'] - $enteryear + 1;
    } else {
        $num = $thisyear['year'] - $enteryear;
    }
    switch ($num) {
    	case 0 :
            return "要上大学了";
            break;
        case 1 :
            return "大一";
            break;
        case 2 :
            return "大二";
            break;
        case 3 :
            return "大三";
            break;
        case 4 :
            return "大四";
            break;
        case 5 :
            return "毕业1年或大五";
            break;
        case 22 :
            return "上世纪入学的前辈"; //ihelpoo alive 1 year
            break;
        case 23 :
            return "上世纪入学的大前辈"; //ihelpoo already alived 2 years
            break;
        case 24 :
            return "上世纪入学的老前辈"; //ihelpoo already alived 3 years
            break;
        default:
            return "毕业" . ($num - 4) . "年了";
    }
}

function i_gradenum($enteryear)
{
    if (empty($enteryear)) {
        return NULL;
    }
    $thisyear = getdate();
    if ($thisyear['mon'] > 8) {
        $num = $thisyear['year'] - $enteryear + 1;
    } else {
        $num = $thisyear['year'] - $enteryear;
    }
    switch ($num) {
    	case 0 :
            return "bef";
            break;
        case 1 :
            return "1";
            break;
        case 2 :
            return "2";
            break;
        case 3 :
            return "3";
            break;
        case 4 :
            return "4";
            break;
        case 5 :
            return "5";
            break;
        default:
            return "6";
    }
}

function i_sex($sex)
{
    if ($sex == 1) {
        return "男";
    } else if ($sex == 2) {
        return "女";
    }
}

function i_constellation($birthdate)
{
    if (empty($birthdate)) {
        return NULL;
    }
    $birthstring = explode("-", $birthdate);
    $birthmonth = $birthstring[1];
    $birthday = $birthstring[2];
    if ($birthday < 10) {
        $birthday = "0" . $birthday;
    }
    $birthint = $birthmonth . $birthday;
    if (101 <= $birthint && $birthint <= 119) {
        return "摩羯";
    } else if (120 <= $birthint && $birthint <= 218) {
        return "水瓶";
    } else if (219 <= $birthint && $birthint <= 320) {
        return "双鱼";
    } else if (321 <= $birthint && $birthint <= 419) {
        return "白羊";
    } else if (420 <= $birthint && $birthint <= 520) {
        return "金牛";
    } else if (521 <= $birthint && $birthint <= 621) {
        return "双子";
    } else if (622 <= $birthint && $birthint <= 722) {
        return "巨蟹";
    } else if (723 <= $birthint && $birthint <= 822) {
        return "狮子";
    } else if (823 <= $birthint && $birthint <= 922) {
        return "处女";
    } else if (923 <= $birthint && $birthint <= 1023) {
        return "天枰";
    } else if (1024 <= $birthint && $birthint <= 1122) {
        return "天蝎";
    } else if (1123 <= $birthint && $birthint <= 1221) {
        return "射手";
    } else if (1122 <= $birthint && $birthint <= 1231) {
        return "摩羯";
    } else {
        return NULL;
    }
}

function i_helpstatus($status)
{
    switch ($status) {
        case 1 :
            return "请求帮助";
            break;
        case 2 :
            return "帮助中";
            break;
        case 3 :
            return "帮助结束";
            break;
    }
}

function i_degree($active)
{
    if ($active < 30) {
        return 1;
    } else if ($active < 120) {
        return 2;
    } else if ($active < 330) {
        return 3;
    } else if ($active < 720) {
        return 4;
    } else if ($active < 1350) {
        return 5;
    } else if ($active < 3990) {
        return 6;
    } else if ($active < 10200) {
        return 7;
    } else if ($active < 22230) {
        return 8;
    } else if ($active < 41280) {
        return 9;
    } else {
        return 10;
    }
}

function i_degreeRatio($active)
{
    if ($active < 30) {
        $differ = $active - 0;
        $length = 30 - 0;
        return array(
            'start' => 0,
            'end' => 30,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else if ($active < 120) {
        $differ = $active - 30;
        $length = 120 - 30;
        return array(
            'start' => 30,
            'end' => 120,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else if ($active < 330) {
        $differ = $active - 120;
        $length = 330 - 120;
        return array(
            'start' => 120,
            'end' => 330,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else if ($active < 720) {
        $differ = $active - 330;
        $length = 720 - 330;
        return array(
            'start' => 330,
            'end' => 720,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else if ($active < 1350) {
        $differ = $active - 720;
        $length = 1350 - 720;
        return array(
            'start' => 720,
            'end' => 1349,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else if ($active < 3990) {
        $differ = $active - 1350;
        $length = 3990 - 1350;
        return array(
            'start' => 1350,
            'end' => 3990,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else if ($active < 10200) {
        $differ = $active - 3990;
        $length = 10200 - 3990;
        return array(
            'start' => 3990,
            'end' => 10200,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else if ($active < 22230) {
        $differ = $active - 10200;
        $length = 22230 - 10200;
        return array(
            'start' => 10200,
            'end' => 22230,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else if ($active < 41280) {
        $differ = $active - 22230;
        $length = 41280 - 22230;
        return array(
            'start' => 22230,
            'end' => 41280,
            'differ' => $differ,
            'ratio' => $differ / $length,
        );
    } else {
        $differ = $active - 41280;
        return array(
            'start' => 41280,
            'differ' => $differ,
        );
    }
}

function i_content($content)
{
    $patternText = array("\n");
    $replacementText = array("<br />");
    $contentReplaced = str_replace($patternText, $replacementText, $content);
    $contentReplaced = stripslashes($contentReplaced);
    return $contentReplaced;
}

/**
 *
 */
function i_logo_shop($uid, $shopBanner, $size = "d")
{
    $baseUrl = image_storage_url()."/";
    if (!empty($shopBanner)) {
        if ($size == "d") {
            $logoPath = $baseUrl . "shop/" . $uid . "/" . $shopBanner;
        } else if ($size == "s") {
            $logoPath = $baseUrl . "shop/" . $uid . "/thumb_" . $shopBanner;
        }
        return $logoPath;
    } else {
        return __PUBLIC__ . "/image/common/logo_banner.jpg";
    }
}

/**
 *
 */
function i_goods_thumbnail($imageurl, $w = "s")
{
    if (empty($imageurl)) {
        return __PUBLIC__ . '/image/common/questionmark.jpg';
    }
    if ("s" == $w) {
        return str_ireplace("goods", "thumb_goods", $imageurl);
    } else if ("l" == $w) {
        return $imageurl;
    }
}

function i_image_thumbnail($imageurl, $w = "s")
{
    if (empty($imageurl)) {
        return __PUBLIC__ . '/image/common/questionmark.jpg';
    }
    if ("s" == $w) {
        if (preg_match("/recordsay/", $imageurl)) {
            return str_ireplace("recordsay", "thumb_recordsay", $imageurl);
        } else if (preg_match("/iconorignal/", $imageurl)) {
            return str_ireplace('iconorignal', 'thumb_iconorignal', $imageurl);
        } else if (preg_match("/goods/", $imageurl)) {
            return str_ireplace('goods', 'thumb_goods', $imageurl);
        } else if (preg_match("/logo/", $imageurl)) {
            return str_ireplace('logo', 'thumb_logo', $imageurl);
        } else if (preg_match("/talk/", $imageurl)) {
            return str_ireplace('talk', 'thumb_talk', $imageurl);
        }
    } else if ("l" == $w) {
        return $imageurl;
    }
}

function i_icon_check($uid, $iconUrl, $size = "m")
{
    $baseUrl = image_storage_url()."/";
    if (!empty($iconUrl)) {
        if ($size == "l") {
            $iconPath = $baseUrl . "useralbum/" . $uid . "/" . $iconUrl . ".jpg";
        } else if ($size == "m") {
            $iconPath = $baseUrl . "useralbum/" . $uid . "/" . $iconUrl . "_" . $size . ".jpg";
        } else if ($size == "s") {
            $iconPath = $baseUrl . "useralbum/" . $uid . "/" . $iconUrl . "_" . $size . ".jpg";
        }
        return $iconPath;
    } else {
        return __ROOT__ . "/Public/image/common/0.jpg";
    }
}

/**
 * paging
 */

function i_page_get_num()
{
    if (!empty($_GET['p'])) {
        if (preg_match("/[0-9]/", $_GET['p']) && $_GET['p'] > 0) {
            $page = $_GET['p'] - 1;
        } else {
            $page = 0;
        }
    } else {
        $page = 0;
    }
    return $page;
}

/**
 *
 * http://www.ihelpoo.com/ + string + ?p=1
 * @param string $urlstring
 * @param int $total
 */
function i_page_show($urlstring, $total, $connectionMode = "?")
{
    if (!empty($_GET['p']) && $_GET['p'] >= '11') {
        $i = (ceil($_GET['p'] / 10) - 1) * 10;
        $totalturn = ceil($total / 10) * 10;
        $nowturn = ceil($_GET['p'] / 10) * 10;
        if ($nowturn < $totalturn) {
            $turn = $nowturn;
        } else {
            $turn = $total;
        }
        while ($i <= $turn) {
            if (!empty($_GET['p']) && $_GET['p'] == $i || !isset($_GET['p']) && 1 == $i) {
                echo "<a href='" . __ROOT__ . "/" . $urlstring . $connectionMode . "p=" . $i . "' class='s_l_l_a_select'>" . $i . "</a> ";
            } else {
                echo "<a href='" . __ROOT__ . "/" . $urlstring . $connectionMode . "p=" . $i . "' class='s_l_l_a'>" . $i . "</a> ";
            }
            $i++;
        }
        if ($nowturn < $totalturn) {
            echo "<a href='" . __ROOT__ . "/" . $urlstring . $connectionMode . "p=" . $i . "' class='s_l_l_a' title='more'>...</a> ";
        }
    } else {
        $i = 1;
        if ($total > 10) {
            while ($i <= 10) {
                if (!empty($_GET['p']) && $_GET['p'] == $i || !isset($_GET['p']) && 1 == $i) {
                    echo "<a href='" . __ROOT__ . "/" . $urlstring . $connectionMode . "p=" . $i . "' class='s_l_l_a_select'>" . $i . "</a> ";
                } else {
                    echo "<a href='" . __ROOT__ . "/" . $urlstring . $connectionMode . "p=" . $i . "' class='s_l_l_a'>" . $i . "</a> ";
                }
                $i++;
            }
            if ($total > 10) {
                echo "<a href='" . __ROOT__ . "/" . $urlstring . $connectionMode . "p=" . $i . "' class='s_l_l_a' title='more'>...</a> ";
            }
        } else {
            while ($i <= $total) {
                if (!empty($_GET['p']) && $_GET['p'] == $i || !isset($_GET['p']) && 1 == $i) {
                    echo "<a href='" . __ROOT__ . "/" . $urlstring . $connectionMode . "p=" . $i . "' class='s_l_l_a_select'>" . $i . "</a> ";
                } else {
                    echo "<a href='" . __ROOT__ . "/" . $urlstring . $connectionMode . "p=" . $i . "' class='s_l_l_a'>" . $i . "</a> ";
                }
                $i++;
            }
        }
    }
}

/**
 *
 * Get image url from Storage
 * record field data link 1;2;3 or ihelpooupload10001time().jpg
 * @param string $recordImageField
 * @param char $way o eq original; s eq small
 * @param array $imageRecordArray return
 */
function i_get_image($recordImageField, $way = 'o')
{
    $sotrageUrl = image_storage_url()."/";
    $RecordOutimg = M("RecordOutimg");
    if (preg_match("/;/", $recordImageField)) {
        $sayRecordImageArray = explode(";", $recordImageField);
        foreach ($sayRecordImageArray as $sayRecordImage) {
            if (preg_match("/ihelpooupload/", $sayRecordImage)) {
                $sayRecordImageResult = substr("$sayRecordImage", 13);
                $imageRecordArray[] = $sotrageUrl . "upload" . $sayRecordImageResult;
            } else {
                $recordOutimg = $RecordOutimg->where("id = $sayRecordImage")->find();
                if ("o" == $way) {
                    $imageRecordArray[] = $recordOutimg['rpath'];
                } else if ("s" == $way) {
                    if (preg_match("/upaiyun/", $recordOutimg['rpath'])) {
                        $imageRecordArray[] = preg_replace("/recordsay/", "thumb_recordsay", $recordOutimg['rpath']);
                    } else {
                        $imageRecordArray[] = $recordOutimg['rpath'];
                    }
                }
            }
        }
    } else {
        if ($recordImageField != "yes") {
            if (preg_match("/ihelpooupload/", $recordImageField)) {
                $sayRecordImageResult = substr($recordImageField, 13);
                $imageRecordArray[] = $sotrageUrl . "upload" . $sayRecordImageResult;
            } else {
                $recordOutimg = $RecordOutimg->where("id = $recordImageField")->find();
                if ("o" == $way) {
                    $imageRecordArray[] = $recordOutimg['rpath'];
                } else if ("s" == $way) {
                    if (preg_match("/upaiyun/", $recordOutimg['rpath'])) {
                        $imageRecordArray[] = preg_replace("/recordsay/", "thumb_recordsay", $recordOutimg['rpath']);
                    } else {
                        $imageRecordArray[] = $recordOutimg['rpath'];
                    }
                }
            }
        }
    }
    return $imageRecordArray;
}

function i_makechickableLinks($text)
{
    $text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_+.~#?&//=]+)', '<a href="\1" title="外部网页链接" target="_blank"><span class="post_link"></span></a>', $text);
    $text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_+.~#?&//=]+)', '\1<a href="http://\2" title="外部网页链接" target="_blank"><span class="post_link"></span></a>', $text);
    //$text = eregi_replace('([_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3})', '<a href="mailto:\1">\1</a>', $text);
    return $text;
}

/**
 * album degree configure
 * 1~3 0.5GB
 * 4~6 1GB
 * 7~8 4GB
 * 9>  16GB
 */
function i_configure_album_size($userLevel)
{
    if ($userLevel <= 5) {
        return $totalAlbumDefaultSize = 0.5 * 1073741824;
    } else if ($userLevel <= 6) {
        return $totalAlbumDefaultSize = 1 * 1073741824;
    } else if ($userLevel <= 7) {
        return $totalAlbumDefaultSize = 4 * 1073741824;
    } else if ($userLevel <= 10) {
        return $totalAlbumDefaultSize = 16 * 1073741824;
    }
}

/**
 * check is email
 */
function i_check_email($mailaddr)
{
	if (!ereg("^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*$", $mailaddr)) {
		return false;
	} else {
		return true;
	}
}


/**
 * Queue the mails to memory, they'll get opportunity to be sent every ten seconds
 *
 * @param $to
 * @param $subject
 * @param $body
 */
function i_send($to, $subject, $body)
{
    $redis = new Redis();
    $redis->pconnect(C('REDIS_HOST'), C('REDIS_PORT'));

    $redis->incr(C('MAIL_COUNTER'));
    $redis->lPush(C('MAIL_QUEUE_TO'), $to);
    $redis->lPush(C('MAIL_QUEUE_SUBJECT'), $subject);
    $redis->lPush(C('MAIL_QUEUE_BODY'), $body);
}


/**
 * @deprecated use i_send instead
 */
function i_sendmail($address, $title, $message)
{

    vendor('Ihelpoo.Phpmailer');

    $mail = new PHPMailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();

    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet = 'UTF-8';

    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address);

    // 设置邮件正文
    $tpl = '<div style="font-family:verdana;font-size:12px;color:#555555;line-height:14pt"> <div style="width:590px"> <div style="background:url(\'http://42.62.50.238/Public/image/common/email_top.png\') no-repeat;width:100%;min-height:75px;display:block"> <div style="padding-top:30px;padding-left:50px;padding-right:50px"><a target="_blank" href="http://www.ihelpoo.com"><img style="border:none" src="http://42.62.50.238/Public/image/common/logo.jpg"></a> </div> </div> <div style="background:url(\'http://www.gstatic.com/android/market_images/email/email_mid.png\') repeat-y;width:100%;display:block"> <div style="padding-left:50px;padding-right:50px;padding-bottom:1px"> <div style="border-bottom:1px solid #ededed"></div> <div style="border-bottom:1px solid #ededed"></div> <div style="margin:30px 0">' . $message . ' </div> <div style="border-bottom:1px solid #ededed"></div> <div style="border-bottom:1px solid #ededed"></div> <div style="font-size:9px;color:#B0B0B0">需要帮助吗？请访问 <a target="_blank" href="http://www.ihelpoo.com">我帮圈圈官网</a>。<br>请勿回复此邮件。<br>&copy; 2013 我帮圈圈 | 保留所有权利。<br>我帮圈圈团队 ♥ </div> </div> </div> <div style="background:url(\'http://42.62.50.238/Public/image/common/email_bottom.png\') no-repeat;width:100%;min-height:50px;display:block" class="adL"> <div style="padding-left:50px;padding-right:50px"></div> </div> </div></div>';
    $mail->Body = $tpl;
    $mail->IsHTML(true);

    // 设置邮件头的From字段。
    $mail->From = C('MAIL_ADDRESS');

    // 设置发件人名字
    $mail->FromName = '我帮圈圈';

    // 设置邮件标题
    $mail->Subject = $title;

    // 设置SMTP服务器。
    $mail->Host = C('MAIL_SMTP');

    // 设置为"需要验证"
    $mail->SMTPAuth = true;

    // 设置用户名和密码。
    $mail->Username = C('MAIL_LOGINNAME');
    $mail->Password = C('MAIL_PASSWORD');

    // 发送邮件。
    return ($mail->Send());
}


/**
 *
 * use this to store system notice message that triggered by some user
 *
 * NOTICE: we should add another jquery event at the trigger page for bouncing the receivers ALERT in case he/she is online.
 *
 * @param $from
 * @param $to
 * @param $noticeType
 * @param $detailId
 * @return system notice message id
 */
function i_savenotice($from, $to, $noticeType, $detailId)
{
    Vendor('Ihelpoo.Idworker');
    $idworker = new Idworker();
    $noticeId = $idworker->next();
    $hs = new HandlerSocket(C('MYSQL_MASTER'), C('HS_PORT_WR'));
    if (!($hs->openIndex(3, C('OO_DBNAME'), C('H_I_MSG_NOTICE'), '', 'notice_id,notice_type,source_id,detail_id,format_id,create_time'))) {
        echo 'ERROR1:' . $hs->getError(), PHP_EOL;
        die();
    }

    if ($hs->executeInsert(3, array($noticeId, $noticeType, $from, $detailId, $noticeType, time())) === false) {
        echo 'ERROR2:' . $hs->getError(), PHP_EOL;
    }
    unset($hs);

    $redis = new Redis();
    $redis->pconnect(C('REDIS_HOST'), C('REDIS_PORT'));
    $redis->hSet(C('R_ACCOUNT') . C('R_MESSAGE') . $to, $noticeId, 0);
    $redis->hIncrBy(C('R_NOTICE') . C('R_SYSTEM') . substr($to, 0, strlen($to) - 3), substr($to, -3), 1);
    return $noticeId;
}

?>