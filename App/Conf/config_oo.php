<?php

$mdbconfig = require 'config_oo_mdb.php';
$ooconfig = array(

    // IHELPOO SYSTEM CONFIG
    'REDIS_HOST'=>'127.0.0.1',
    'REDIS_PORT'=>6379,

    'MYSQL_MASTER'=>'10.6.1.208',
    'MYSQL_SLAVE'=>'10.6.5.68',
    'MYSQL_PORT'=>3306,

    'HS_PORT_R'=>9998,     // handlersocket port read only
    'HS_PORT_WR'=>9999,  // handlersocket port write only

    'OO_DBNAME'=>'ihelpoo',

);

return array_merge($ooconfig, $mdbconfig);
?>