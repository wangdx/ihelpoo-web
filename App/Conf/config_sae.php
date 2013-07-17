<?php
return array(
   'SMS_ON'=>false,
   'SMS_MOBILE'=>'',//填写你的手机号

   'SPARE_DB_HOST'=>'',//填写备用数据库地址
   'SPARE_DB_NAME'=>'',//填写备用数据库名
   'SPARE_DB_USER'=>'',//填写备用数据库用户名
   'SPARE_DB_PWD'=>'',//填写备用数据库密码
   'SPARE_DB_PORT'=>'',//填写备用数据库端口
   'SPARE_DB_DEBUG'=>false,//是否开启备用数据库调试

   'UPGRADE_NOTICE_ON'=>false,//开启短信升级提醒功能
   'UPGRADE_NOTICE_DEBUG'=>false,//调试默认，设置为true后UPGRADE_NOTICE_CHECK_INTERVAL配置项不会起作用，每次都会检测，调试完毕后，请设置此配置项为false
   'UPGRADE_NOTICE_MOBILE'=>'136123456789',//接受短信的手机号
   'UPGRADE_NOTICE_CHECK_INTERVAL' => 604800,//检测频率,单位秒,默认是一周
   'UPGRADE_CURRENT_VERSION'=>'3.1',//升级后的版本号，会在短信中告诉你填写什么

   'TMPL_PARSE_STRING'=>array(
   '/Public/upload'=>sae_storage_root('Public').'/upload'
    )
);