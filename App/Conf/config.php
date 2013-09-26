<?php

$ooconfig = require 'config_oo.php';

$config = array(
    //'配置项'=>'配置值'
    'APP_DEBUG'=>true,
    'SHOW_PAGE_TRACE'=>true,
	'URL_CASE_INSENSITIVE'=>true,
    'URL_HTML_SUFFIX'=>'.html',
    'DB_PREFIX'=>'i_',
	//'TMPL_EXCEPTION_FILE'=>'./App/Tpl/Public/error.html',

	'MAIL_ADDRESS'=>'info@ihelpoo.com', //email
	'MAIL_SMTP'=>'smtp.exmail.qq.com',
	'MAIL_LOGINNAME'=>'info@ihelpoo.com',
	'MAIL_PASSWORD'=>'help2012',

	/**
	 * web config
	 */
	'IS_LOGIN_WEIBO'=>true,
	'IS_LOGIN_QQ'=>true,
	'IS_SEND_MAIL'=>true,
	'VERIFI_RECORD_TIME'=>'8',
	'VERIFI_RECORD_UNMS'=>'3',
	'VERIFI_COMMENT_UNMS'=>'60',
	'VERIFI_COMMENT_TIME'=>'10',
	'IMAGE_STORAGE_URL'=>'http://ihelpoo.b0.upaiyun.com',
);

return array_merge($config,$ooconfig);

?>
