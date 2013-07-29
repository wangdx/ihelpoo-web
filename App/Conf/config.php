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
);

return array_merge($config,$ooconfig);

?>
