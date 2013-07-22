<?php
//注意，请不要在这里配置SAE的数据库，配置你本地的数据库就可以了。
return array(
    //'配置项'=>'配置值'
    'APP_DEBUG'=>true,
    'SHOW_PAGE_TRACE'=>true,
	'URL_CASE_INSENSITIVE'=>true,
    'URL_HTML_SUFFIX'=>'.html',
    'DB_PREFIX'=>'i_',

    // IHELPOO SYSTEM CONFIG
    'REDIS_HOST'=>'127.0.0.1',
    'REDIS_PORT'=>6379,

    'MYSQL_MASTER'=>'10.6.1.208',
    'MYSQL_SLAVE'=>'10.6.5.68',
    'MYSQL_PORT'=>3306,

    'HS_PROT'=>9998,     // handlersocket port
    'HS_PORT_WR'=>9999,  // handlersocket port wr

//	'TMPL_EXCEPTION_FILE'=>'./App/Tpl/Public/error.html',
);
?>
