<?php

$ooconfig = require 'config_oo.php';

$config = array(
    //'配置项'=>'配置值'
    'APP_DEBUG'=>false,
    'SHOW_PAGE_TRACE'=>false,
	'URL_CASE_INSENSITIVE'=>false,
    'URL_HTML_SUFFIX'=>'.html',
    'DB_PREFIX'=>'i_',
//	'TMPL_EXCEPTION_FILE'=>'./App/Tpl/Public/error.html',

	'MAIL_ADDRESS'=>'info@ihelpoo.com', //email
	'MAIL_SMTP'=>'smtp.exmail.qq.com',
	'MAIL_LOGINNAME'=>'info@ihelpoo.com',
	'MAIL_PASSWORD'=>'help2012',
);

return array_merge($config,$ooconfig);

?>
