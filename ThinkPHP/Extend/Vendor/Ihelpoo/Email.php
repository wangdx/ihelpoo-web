<?php
class Email
{
    private $host;
    private $username;
    private $password;
    private $ihelpoo;

    public function __construct()
    {
        $this->host = 'smtp.exmail.qq.com';
        $this->from = 'info@ihelpoo.com';
        $this->username = 'info@ihelpoo.com';
        $this->password = 'help2012';
        $this->ihelpoo = '42.62.50.238';
    }

    public function sendDefault($toEmail, $toSubject, $toContent, $toNickname = NULL)
    {
        try{
//            $mail = new SaeMail();
//            $options = array(
//	            from => $this->from,
//	            to => $toEmail,
//	            smtp_host => $this->host,
//	            smtp_port => 25,
//	            smtp_username => $this->username,
//	            smtp_password => $this->password,
//	            subject => $toSubject,
//	            content => $toContent,
//	            content_type => 'HTML'
//            );
//            $mail->setOpt($options);
//            $mail->send();
            i_send($toEmail, $toSubject, $toContent);
        } catch (Exception $e){
            $info = $e->getMessage();
            return FALSE;
        }
    }

    /**
     *
     * Sending Multiple Mails per SMTP Connection, should use mailconnect first
     * @param object $transport
     * @param char $toEmail
     * @param char $toNickname
     */
    public function invite($toEmail, $toNickname = NULL)
    {
        $toSubject = "我帮圈圈帮助主题社交网站 - 期待您的加入";
        $toContent = "<p>亲！童鞋 <span style='font-size:12px; color:gray'>(或是由于我们收集数据错误, 导致这封email来到您邮箱的网上的朋友)</span></p>
                      <p>我们是校园帮助主题社交网站 我帮圈圈 网站的开发人员。 经过近一年的摸索, 几个版本的更替，酷暑寒冬中的挥汗...我帮圈圈第三版本上线了!</p>
                      <p>在此诚邀您关注, 期待您的<a href='http://".$this->ihelpoo."/'>加入...</a></p>
                      <p style='color:gray; font-size:12px'>
                      希望大家一起能帮您解决一些问题...<br />
                      希望您找到需要的讯息...<br />
                      希望您找到幸福...<br />
                      Good luck! :)
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      天天开心:D 祝好
                      </p>
                      ";
        try {
//            $mail = new SaeMail();
//            $options = array(
//	            from => $this->from,
//	            to => $toEmail,
//	            smtp_host => $this->host,
//	            smtp_port => 25,
//	            smtp_username => $this->username,
//	            smtp_password => $this->password,
//	            subject => $toSubject,
//	            content => $toContent,
//	            content_type => 'HTML'
//            );
//            $mail->setOpt($options);
//            $mail->send();
            i_send($toEmail, $toSubject, $toContent);
        } catch (Exception $e){
            $info = $e->getMessage();
            return FALSE;
        }
        return TRUE;
    }

    public function longtimeNotlogin($toEmail, $toNickname, $messageNums)
    {
        $toSubject = "童鞋您有新消息啦 - 我帮圈圈";
        $toContent = "<p>".$toNickname." 很久没来我帮圈圈啦!</p>
                      <p>您有 <span style='color:red'>".$messageNums."</span> 条未读新消息 <a href='http://".$this->ihelpoo."'>快来看看吧!</a>
                      <br/>
                      <span style='font-size:12px; color:gray'>有人向您求助, 或是有人想知道您的真实联系方式, 或是您又有了新评论和回复...</span>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        try{
//            $mail = new SaeMail();
//            $options = array(
//	            from => $this->from,
//	            to => $toEmail,
//	            smtp_host => $this->host,
//	            smtp_port => 25,
//	            smtp_username => $this->username,
//	            smtp_password => $this->password,
//	            subject => $toSubject,
//	            content => $toContent,
//	            content_type => 'HTML'
//            );
//            $mail->setOpt($options);
//            $mail->send();
            i_send($toEmail, $toSubject, $toContent);
        } catch (Exception $e){
        	$info = $e->getMessage();
        	return FALSE;
        }
        return TRUE;
    }

    public function welcome($toEmail, $toNickname)
    {
        $toSubject = "欢迎加入我帮圈圈";
        $toContent = "<p>欢迎加入我帮圈圈 - 校园帮助主题社交网站。</p>
                      <p style='font-size:12px'>
                      在这里~ 大家一起能帮您解决一些问题...<br />
                      在这里~ 您能找到需要的讯息...<br />
                      在这里~ 希望您能找到幸福...<br />
                      Good luck! :)
                      </p>
                      <p><a href='http://".$this->ihelpoo."/'>开始您的故事吧!</a></p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function emailAffirm($uid, $uhash, $toEmail, $toNickname)
    {
        $toSubject = "邮箱验证 - 我帮圈圈";
        $toContent = "<p>
                      <a href='http://".$this->ihelpoo."/user/emailaffirm/u/".$uid."/h/".$uhash."'>点击这里</a> 验证您的邮箱.<br />
                      (邮箱验证后才能帮助您快速找回密码, 给".$toNickname."提供一些必要的消息...总之很重要, 希望您火速验证)</p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function resetpassword($newpassword, $toEmail, $toNickname)
    {
        $toSubject = "密码已经重置 - 我帮圈圈";
        $toContent = "<p>您的新密码已经初始化为: <strong>".$newpassword."</strong></p>
                      <p>请尽快登录修改密码 <a href='http://".$this->ihelpoo."/setting/password'>点这里</a>
                      <br />
                      <span style='font-size:12px; color:gray'>提示: 我帮圈圈的密码不区分大小写</span>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function resetpasswordask($toEmail, $toNickname, $toUid, $toUhash)
    {
        $toSubject = "找回密码 - 我帮圈圈";
        $toContent = "<p>您将要找回密码:</p>
                      <p>请点击这条确认连接 <a href='http://".$this->ihelpoo."/user/resetpwsure/".$toUid."/".$toUhash."'>点这里</a>
                      <br />
                      <span style='font-size:12px; color:gray'>提示: 我帮圈圈的密码不区分大小写</span>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function realNameModifiedAllow($toEmail, $toNickname)
    {
        $toSubject = "改变真实姓名回复 - 我帮圈圈";
        $toContent = "<p>我帮圈圈对您的资料进行了查看, 对您在社区中因为公布了自己的真实姓名而体悟到的不安全感 感同身受。对我们的工作没有做到位表示歉意。</p>
                      <p>请您 <a href='http://".$this->ihelpoo."/setting/realfirst?step=1'>点击这里</a> 修改您的真实姓名吧。</p>
                      <p>我帮圈圈是针对民院的社交平台, 这里都是校友, 我们也会做好隐私保护工作。希望您能提供真实姓名</p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

	public function helpstatusNeed($toEmail, $toNickname, $nickname, $content)
    {
        $toSubject = $nickname."有困难了需要您的帮助，快来看看吧 - 我帮圈圈";
        $toContent = "<p>".$toNickname."童鞋  <br />您的朋友 ".$nickname." 有困难了，需要您的帮助 help~ </p>
                      <p style='font-size:12px'>
                      ".$content."
                      <a href='http://".$this->ihelpoo."/'>快去看看吧!</a>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function helpstatusNew($toEmail, $toNickname)
    {
        $toSubject = "有人来帮助您啦 快来看看吧 - 我帮圈圈";
        $toContent = "<p>".$toNickname." 童鞋  <br />您的求助有了新回复, 有童鞋来帮助您啦! 看能对您有什么促进不? <a href='http://".$this->ihelpoo."/'>快来看看吧!</a></p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function helpstatusEnd($hid, $toEmail, $toNickname)
    {
        $toSubject = "您的求助要到期了 - 我帮圈圈";
        $toContent = "<p>".$toNickname."童鞋  <br />您的求助要到期了, 看看有什么对您有点帮助的，选个童鞋进行回馈吧。</p>
                     <p><a href='http://".$this->ihelpoo."/stream/ih/".$hid."'>马上去看看</a></p>
                     <p><span style='color:gray; font-size:12px'>如果没能帮到您什么 我帮圈圈表示歉意, 希望下次能为您做些什么。超时的帮助将会自动关闭，谢谢支持 :)</span></p>
                     <br />
                     <p style='color:gray; font-size:12px; font-style:italic;'>
                     校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                     <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                     ".$toNickname."天天开心:D 祝好
                     </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

	public function groupMessagePush($toEmail, $toNickname, $guoupName, $pushContent)
    {
        $toSubject = $guoupName." 组织, 有新消息通知您 - 我帮圈圈";
        $toContent = "<p>".$toNickname."童鞋  <br />您加入的我帮圈圈中的 ".$guoupName." 组织，有新的消息通知您!</p>
                      <p style='font-size:12px'>
                      ".$pushContent."
                      <a href='http://".$this->ihelpoo."/'>快来看看吧!</a>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function activityNotice($toEmail, $toNickname, $notiveContent, $activityId, $activitySubject)
    {
        $toSubject = "您参加的 “".$activitySubject."” 活动 有新消息通知您 - 我帮圈圈";
        $toContent = "<p>".$toNickname."童鞋  <br />您参加的我帮圈圈中的 “".$activitySubject."” 活动 ，主办方有新的消息通知您!</p>
                      <p style='font-size:12px'>
                      ".$notiveContent."
                      <a href='http://".$this->ihelpoo."/activity/item/".$activityId."'>详情!</a>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function activityVerify($toEmail, $toNickname, $verifyContent, $activityId, $activitySubject)
    {
        $toSubject = "关于您组织的 “".$activitySubject."” 活动 审核情况回复 - 我帮圈圈";
        $toContent = "<p>".$toNickname."童鞋  <br />您组织的 “".$activitySubject."” 活动 ，审核进度有新的消息通知您!</p>
                      <p style='font-size:12px'>
                      ".$verifyContent."
                      <a href='http://".$this->ihelpoo."/activity/item/".$activityId."'>详情!</a>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function mallInfo($toEmail, $toNickname, $infoContent)
    {
        $toSubject = "您有新交易消息 - 我帮圈圈逛街";
        $toContent = "<p>".$toNickname."童鞋  <br />您有新的交易消息!</p>
                      <p style='font-size:12px'>
                      ".$infoContent."
                      <a href='http://".$this->ihelpoo."'>来看看!</a>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    public function mallNotice($toEmail, $toNickname, $infoContent)
    {
        $toSubject = "邀请公告 - 我帮圈圈";
        $toContent = "<p>".$toNickname."童鞋  <br />您有新的我帮圈圈邀请公告!</p>
                      <p style='font-size:12px'>
                      ".$infoContent."
                      <a href='http://".$this->ihelpoo."'>来看看!</a>
                      </p>
                      <br />
                      <p style='color:gray; font-size:12px; font-style:italic;'>
                      校园帮助主题社交网站 - <a href='http://".$this->ihelpoo."/'>我帮圈圈</a>敬上!
                      <a href='http://www.weibo.com/ihelpoo' style='font-size:10px; color:gray'>(新浪微博)</a><br />
                      ".$toNickname."天天开心:D 祝好
                      </p>";
        $this->sendDefault($toEmail, $toSubject, $toContent, $toNickname);
        return TRUE;
    }

    /**
     *
     * other necessary email type
     * such as
     * 1.delete Upper limit & verification
     * 2.destory account
     * ...
     *
     */

    public function getHost()
    {
        return $this->host;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
}