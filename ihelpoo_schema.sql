-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: w.rdc.sae.sina.com.cn:3307
-- Generation Time: Jul 14, 2013 at 03:51 PM
-- Server version: 5.5.23
-- PHP Version: 5.3.3

use ihelpoo;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `app_ihelpoo`
--

-- --------------------------------------------------------

--
-- Table structure for table `i_activity_appendix`
--

CREATE TABLE IF NOT EXISTS `i_activity_appendix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_activity_comment`
--

CREATE TABLE IF NOT EXISTS `i_activity_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `good` int(11) DEFAULT NULL,
  `bad` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_activity_item`
--

CREATE TABLE IF NOT EXISTS `i_activity_item` (
  `aid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'activity id',
  `status` char(1) DEFAULT NULL COMMENT '0 for not handle; 1 for yes; 2 dor no',
  `run_type` char(1) NOT NULL COMMENT '1 for normal; 2 for parter',
  `subject` varchar(50) DEFAULT NULL,
  `sponsor_uid` int(11) DEFAULT NULL,
  `activity_ti` int(11) DEFAULT NULL,
  `join_num` int(11) DEFAULT NULL,
  `hit` int(11) DEFAULT NULL,
  `content` text,
  `good_nu` int(11) DEFAULT NULL,
  `bad_nu` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_activity_lotterydraw`
--

CREATE TABLE IF NOT EXISTS `i_activity_lotterydraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `userids` varchar(255) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_activity_notice`
--

CREATE TABLE IF NOT EXISTS `i_activity_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_activity_user`
--

CREATE TABLE IF NOT EXISTS `i_activity_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `partner_uid` int(11) DEFAULT NULL,
  `invite_status` char(1) DEFAULT NULL COMMENT '1 for invite; 2 for has parter; 0 for not handle',
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=390 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_activity_userinvite`
--

CREATE TABLE IF NOT EXISTS `i_activity_userinvite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `invite_uid` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_admin_realnamemf`
--

CREATE TABLE IF NOT EXISTS `i_admin_realnamemf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `allow` varchar(1) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=278 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_admin_user`
--

CREATE TABLE IF NOT EXISTS `i_admin_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `priority` int(1) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_admin_userlogin`
--

CREATE TABLE IF NOT EXISTS `i_admin_userlogin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `loginip` char(15) DEFAULT NULL,
  `logintime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_admin_userrecord`
--

CREATE TABLE IF NOT EXISTS `i_admin_userrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `record` varchar(255) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3161 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_au_mail_send`
--

CREATE TABLE IF NOT EXISTS `i_au_mail_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `helperid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL COMMENT '1 eq 到期  2 eq 新帮助',
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3934 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_au_temp_uploadimg`
--

CREATE TABLE IF NOT EXISTS `i_au_temp_uploadimg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2019 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_cms_artical`
--

CREATE TABLE IF NOT EXISTS `i_cms_artical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'admin user id',
  `title` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `time` int(11) NOT NULL,
  `hit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_da_qcode`
--

CREATE TABLE IF NOT EXISTS `i_da_qcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `qcode` int(11) DEFAULT NULL,
  `use` char(1) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_da_students`
--

CREATE TABLE IF NOT EXISTS `i_da_students` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `enteryear` int(11) DEFAULT NULL,
  `birthday` int(11) DEFAULT NULL,
  `sex` int(11) DEFAULT NULL,
  `academy` int(11) DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `province` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `i_mall_cooperation`
--

CREATE TABLE IF NOT EXISTS `i_mall_cooperation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(200) NOT NULL,
  `order` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_mall_indeximg`
--

CREATE TABLE IF NOT EXISTS `i_mall_indeximg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `middle_img` varchar(200) NOT NULL,
  `middle_url` varchar(200) NOT NULL,
  `right_img` varchar(200) NOT NULL,
  `right_url` varchar(200) NOT NULL,
  `center_img` varchar(200) NOT NULL,
  `center_url` varchar(200) NOT NULL,
  `order` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_msg_active`
--

CREATE TABLE IF NOT EXISTS `i_msg_active` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `change` int(11) NOT NULL,
  `way` varchar(3) NOT NULL COMMENT 'add, min;',
  `reason` varchar(50) NOT NULL,
  `time` int(11) NOT NULL,
  `deliver` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111295 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_msg_at`
--

CREATE TABLE IF NOT EXISTS `i_msg_at` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `touid` int(11) DEFAULT NULL,
  `fromuid` int(11) NOT NULL DEFAULT '0',
  `sid` int(11) DEFAULT NULL,
  `cid` int(11) DEFAULT NULL COMMENT 'comment id',
  `hid` int(11) DEFAULT NULL COMMENT 'helpreply id',
  `aid` int(11) DEFAULT NULL COMMENT 'commodityassess id ',
  `time` int(10) DEFAULT NULL,
  `deliver` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=941 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_msg_comment`
--

CREATE TABLE IF NOT EXISTS `i_msg_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  `cid` int(11) DEFAULT NULL,
  `ncid` int(11) DEFAULT NULL COMMENT 'now cid',
  `rid` int(11) DEFAULT NULL COMMENT 'who reply id',
  `time` int(10) DEFAULT NULL,
  `deliver` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=153515 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_msg_system`
--

CREATE TABLE IF NOT EXISTS `i_msg_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `url_id` int(11) DEFAULT NULL,
  `from_uid` int(11) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  `deliver` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=171927 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_op_academy`
--

CREATE TABLE IF NOT EXISTS `i_op_academy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `number` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_op_city`
--

CREATE TABLE IF NOT EXISTS `i_op_city` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `prov_id` int(10) unsigned NOT NULL,
  `idcode` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `i_op_dormitory`
--

CREATE TABLE IF NOT EXISTS `i_op_dormitory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL,
  `type` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_op_province`
--

CREATE TABLE IF NOT EXISTS `i_op_province` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `type` int(1) DEFAULT NULL COMMENT '1 - 直辖市2 - 行政省3 - 自治区4 - 特别行政区5 - 其他国家见全局数据字典[省份类型] ',
  `idcode` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `i_op_specialty`
--

CREATE TABLE IF NOT EXISTS `i_op_specialty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `number` varchar(10) DEFAULT NULL,
  `academy` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_comment`
--

CREATE TABLE IF NOT EXISTS `i_record_comment` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  `toid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `diffusion_co` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=157198 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_commodity`
--

CREATE TABLE IF NOT EXISTS `i_record_commodity` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `shopid` int(11) DEFAULT NULL COMMENT 'shopid = uid',
  `name` varchar(30) DEFAULT NULL,
  `price` varchar(15) DEFAULT NULL,
  `good_nums` int(11) DEFAULT NULL,
  `good_type` char(1) DEFAULT NULL COMMENT '1 for new; 2 for second hand',
  `rebate` int(11) NOT NULL COMMENT 'default 5 + % biger than this',
  `buyway` varchar(15) NOT NULL COMMENT '1 eq 免费送货上门;2 eq +1元送货上门; 3 eq 店里来取;',
  `detail` text COMMENT 'good detail',
  `image` varchar(255) DEFAULT NULL,
  `sales_co` int(11) DEFAULT NULL,
  `assess_co` int(11) DEFAULT NULL,
  `hit` int(11) NOT NULL,
  `time` int(10) DEFAULT NULL,
  `update_ti` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` char(50) DEFAULT NULL,
  `close_reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=325 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_commodityassess`
--

CREATE TABLE IF NOT EXISTS `i_record_commodityassess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `cid` int(11) DEFAULT NULL COMMENT 'commodity id',
  `anonymous` char(1) DEFAULT NULL COMMENT '1 for yes',
  `buynums` int(11) NOT NULL,
  `buyprice` varchar(15) NOT NULL,
  `usecoins` varchar(15) DEFAULT NULL,
  `buyway` varchar(2) DEFAULT NULL,
  `buyaddressid` int(11) NOT NULL COMMENT 'forengin key',
  `remarks` varchar(255) DEFAULT NULL,
  `type` char(1) DEFAULT NULL COMMENT '1 for good; 2 for middle; 3 for bad',
  `score` int(2) DEFAULT NULL COMMENT '5 star 10;',
  `content` varchar(255) DEFAULT NULL,
  `diffusion_co` int(11) DEFAULT NULL,
  `assess_ti` int(10) DEFAULT NULL,
  `status` char(1) DEFAULT NULL COMMENT '1 for on commodity; 2 for finish commodity;3 for finish assess ',
  `refusereason` varchar(255) NOT NULL,
  `start_ti` int(10) DEFAULT NULL,
  `end_ti` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_commoditycategory`
--

CREATE TABLE IF NOT EXISTS `i_record_commoditycategory` (
  `cate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) unsigned NOT NULL DEFAULT '0',
  `cate_name` varchar(100) NOT NULL DEFAULT '',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`cate_id`),
  KEY `store_id` (`shop_id`,`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_diffusion`
--

CREATE TABLE IF NOT EXISTS `i_record_diffusion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `helpreply_id` int(11) DEFAULT NULL,
  `assess_id` int(11) DEFAULT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5797 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_dynamic`
--

CREATE TABLE IF NOT EXISTS `i_record_dynamic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT 'quan; change icon; shoping; honor; help winer; join; new goods',
  `url_id` int(11) DEFAULT NULL,
  `about_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5224 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_favourites`
--

CREATE TABLE IF NOT EXISTS `i_record_favourites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=135 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_help`
--

CREATE TABLE IF NOT EXISTS `i_record_help` (
  `hid` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL,
  `reward_coins` int(11) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `win_uid` int(11) DEFAULT NULL,
  `thanks` varchar(255) DEFAULT NULL,
  `thanks_ti` int(10) DEFAULT NULL,
  PRIMARY KEY (`hid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=933 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_helpreply`
--

CREATE TABLE IF NOT EXISTS `i_record_helpreply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  `toid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `diffusion_co` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9398 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_outimg`
--

CREATE TABLE IF NOT EXISTS `i_record_outimg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `rpath` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'real path',
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3388 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_record_say`
--

CREATE TABLE IF NOT EXISTS `i_record_say` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `say_type` char(1) DEFAULT NULL COMMENT '0 for default; 1 for help; 2 for dynamic; 3 for commodity; 4 for notice; 9 for quietlyreleased',
  `content` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `authority` char(1) DEFAULT NULL,
  `comment_co` int(11) DEFAULT NULL,
  `diffusion_co` int(11) DEFAULT NULL,
  `hit_co` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  `from` varchar(50) DEFAULT NULL COMMENT '来源  sourse , 1 for web, 2 for spider, 3 for Andtiod ',
  `last_comment_ti` int(10) DEFAULT NULL COMMENT '最后一条评论的时间',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33086 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_sys_parameter`
--

CREATE TABLE IF NOT EXISTS `i_sys_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parameter` varchar(50) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_talk_content`
--

CREATE TABLE IF NOT EXISTS `i_talk_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `touid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `deliver` varchar(1) DEFAULT NULL,
  `del` int(11) DEFAULT NULL COMMENT 'who delete this records',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29740 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_talk_inputstatus`
--

CREATE TABLE IF NOT EXISTS `i_talk_inputstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `touid` int(11) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL COMMENT 'if equal 1 means input',
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7631 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_talk_list`
--

CREATE TABLE IF NOT EXISTS `i_talk_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `listuid` int(11) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1476 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_album`
--

CREATE TABLE IF NOT EXISTS `i_user_album` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` char(1) DEFAULT NULL COMMENT '1 for icon;2 for image; ',
  `foreignid` int(11) NOT NULL COMMENT 'foreign key id',
  `url` varchar(200) DEFAULT NULL,
  `size` int(11) NOT NULL,
  `hit` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8044 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_changeinfo`
--

CREATE TABLE IF NOT EXISTS `i_user_changeinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `withid` int(11) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1737 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_coins`
--

CREATE TABLE IF NOT EXISTS `i_user_coins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `total` varchar(15) DEFAULT NULL,
  `use` varchar(15) DEFAULT NULL COMMENT 'used coins',
  `way` char(3) DEFAULT NULL COMMENT 'add, min;',
  `reason` varchar(50) DEFAULT NULL,
  `hash` char(32) DEFAULT NULL,
  `status` char(1) DEFAULT NULL COMMENT '1 for ok;2 for wrong;',
  `check_ti` int(10) DEFAULT NULL COMMENT 'check coins per day / time;',
  `time` int(10) DEFAULT NULL,
  `deliver` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_deliveryaddress`
--

CREATE TABLE IF NOT EXISTS `i_user_deliveryaddress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_use` char(1) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_honor`
--

CREATE TABLE IF NOT EXISTS `i_user_honor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=207 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_info`
--

CREATE TABLE IF NOT EXISTS `i_user_info` (
  `uid` int(11) NOT NULL,
  `introduction` char(255) DEFAULT NULL,
  `introduction_re` int(1) DEFAULT NULL,
  `academy_op` int(2) DEFAULT NULL,
  `specialty_op` int(2) DEFAULT NULL,
  `dormitory_op` int(2) DEFAULT NULL,
  `province_op` int(2) DEFAULT NULL,
  `city_op` int(4) DEFAULT NULL,
  `realname` varchar(15) DEFAULT NULL,
  `realname_re` int(1) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `qq` varchar(15) DEFAULT NULL,
  `weibo` varchar(50) DEFAULT NULL,
  `room` varchar(10) DEFAULT NULL,
  `dynamic` int(11) DEFAULT NULL COMMENT '动态',
  `fans` int(11) DEFAULT NULL,
  `follow` int(11) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_invite`
--

CREATE TABLE IF NOT EXISTS `i_user_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `inviteuid` int(11) DEFAULT NULL COMMENT 'invite who registed',
  `award` char(1) NOT NULL COMMENT 'is award',
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=238 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_login`
--

CREATE TABLE IF NOT EXISTS `i_user_login` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(1) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `nickname` varchar(25) DEFAULT NULL,
  `sex` int(1) DEFAULT NULL,
  `birthday` char(10) DEFAULT NULL,
  `enteryear` char(4) DEFAULT NULL,
  `type` int(1) DEFAULT NULL,
  `priority` int(1) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `logintime` int(10) DEFAULT NULL,
  `lastlogintime` int(10) DEFAULT NULL,
  `creat_ti` int(10) DEFAULT NULL,
  `login_days_co` int(11) DEFAULT NULL,
  `online` char(50) DEFAULT NULL,
  `coins` varchar(15) DEFAULT NULL COMMENT 'rmb',
  `active` int(11) DEFAULT NULL,
  `icon_fl` char(1) DEFAULT NULL COMMENT 'icon flag',
  `icon_url` varchar(20) DEFAULT NULL,
  `skin` char(1) NOT NULL,
  `school` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14327 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_login_mi`
--

CREATE TABLE IF NOT EXISTS `i_user_login_mi` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `statue` int(1) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_login_wb`
--

CREATE TABLE IF NOT EXISTS `i_user_login_wb` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `weibo_uid` varchar(15) DEFAULT NULL,
  `switch` char(1) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_priority`
--

CREATE TABLE IF NOT EXISTS `i_user_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `pid_type` int(1) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL COMMENT 'shield_user_id',
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11966 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_shop`
--

CREATE TABLE IF NOT EXISTS `i_user_shop` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `status` char(1) DEFAULT NULL,
  `shop_type` char(1) NOT NULL COMMENT '1 for student secondhand; 2 for student; 3 for business',
  `category` int(11) DEFAULT NULL,
  `idcard` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `degree` int(2) DEFAULT NULL,
  `commodity_co` int(11) DEFAULT NULL,
  `sales_co` int(11) DEFAULT NULL,
  `revenue_co` varchar(15) DEFAULT NULL,
  `assess_good` int(11) DEFAULT NULL,
  `assess_middle` int(11) DEFAULT NULL,
  `assess_bad` int(11) DEFAULT NULL,
  `theme` int(2) DEFAULT NULL,
  `shop_banner` varchar(255) DEFAULT NULL,
  `imww` varchar(60) DEFAULT NULL,
  `time` int(10) DEFAULT NULL COMMENT 'open shop time',
  `notice_is` char(1) DEFAULT NULL,
  `notive_content` varchar(255) DEFAULT NULL,
  `notive_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_shopcart`
--

CREATE TABLE IF NOT EXISTS `i_user_shopcart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `cid` int(11) DEFAULT NULL COMMENT 'commodity id',
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `i_user_status`
--

CREATE TABLE IF NOT EXISTS `i_user_status` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `acquire_seconds` int(11) NOT NULL,
  `acquire_times` int(11) NOT NULL,
  `last_active_ti` int(11) DEFAULT NULL,
  `total_active_ti` int(11) DEFAULT NULL,
  `active_flag` char(1) DEFAULT NULL,
  `active_s_limit` int(11) DEFAULT NULL COMMENT 'active_record_say_limit if < 3*3 then add active',
  `active_c_limit` int(11) DEFAULT NULL COMMENT 'active_record_comment_limit if < 15*1 then add active',
  `dynamic_flag` char(1) DEFAULT NULL COMMENT 'dynamic authority; 1 for show only to myself',
  `record_limit` int(11) NOT NULL DEFAULT '6' COMMENT 'allow publish record limit nums 6 per 12hours',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `i_web_status`
--

CREATE TABLE IF NOT EXISTS `i_web_status` (
  `parameter` varchar(255) NOT NULL DEFAULT '',
  `valueint` int(255) DEFAULT NULL,
  `valuechar` varchar(255) DEFAULT NULL,
  `explain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`parameter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
